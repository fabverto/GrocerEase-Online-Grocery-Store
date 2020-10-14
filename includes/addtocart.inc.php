<?php
  //initiate session with userId and userType
  session_start($options = ["userId", "userType", "cartId"]);
  include_once "./dbh.inc.php";

  //If userId is empty in the session, redirect back to login
  if(!isset($_SESSION["userId"])) {
    header("Location: ./login.php");
    exit();
  }

  //get cart id of user's current cart
  $userId = $_SESSION["userId"];
  $cartId = "";
  $cartQuery = "SELECT cart_id FROM shopping_cart WHERE active=1 AND user_id=\"$userId\"";
  $cartResult = mysqli_query($conn, $cartQuery);
  if(mysqli_num_rows($cartResult) > 0) {
    $row = mysqli_fetch_array($cartResult);
    $cartId = $row["cart_id"];
  }

	if(isset($_POST["add_to_cart"])){
    //If already in cart, add to quantity
    $itemId = $_POST["item-id"];
    $quantity = $_POST["quantity"];
    $sql = "SELECT * FROM is_put_in WHERE user_id=\"$userId\" AND cart_id=$cartId AND item_id=\"$itemId\"";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
      //Error if user tries to put more than the available stock into shopping cart
      $quantityAlreadyInCart = mysqli_fetch_assoc($result)["quantity_of_item"];
      $sql = "SELECT name, stock FROM item WHERE item_id = '$itemId'";
      $itemResult = mysqli_query($conn, $sql);
      $row = mysqli_fetch_assoc($itemResult);
      if(($quantity + $quantityAlreadyInCart) > $row["stock"]) {
        header("Location: ../produce.php?error=exceedstock");
        exit();
      }

      $sql = "UPDATE is_put_in SET quantity_of_item=quantity_of_item+$quantity WHERE cart_id=$cartId AND user_id=\"$userId\" AND item_id=\"$itemId\"";
      if (!mysqli_query($conn, $sql)) {
        header("Location: ../produce.php?addtocart=error");
        exit();
      }
    }
    else {
      //Error if user tries to put more than the available stock into shopping cart
      $sql = "SELECT name, stock FROM item WHERE item_id = '$itemId'";
      $itemResult = mysqli_query($conn, $sql);
      $row = mysqli_fetch_assoc($itemResult);
      if($quantity > $row["stock"]) {
        header("Location: ../produce.php?error=exceedstock");
        exit();
      }

      //If not in cart create new entry
      $sql = "INSERT INTO is_put_in (item_id, user_id, cart_id, quantity_of_item) VALUES ('$itemId','$userId','$cartId','$quantity')";
      if (!mysqli_query($conn, $sql)) {
        header("Location: ../produce.php?addtocart=error");
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
    if($result) {
      header("Location: ../produce.php?addtocart=success");
      exit();
    }
	}
?>
