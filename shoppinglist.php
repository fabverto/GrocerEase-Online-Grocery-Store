<?php
//initiate session with userId and userType
session_start($options = ["userId", "userType", "cartId"]);
include_once "./includes/dbh.inc.php";

if(!isset($_SESSION["userType"])) {
  $userType = $_SESSION["userType"];
  if($userType == "store_manager") {
    header("Location: ../storemanage.php");
  }
}

//If userId is empty in the session, redirect back to login
if(!isset($_SESSION["userId"])) {
  header("Location: ./login.php");
  exit();
}

//Get the user id and cart id of the current cart
$userId = $_SESSION["userId"];
$cartId = "";
$cartQuery = "SELECT cart_id FROM shopping_cart WHERE active=1 AND user_id=\"$userId\"";
$cartResult = mysqli_query($conn, $cartQuery);
if(mysqli_num_rows($cartResult) > 0) {
  $row = mysqli_fetch_array($cartResult);
  $cartId = $row["cart_id"];
}

//Get user shopping list
$listQuery = "SELECT * FROM shopping_list WHERE user_id=\"$userId\"";
$listResult = mysqli_query($conn, $listQuery);

?>

<!DOCTYPE html>

<html>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">
  </script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js">
  </script>
  <link rel="stylesheet" href="style.css">
<style>

h1.verdana {
  font face = "verdana";
  font size = 90px;
}

.add-btn {
  height: 40px;
  border-radius: 15px;
  background: #25cd8a;
  color: white;
  border: none;
  padding: 12px;
  font-size: 16px;
  cursor: pointer;
  margin: 12px 0;
  box-shadow: none;
  outline: none;
  transition: background-color ease-in-out 0.1s;
}

.add-btn:hover{
  background-color: #46ea88;
}

.add-btn:active{
  background-color: #209f80;
}

p {
  font face = "verdana";
  font size = 50px;
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

.active {
background-color: #209f80;;
}

/*Logout Button */
li2 {
float: right;
}


li2 a {
display: block;
color: white;
text-align: center;
padding: 14px 16px;
text-decoration: none;
}
li2 a:hover:not(.active) {
background-color: #209f80;
}

.remove-item{
  background: none;
  outline: none;
  border: none;
  color: #e60000;
}
.remove-item:hover{
  color: #f3645c;
}

.remove-item:active{
  color: #eb413d;
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

.wrapper{
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 30px
}
 </style>
 </head>


<body>
  <div style="background-color: #25cd8a; height: 100px; text-align: center; text-shadow: 3px 3px #209f80; pointer-events: none; user-select: none;-moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; border-radius: 25px 25px 0 0;">
  <h1 style="transform: translateY(25px); color: white;">GrocerEase</h1>
  </div>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="produce.php">Catalog</a></li>
    <li><a class="active" href="shoppinglist.php">Shopping Lists</a></li>
    <li><a href="shoppingCart.php">Shopping Cart</a></li>
    <li2><a href="logout.php">Logout</a></li2>
  </ul>

<div class="container wrapper">
  <h3>Shopping Lists</h3>
  <p style="width: 400px; text-align: center;">Shopping lists are a great way to save a list of favourite items to a list. You can add all the items in a list to your cart anytime you want!</p>
  <form style="margin-left: 10px; margin-bottom: 10px;" action="createlist.php" method="post">
    <input class="new-item-btn" type="submit" name="create-list" value="Create new list">
  </form>
<div class="error">
<?php
 //Take the query parameters in the path, if has error, show the error message.
  if(isset($_GET["error"])) {
    if($_GET["error"] == "invalidlist") {
      echo 'Error: That list does not exist.';
    }
  }
?>
</div>
<div class="table-responsive" style="width: 650px;">
  <table class="table table-bordered">
    <tr>
      <th width="70%">List Name</th>
      <th width="30%">Action</th>
    </tr>
    <?php
      while($row = mysqli_fetch_assoc($listResult)) {
        echo(
          "<tr>".
            "<td>".
              $row["name"].
            "</td>".
            "<td>".
              "<div style=\"display: flex; flex-direction: row;\">".
                "<form action=\"includes/deleteshoppinglist.inc.php?listname=".$row["name"]."\" method=\"post\">".
                  "<input class=\"new-item-btn\" type=\"submit\" name=\"delete-list\" value=\"Delete\">".
                "</form>".
                "<form style=\"margin-left: 10px\" action=\"viewlist.php?listname=".$row["name"]."\" method=\"post\">".
                  "<input class=\"new-item-btn\" type=\"submit\" name=\"view-list\" value=\"View\">".
                "</form>".
              "</div>".
            "</td>".
          "</tr>"
        );
      }
    ?>

  </table>
  <?php
    if(isset($_GET["delete"])) {
      if($_GET["delete"] == "success") {
        echo "<div style='color: limegreen;'>Shopping list succesfully deleted.</div>";
      }
    }
    if(isset($_GET["create"])) {
      if($_GET["create"] == "success") {
        echo "<div style='color: limegreen;'>New shopping list created.</div>";
      }
    }
  ?>
</div>
</div>
</body>
</html>
