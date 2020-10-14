<?php
  if(isset($_POST["delete-btn"])) {
    //import the file with the sql connection
    require "dbh.inc.php";

    $itemId = $_POST["delete-item-id"];

    $sql = "DELETE FROM item WHERE item_id=\"$itemId\"";
    mysqli_query($conn, $sql);
    header("Location: ../storemanage.php");
    exit();
  }
?>
