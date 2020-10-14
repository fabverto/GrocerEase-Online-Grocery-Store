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


  if(isset($_POST["remove-item"])){
    $itemId = $_POST["item-id"];

    $sql = "DELETE FROM is_put_in WHERE item_id=\"$itemId\" AND user_id=\"$userId\" AND cart_id=\"$cartId\";";;
    mysqli_query($conn, $sql);

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
        }
      }
    }

    $sql = "UPDATE shopping_cart SET total_price=$total WHERE cart_id = $cartId AND user_id='$userId';";
    $result = mysqli_query($conn, $sql);
    if($result) {
      header("Location: ../produce.php?deletefromcart=success");
      exit();
    }
  }
?>
