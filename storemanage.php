<?php
  //initiate session with userId and userType
  session_start($options = ["userId", "userType"]);
  include_once "./includes/dbh.inc.php";

  if(!isset($_SESSION["userType"])) {
    $userType = $_SESSION["userType"];
    if($userType == "shopper") {
      header("Location: ../index.php");
    }
  }

  //If userId is empty in the session, redirect back to login
  if(!isset($_SESSION["userId"])) {
    header("Location: ./login.php");
    exit();
  }

  $userId = $_SESSION["userId"];
?>

<!DOCTYPE html>

<html>
<head>
  <link rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
<style>

  .slideShows {display: none;}

  #borderImage {

  border: 30px solid transparent;
  padding: 30px;
  border-image-source: url(images/foodBorder.jpg);
  border-image-slice: 20;
  }

  ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
  overflow: hidden;
  background-color: #333;
  }

  li {
  float: left;
  }

  li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  }

 li a:hover:not(.active) {
 background-color: #209f80;
 }

  <!-- banner font----------------------->
  h1.verdana {
    font face = "verdana";
    font size = 90px;
  }

  p {
    font face = "verdana";
    font size = 50px;
  }

  .store-name{
    border-bottom: 4px solid #ff8f85;
    width: fit-content;
  }

  .new-item-btn{
    border: none;
    background: #ff8f85;
    outline: none !important;
    box-shadow: none;
    border-radius: 15px;
    color: white;
    transition: background-color ease-in-out 0.1s;
    height: 35px;
    padding: 0 10px 0 10px;
  }
  .new-item-btn:hover{
    background-color: #f3645c;
  }

  .new-item-btn:active{
    background-color: #eb413d;
  }

  .delete-btn{
    border: none;
    background: #e60000;
    outline: none !important;
    box-shadow: none;
    border-radius: 15px;
    color: white;
    transition: background-color ease-in-out 0.1s;
    height: 35px;
    padding: 0 10px 0 10px;
  }
  .delete-btn:hover{
    background-color: #ff1a1a;
  }

  .delete-btn:active{
    background-color: #990000;
  }

  .cancel-btn{
    border: none;
    background: #b3b3b3;
    outline: none !important;
    box-shadow: none;
    border-radius: 15px;
    color: white;
    transition: background-color ease-in-out 0.1s;
    height: 35px;
    padding: 0 10px 0 10px;
  }
  .cancel-btn:hover{
    background-color: #cccccc;
  }

  .cancel-btn:active{
    background-color: #737373;
  }

  .store-manage-container{
    margin-left: 3% !important;
  }

  .item-list-wrapper{
    width: 750px;
    transform: translateX(-60px);
  }

  .item-wrapper{
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .item-img{
    height: 200px;
    padding: 10px;
  }

  .item-name {
    width: 150px;
    max-height: 25px;
    text-align: center;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow-x: hidden;
  }

  .name-input {
    width: 250px;
  }

  .desc-input {
    width: 400px;
  }

  .number-input {
    width: 60px;
  }

  .decimal-input {
    width: 100px;
  }

  .check-box {
    transform: translateY(6px);
  }

  /*================================================================================================*/
  /* THIS CODE IS USED FROM https://www.w3schools.com/howto/tryit.asp?filename=tryhow_css_modal
    /* The Modal (background) */
  .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 35px;
    border: 1px solid #888;
    width: 80%;
  }

  /* The Close Button */
  .close {
    color: #aaaaaa;
    align-self: flex-end;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
  /*================================================================================================*/

  li a:hover, li a:active, .active{
  background-color: #209f80;
  }
</style>


</head>

<body>
  <div class="mt-4" style="background-color: #25cd8a; height: 100px; text-align: center; text-shadow: 3px 3px #209f80; pointer-events: none; user-select: none;-moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; border-radius: 25px 25px 0 0;">
    <h1 style="transform: translateY(25px); color: white;">GrocerEase</h1>
  </div>  <ul>
    <li><a class="active" href="storemanage.php">Home</a></li>
    <li><a href="includes/logout.inc.php">Logout</a></li>
  </ul>
 <br><br>
 <div class="container store-manage-container mb-4">
   <h2 class="mb-4 store-name">
     <?php
      //Create the query and turn it to a prepared statement
      $storeIdQuery = "SELECT name FROM grocery_store WHERE store_id = (SELECT store_id FROM store_manager WHERE user_id='" . $userId . "');";
      $resultStore = mysqli_query($conn, $storeIdQuery);
      if(mysqli_num_rows($resultStore) > 0) {
        $row = mysqli_fetch_array($resultStore);
        $storeName = $row["name"];
        echo $storeName;
      }
      else{
        echo "No name";
      }
     ?>
   </h2>
   <div class="row">
     <h3 class="ml-3">Items</h3>
     <button class="ml-4 new-item-btn" onclick="showNewItemModal()" id="modalBtn">New item</button>
   </div>
   <div class="error">
     <?php
        if(isset($_GET["error"])) {
          if($_GET["error"] == "emptyfields") {
            echo "Please fill in required fields.";
          }
          if($_GET["error"] == "sqlerror") {
            echo "Failed connecting to DB.";
          }
          if($_GET["error"] == "failcreatequery") {
            echo "There was a problem creating the query.";
          }
        }
      ?>
   </div>
   <div class="success">
     <?php
         if(isset($_GET["edit"])) {
           if($_GET["edit"] == "success") {
             echo "Item succesfully edited.";
           }
         }
     ?>
   </div>
   <div class="mt-3 row item-list-wrapper">
     <?php
      $allItemsQuery = "SELECT * FROM item WHERE store_id = (SELECT store_id FROM store_manager WHERE user_id='" . $userId . "');";
      $resultItems = mysqli_query($conn, $allItemsQuery);
      if(mysqli_num_rows($resultItems) > 0) {
        while($row = mysqli_fetch_array($resultItems)){
          $itemId = $row["item_id"];
          $itemName = $row["name"];
          $itemImg = $row["image"];
          echo(
            "<div class=\"col-4 item-img\" >".
              "<div class=\"item-wrapper\">".
                "<img src=\"data:image/jpeg;base64,".base64_encode($itemImg)."\" height=\"100px\" width=\"100px\"/>".
                "<div class=\"item-name\">$itemName</div>".
                "<div class=\"row\">".
                  "<button class=\"new-item-btn mt-2 mr-1\" onclick=\"showDeleteModal(this)\" value=\"".$itemId."\">Delete</button>".
                  "<button class=\"new-item-btn mt-2 ml-1\" onclick=\"editItem(this)\" value=\"".$itemId."\">Edit</button>".
                "</div>".
              "</div>".
            "</div>"
          );
        }
      }
     ?>
   </div>
  </div>

  <div id="newItemModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
      <span class="close new-item-close" onclick="hideNewItemModal()">&times;</span>
      <div class="row">
        <h3 class="col-4">New Item</h3>
      </div>
      <form method="POST" action="includes/createitem.php" enctype="multipart/form-data">
        <div class="row mb-2 ml-0">
          <div class="d-flex flex-column">
            <h6>Name*</h6>
            <input class="name-input" type="text" name="name" value="">
          </div>
          <div class="d-flex flex-column ml-3">
            <h6>Image</h6>
            <input class="" type="file" name="upload-img" value="">
          </div>
        </div>
        <div class="d-flex flex-column mb-2">
          <h6>Description</h6>
          <textarea class="desc-input" name="description" value="" rows="4" cols="50"></textarea>
        </div>
        <div class="d-flex flex-column mb-2">
          <h6>Stock*</h6>
          <input class="number-input" type="number" name="stock" value="$itemStock">
        </div>
        <div class="d-flex flex-column mb-2">
          <h6>Price*</h6>
          <input class="decimal-input" type="number" step="0.01" min="0" name="price" value="$itemPrice">
        </div>
        <div class="d-flex flex-column mb-2">
          <h6>Percent Off</h6>
          <div class="row ml-0 mb-2 mr-1">
            <input class="number-input mr-3 disabled" type="number" name="percent-off" max="100" id="percentOff" min="0">
              On Sale
            <input class="ml-1 check-box" type="checkbox" name="on-sale" id="checkBox" onclick="toggleOnsale()">
          </div>
        </div>
        <div class="row ml-0 mt-1 mb-5">
          <input class="new-item-btn" type="submit" name="create-submit" value="Create">
          <button type="button" class="cancel-btn ml-2" onclick="hideNewItemModal()">Cancel</button>
        </div>
      </form>
    </div>

  </div>

  <div id="deleteModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
      <span class="close delete-close" onclick="hideDeleteModal()">&times;</span>
      <h3 class="text-center">Are you sure you want to delete this item?</h3>
      <div class="row justify-content-center">
        <button class="cancel-btn mt-2 mr-1" onclick="hideDeleteModal()">Cancel</button>
        <form action="includes/deleteitemfromshop.inc.php" method="post">
          <input type="submit" class="delete-btn mt-2 ml-1" name="delete-btn" value="Delete">
          <input type="text" class="d-none" name="delete-item-id" id="deleteItemId">
        </form>
      </div>
    </div>

  </div>

  <script>

    var newItemModal = document.getElementById("newItemModal");
    var deleteModal = document.getElementById("deleteModal");

    // When the user clicks the button, open the modal
    function showNewItemModal() {
      newItemModal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    function hideNewItemModal() {
      newItemModal.style.display = "none";
    }

    // When the user clicks the button, open the modal
    function showDeleteModal(elem) {
      document.getElementById("deleteItemId").value = elem.value;
      deleteModal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    function hideDeleteModal() {
      document.getElementById("deleteItemId").value = null;
      deleteModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if ((event.target == newItemModal) || (event.target == deleteModal)) {
        hideNewItemModal();
        hideDeleteModal();
      }
    }

    function editItem(elem) {
      window.location.href = "edititem.php?itemId="+elem.value;
    }
  </script>
</body>
</html>
