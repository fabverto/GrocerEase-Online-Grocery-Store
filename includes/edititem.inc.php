<?php
  if(isset($_POST["save-submit"])) {
    //import the file with the sql connection
    require "dbh.inc.php";

    $itemId = $_POST["item-id"];
    $editedItemName = $_POST["edit-name"];
    $editedDescription = "";
    if(!empty($_POST["edit-description"])) {
      $editedDescription = $_POST["edit-description"];
    }
    $editedStock = $_POST["edit-stock"];
    $editedPrice = $_POST["edit-price"];
    $editedOnSale = 0;
    if(!empty($_POST["edit-on-sale"])) {
      $editedOnSale = 1;
    }
    $editedPercOff = 0;
    if(!empty($_POST["edit-percent-off"])) {
      $editedPercOff = $_POST["edit-percent-off"];
    }
    $salePrice = $editedPrice * ((100-$editedPercOff) * 0.01);
    $null = NULL;
    $itemImg = NULL;
    if($_FILES['edit-img']['tmp_name']) {
      $itemImg = file_get_contents($_FILES['edit-img']['tmp_name']);
    }

    //If these are empty redirect back to same page with error msg
    if(empty($editedItemName) || empty($editedStock) || empty($editedPrice)) {
      header("Location: ../edititem.php?itemId=$itemId&error=emptyfields");
      exit();
    }

    else {
      //Create the query and turn it to a prepared statement
      $sql = "UPDATE item SET name=?, description=?, stock=?, price=?, is_on_sale=?, percent_off=?, image=? WHERE item_id=?;";
      if($itemImg == NULL) {
        $sql = "UPDATE item SET name=?, description=?, stock=?, price=?, is_on_sale=?, percent_off=? WHERE item_id=?;";
      }
      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt, $sql)) {
        //If this fails redirect back with error
        header("Location: ../edititem.php?itemId=$itemId&error=sqlerror");
        exit();
      }
      else {
        //bind the variable (?) in the query with $userId and execute query
        if($itemImg != NULL) {
          mysqli_stmt_bind_param($stmt, "ssidiibsd", $editedItemName, $editedDescription, $editedStock, $editedPrice, $editedOnSale, $editedPercOff, $null, $itemId, $salePrice);
          $stmt->send_long_data(6, $itemImg);
        }
        else {
          mysqli_stmt_bind_param($stmt, "ssidiisd", $editedItemName, $editedDescription, $editedStock, $editedPrice, $editedOnSale, $editedPercOff, $itemId, $salePrice);
        }
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_error($conn)) {
          header("Location: ../edititem.php?itemId=$itemId&error=failedquery");
          exit();
        }
        else {
          header("Location: ../storemanage.php?edit=success");
          exit();
        }
      }
    }
  }
?>
