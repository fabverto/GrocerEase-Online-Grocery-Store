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
    header("Location: ../login.php");
    exit();
  }

  $userId = $_SESSION["userId"];

  if(isset($_POST["create-list"])) {
    if($_POST["list-name"] == "") {
      header("Location: ../createlist.php?error=emptyfield");
      exit();
    }

    $listName = $_POST["list-name"];

    $sql = "SELECT * FROM shopping_list WHERE user_id = '$userId' AND name = '$listName'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
      header("Location: ../createlist.php?error=listexists");
      exit();
    }

    $sql = "INSERT INTO shopping_list (`name`, `user_id`, `total_price`) VALUES ('$listName', '$userId', 0)";
    $result = mysqli_query($conn, $sql);
    if(!$result) {
      header("Location: ../createlist.php?error=creationerror");
      exit();
    }
    header("Location: ../shoppinglist.php?create=success");
    exit();
  }

?>
