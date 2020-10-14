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

  $percentOff = 0;
  if(isset($_POST["apply-coupon"])){
    $couponCode = $_POST["coupon-code"];

    $sql = "SELECT percent_off, is_used FROM coupon WHERE coupon_code=?;";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $couponCode);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
      if($row["is_used"] == 1) {
        header("Location: ../produce.php?error=usedcoupon");
        exit();
      } else {
        $percentOFf = $row["percent_off"];
      }
    } else {
      header("Location: ../produce.php?error=couponnotexist");
      exit();
    }

    $sql = "INSERT INTO `is_applied_to`(`cart_id`, `user_id`, `coupon_code`) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $cartId, $userId, $couponCode);
    mysqli_stmt_execute($stmt);

    $total = 0;
    $sql="SELECT * FROM shopping_cart WHERE user_id=\"$userId\" AND cart_id=$cartId";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_array($result);
      $total = number_format($row["total_price"] * ((100 - $percentOFf)*0.01), 2);

      $sql = "UPDATE shopping_cart SET discounted_price=$total WHERE cart_id = $cartId AND user_id='$userId';";
      $result = mysqli_query($conn, $sql);
      if($result) {
        $sql = "UPDATE coupon SET is_used=1 WHERE coupon_code='$couponCode'";
        $result2 = mysqli_query($conn, $sql);
        if($result2) {
          header("Location: ../produce.php?applycoupon=success$total");
          exit();
        }
      }
    }
  }
?>
