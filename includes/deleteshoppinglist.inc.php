<?php
  //initiate session with userId and userType
  session_start($options = ["userId", "userType", "cartId"]);
  include_once "./dbh.inc.php";

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

  //Get the user id
  $userId = $_SESSION["userId"];

  if(!isset($_GET["listname"])) {
    header("Location: ../shoppinglist.php?error=invalidlist");
    exit();
  }

  $listName = urldecode($_GET["listname"]);
  //Get user shopping list
  $listQuery = "DELETE FROM shopping_list WHERE user_id='$userId' AND name='$listName'";
  $listResult = mysqli_query($conn, $listQuery);
  header("Location: ../shoppinglist.php?delete=success");
  exit();
?>
