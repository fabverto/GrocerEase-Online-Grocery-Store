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

$userId = $_SESSION["userId"];

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
  <h3>Create New List</h3>
  <div class="error">
  </div>
  <form action="includes/createlist.inc.php" method="post">
    List name: <input type="text" name="list-name" value="">
    <input class="new-item-btn" type="submit" name="create-list" value="Create">
  </form>
</div>
<?php
  if(isset($_GET["error"])) {
    if($_GET["error"] == "listexists") {
      echo "<div style='color: red; text-align: center;'>Error: This list already exists. Please use another name.</div>";
    }
    if($_GET["error"] == "emptyfield") {
      echo "<div style='color: red; text-align: center;'>Error: Please enter a name for your list.</div>";
    }
    if($_GET["error"] == "creationerror") {
      echo "<div style='color: red; text-align: center;'>Error: Database error.</div>";
    }
  }
?>
</body>
</html>
