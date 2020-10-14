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

  h6 {
    color: #ff8f85;
    font-weight: bold;
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
  }
  .new-item-btn:hover{
    background-color: #f3645c;
  }

  .new-item-btn:active{
    background-color: #eb413d;
  }

  .store-manage-container{
    margin-left: 3% !important;
  }

  .item-img{
    height: 200px;
    padding: 10px;
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

  .disabled {
    opacity: 0.5;
    pointer-events: none;
  }

  .fit-content {
    width: fit-content;
  }

  .max-content {
    width: max-content;
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
    padding: 20px;
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
</style>


</head>

<body>
  <div style="background-color: #25cd8a; height: 100px; text-align: center; text-shadow: 3px 3px #209f80; pointer-events: none; user-select: none;-moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; border-radius: 25px 25px 0 0;">
    <h1 style="transform: translateY(25px); color: white;">GrocerEase</h1>
  </div>
  <ul>
    <li><a class="active" href="storemanage.php">Home</a></li>
    <li><a class="active" href="includes/logout.inc.php">Logout</a></li>
  </ul>
 <br><br>
 <div class="container store-manage-container">
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
     <h3 class="col-4">Edit Item</h3>
   </div>
   <div>
     <?php
      $itemId = $_GET["itemId"];
      $itemQuery = "SELECT * FROM item WHERE item_id=\"$itemId\"";
      $resultItems = mysqli_query($conn, $itemQuery);
      if(mysqli_num_rows($resultItems) > 0) {
        $row = mysqli_fetch_array($resultItems);

        $itemId = $row["item_id"];
        $itemName = $row["name"];
        $itemDesc = $row["description"];
        $itemStock = $row["stock"];
        $itemPrice = $row["price"];
        $itemOnsale = $row["is_on_sale"];
        $itemPercentOff = $row["percent_off"];
        $itemImg = $row["image"];
        echo(
          "<div class=\"col-4 item-img\">".
              "<img class=\"mb-2\" src=\"data:image/jpeg;base64,".base64_encode($itemImg)."\" height=\"200px\" width=\"200px\"/>".
              "<form action=\"includes/edititem.inc.php\" method=\"post\" enctype=\"multipart/form-data\">".
                "<div class=\"row max-content mb-2 ml-0\">".
                  "<div class=\"d-flex flex-column\">".
                    "<h6>Name*</h6>".
                    "<input class=\"d-none\" tpye=\"text\" name=\"item-id\" value=\"$itemId\">".
                    "<input class=\"name-input\" type=\"text\" name=\"edit-name\" value=\"$itemName\">".
                  "</div>".
                  "<div class=\"d-flex flex-column ml-3\">".
                    "<h6>Edit Image</h6>".
                    "<input class=\"fit-content\" type=\"file\" name=\"edit-img\" value=\"\">".
                  "</div>".
                "</div>".
                "<div class=\"d-flex flex-column mb-2\">".
                  "<h6>Description</h6>".
                  "<textarea class=\"desc-input\" name=\"edit-description\" value=\"\" rows=\"4\" cols=\"50\">$itemDesc</textarea>".
                "</div>".
                "<div class=\"d-flex flex-column mb-2\">".
                  "<h6>Stock*</h6>".
                  "<input class=\"number-input\" type=\"number\" name=\"edit-stock\" value=\"$itemStock\">".
                "</div>".
                "<div class=\"d-flex flex-column mb-2\">".
                  "<h6>Price*</h6>".
                  "<input class=\"decimal-input\" type=\"number\" step=\"0.01\" min=\"0\" name=\"edit-price\" value=\"$itemPrice\">".
                "</div>".
                "<div class=\"d-flex flex-column mb-2\">".
                  "<h6>Percent Off</h6>".
                  "<div class=\"row ml-0 mb-2 mr-1\">".
                    "<input class=\"number-input mr-3".($itemOnsale == 1 ? "" : " disabled")."\" type=\"number\" name=\"edit-percent-off\" max=\"100\" id=\"percentOff\" min=\"0\" value=\"".
                    (isset($itemPercentOff) ? $itemPercentOff : "").
                    "\">".
                    "On Sale".
                    "<input class=\"ml-1 check-box\" type=\"checkbox\" name=\"edit-on-sale\" id=\"checkBox\" onclick=\"toggleOnsale()\"".
                    ($itemOnsale == 1 ? "checked" : "").
                    ">".
                  "</div>".
                "</div>".
                "<div class=\"row ml-0 mt-1 mb-5\">".
                  "<input class=\"new-item-btn\" type=\"submit\" name=\"save-submit\">".
                  "<button type=\"button\" class=\"new-item-btn ml-2\" onclick=\"cancelEdit()\">Cancel</button>".
                "</div>".
              "</form>".
          "</div>"
        );
      }
     ?>
   </div>
  </div>
  <script>
    checkBox = document.getElementById("checkBox");

    function toggleOnsale(){
      if(checkBox.checked) {
        document.getElementById("percentOff").className = "number-input mr-3";
        document.getElementById("percentOff").value = 0;
      }
      else {
        document.getElementById("percentOff").value = null;
        document.getElementById("percentOff").className = "number-input mr-3 disabled";
      }
    }

    function cancelEdit(){
      window.location.href="storemanage.php";
    }
  </script>
</body>
</html>
