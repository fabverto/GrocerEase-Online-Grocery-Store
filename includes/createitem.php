<?php
  //initiate session with userId and userType
  session_start($options = ["userId", "userType"]);
  $userId = $_SESSION["userId"];

  if(isset($_POST["create-submit"])) {
    //import the file with the sql connection
    require "dbh.inc.php";

    //generate new item id
    $itemId = str_replace(' ', '', strtolower($_POST["edit-name"])).strval(mt_rand());
    $itemName = $_POST["name"];
    $itemDesc = "";
    if(!empty($_POST["description"])) {
      $itemDesc = $_POST["description"];
    }
    $itemStock = $_POST["stock"];
    $itemPrice = $_POST["price"];
    $onSale = 0;
    if(!empty($_POST["on-sale"])) {
      $onSale = 1;
    }
    $percOff = 0;
    $salePrice = NULL;
    if(!empty($_POST["percent-off"])) {
      $percOff = $_POST["percent-off"];
      $salePrice = $itemPrice * ((100-$percOff) * 0.01);
    }

    $null = NULL;
    $itemImg = file_get_contents($_FILES['upload-img']['tmp_name']);

    //If these are empty redirect back to same page with error msg
    if(empty($itemName) || empty($itemStock) || empty($itemPrice)) {
      header("Location: ../storemanage.php?error=emptyfields");
      exit();
    }

    else {
      //Create the query and turn it to a prepared statement
      $sql = "INSERT INTO `item` (`item_id`, `name`, `description`, `stock`, `price`, `is_on_sale`, `store_id`, `percent_off`, `sale_price`, `image`) VALUES (?, ?, ?, ?, ?, ?, (SELECT store_id FROM store_manager WHERE user_id =?), ?, ?, ?)";
      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt, $sql)) {
        //If this fails redirect back with error
        header("Location: ../edititem.php?itemId=$itemId&error=sqlerror");
        exit();
      }
      else {
        //bind the variable (?) in the query with $userId and execute query
        mysqli_stmt_bind_param($stmt, "sssidisidb", $itemId, $itemName, $itemDesc, $itemStock, $itemPrice, $onSale, $userId, $percOff, $salePrice, $null);
        $stmt->send_long_data(9, $itemImg);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_error($conn)) {
          header("Location: ../storemanage.php?error=failcreatequery");
          exit();
        }
        else {
          header("Location: ../storemanage.php");
          exit();
        }
      }
    }
  }
?>
