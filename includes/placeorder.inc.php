<?php
  //initiate session with userId and userType
  session_start($options = ["userId", "userType", "cartId"]);
  include_once "./dbh.inc.php";

  //If userId is empty in the session, redirect back to login
  if(!isset($_SESSION["userId"])) {
    header("Location: ./login.php");
    exit();
  }
  $userId = $_SESSION["userId"];
  $cartId = "";
  $cartQuery = "SELECT cart_id FROM shopping_cart WHERE active=1 AND user_id=\"$userId\"";
  $cartResult = mysqli_query($conn, $cartQuery);
  if(mysqli_num_rows($cartResult) > 0) {
    $row = mysqli_fetch_array($cartResult);
    $cartId = $row["cart_id"];
  }

  if(isset($_POST["generate-order"])) {
    $orderNumber = rand(0, 99999999);
    $numItems = 0;
    $orderTotal = 0;
    $dateOfOrder = date('Y-m-d');

    //Calculate amount of items bought
    $sql = "SELECT SUM(quantity_of_item) FROM `is_put_in` WHERE cart_id=$cartId AND user_id='$userId'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $numItems = $row[0];
    if($numItems < 1) {
      header("Location: ../produce.php?error=noitems");
      exit();
    }

    $sql = "SELECT total_price, discounted_price FROM shopping_cart WHERE cart_id=$cartId AND user_id='$userId'";
    $result = mysqli_query($conn, $sql);

    $row = mysqli_fetch_assoc($result);
    if($row["discounted_price"] != NULL) {
      $orderTotal = $row["discounted_price"];
    } else {
      $orderTotal = $row["total_price"];
    }

    //Generate order from cart
    $sql = "INSERT INTO order_receipt (order_number, number_of_items, order_total, user_id, date_of_order, cart_id) VALUES($orderNumber, $numItems, $orderTotal, \"$userId\", $dateOfOrder, $cartId)";
    mysqli_query($conn, $sql);

    //Set the cart that just generated the order to inactive
    $sql = "UPDATE shopping_cart SET active=0 WHERE cart_id=$cartId AND user_id='$userId'";
    mysqli_query($conn, $sql);

    //Subtract the stock of each item.
    $sql = "SELECT item_id, quantity_of_item FROM is_put_in WHERE cart_id = $cartId AND user_id = \"$userId\"";
    $boughtItems = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($boughtItems)){
      $boughtItemId = $row["item_id"];
      $quantityToSubtract = $row["quantity_of_item"];
      $sql = "UPDATE item SET stock = (stock - $quantityToSubtract) WHERE item_id = '$boughtItemId'";
      $result = mysqli_query($conn, $sql);
      if(!$result) {
        echo "couldn't subtract stock!";
      }
    }

    //create new cart for user
    $newCartId = rand(0, 99999999);
    $sql = "INSERT INTO shopping_cart (cart_id, total_price, discounted_price, user_id, company_id, active) VALUES($newCartId, 0, NULL, \"$userId\", 'quik-parcel', 1)";
    mysqli_query($conn, $sql);
    header("Location: ../orderreceipt.php?orderNo=$orderNumber");
    exit();
  }

?>
