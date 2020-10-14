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

  //Get the user id and cart id of the current cart
  $userId = $_SESSION["userId"];
  $cartId = "";
  $cartQuery = "SELECT cart_id FROM shopping_cart WHERE active=1 AND user_id=\"$userId\"";
  $cartResult = mysqli_query($conn, $cartQuery);
  if(mysqli_num_rows($cartResult) > 0) {
    $row = mysqli_fetch_array($cartResult);
    $cartId = $row["cart_id"];
  }

  if(!isset($_GET["listname"]) AND !isset($_POST["item-id"])) {
    header("Location: ../shoppinglist.php?error=invalidlist");
    exit();
  }

  $listName = urldecode($_GET["listname"]);
  //Get user shopping list
  $listQuery = "SELECT S.item_id, S.name, S.price, S.is_on_sale, S.sale_price, I.quantity_of_item FROM item S INNER JOIN is_added_to I ON I.item_id=S.item_id AND I.user_id=\"$userId\" AND I.list_name=\"$listName\"";
  $listResult = mysqli_query($conn, $listQuery);
  if(mysqli_error($conn)) {
    echo mysqli_error($conn);
  }

  if(isset($_POST["save-changes"])) {
    while($listRow = mysqli_fetch_assoc($listResult)) {
      $newQuantity = $_POST["quantity-".$listRow["item_id"]];
      if($newQuantity > 0) {
        $sql = "UPDATE is_added_to SET quantity_of_item = $newQuantity WHERE item_id=\"".$listRow["item_id"]."\" AND user_id=\"$userId\" AND list_name=\"$listName\"";
        mysqli_query($conn, $sql);
      }
    }
    header("Location: ../viewlist.php?listname=$listName&save=success");
    exit();
  }
  else if(isset($_POST["delete-item"])) {
    $deleteItemId = $_GET["delete"];

    $sql = "DELETE FROM is_added_to WHERE list_name='$listName' AND user_id='$userId' AND item_id='$deleteItemId'";
    mysqli_query($conn, $sql);
    if(mysqli_error($conn)) {
      echo mysqli_error($conn);
    }
    else {
      header("Location: ../viewlist.php?listname=$listName&delete=success");
      exit();
    }
  }

  else if(isset($_POST["transfer"])){
    while($listRow = mysqli_fetch_assoc($listResult)) {
        //If already in cart, add to quantity
        $itemId = $listRow["item_id"];
        $quantity = $_POST["quantity-".$listRow["item_id"]];
        $sql = "SELECT * FROM is_put_in WHERE user_id=\"$userId\" AND cart_id=$cartId AND item_id=\"$itemId\"";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0) {
          //if user puts more than available stock, make the cart hold the max amount possible
          $quantityAlreadyInCart = mysqli_fetch_assoc($result)["quantity_of_item"];
          $sql = "SELECT name, stock FROM item WHERE item_id = '$itemId'";
          $itemResult = mysqli_query($conn, $sql);
          $row = mysqli_fetch_assoc($itemResult);
          if(($quantity + $quantityAlreadyInCart) > $row["stock"]) {
            $quantity = $row["stock"] - $quantityAlreadyInCart;
          }

          $sql = "UPDATE is_put_in SET quantity_of_item=quantity_of_item+$quantity WHERE cart_id=$cartId AND user_id=\"$userId\" AND item_id=\"$itemId\"";
          if (!mysqli_query($conn, $sql)) {
            header("Location: ../viewlist.php?listname=$listName&transfertocart=error");
            exit();
          }
        }
        else {
          //Error if user tries to put more than the available stock into shopping cart
          $sql = "SELECT name, stock FROM item WHERE item_id = '$itemId'";
          $itemResult = mysqli_query($conn, $sql);
          $row = mysqli_fetch_assoc($itemResult);
          if($quantity > $row["stock"]) {
            $quantity = $row["stock"];
          }

          //If not in cart create new entry
          $sql = "INSERT INTO is_put_in (item_id, user_id, cart_id, quantity_of_item) VALUES ('$itemId','$userId','$cartId','$quantity')";
          if (!mysqli_query($conn, $sql)) {
            header("Location: ../viewlist.php?listname=$listName&transfertocart=error");
            exit();
          }
        }

        $total = 0;
        $sql = "SELECT S.item_id, S.name, S.price, S.is_on_sale, S.sale_price, I.quantity_of_item FROM item S INNER JOIN is_put_in I ON I.item_id=S.item_id AND I.user_id=\"$userId\" AND I.cart_id=$cartId";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_array($result)){
            if($row["is_on_sale"] == 1) {
              $total += $row["quantity_of_item"] * $row["sale_price"];
            }
            else {
              $total += $row["quantity_of_item"] * $row["price"];
            }      }
        }

        $sql = "UPDATE shopping_cart SET total_price=$total WHERE cart_id = $cartId AND user_id='$userId';";
        $result = mysqli_query($conn, $sql);
        if(!$result) {
          header("Location: ../viewlist.php?listname=$listName&error=transfertocartfail");
          exit();
        }
      }
      header("Location: ../shoppingCart.php?transfertocart=success");
      exit();
    }
    else if(isset($_POST["add-to-list"])) {
      $quantity = $_POST["quantity"];
      $itemId = $_POST["item-id"];
      if(!isset($_POST["shopping-list"]) || empty($_POST["shopping-list"])) {
        header("Location: ../produce.php?error=nolist");
        exit();
      }
      $addToList = $_POST["shopping-list"];
      $sql = "SELECT * FROM is_added_to WHERE list_name='$addToList' AND item_id='$itemId' AND user_id='$userId'";
      $result = mysqli_query($conn, $sql);

      if(mysqli_num_rows($result) > 0) {
        $sql = "UPDATE is_added_to SET `quantity_of_item` = `quantity_of_item`+$quantity WHERE list_name='$addToList' AND item_id='$itemId' AND user_id='$userId'";
        $result = mysqli_query($conn, $sql);
        if(!result) {
          header("Location: ../produce.php?error=addtolistfail");
          exit();
        }
      }
      else {
        $sql = "INSERT INTO is_added_to (`list_name`, `user_id`, `item_id`, `quantity_of_item`) VALUES ('$addToList', '$userId', '$itemId', $quantity)";
        $result = mysqli_query($conn, $sql);
        if(!result) {
          header("Location: ../produce.php?error=addtolistfail");
          exit();
        }
      }
      header("Location: ../produce.php?addtolist=success");
      exit();
    }
?>
