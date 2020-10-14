<?php
 if (isset($_POST['register-submit'])){
   include_once "./dbh.inc.php";
  $userId = mysqli_real_escape_string($conn, $_POST['input-username']);
  $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);

  if(empty($userId) || empty($password_1) || empty($password_2)){
    header("Location: ../register.php?error=emptyfields");
    exit();
  }
 if($password_1 != $password_2){
   header("Location: ../register.php?error=pwmismatch");
   exit();
 }

 $result = mysqli_query($conn, "SELECT user_id FROM user WHERE user_id = '$userId'");
 if(mysqli_num_rows($result) == 0) {
   $password = $password_1;
   //insert new user
   $sql = "INSERT INTO user (user_id, password) VALUES('$userId', '$password')";
   mysqli_query($conn, $sql);

   $sql = "INSERT INTO shopper (user_id) VALUES('$userId')";
   mysqli_query($conn, $sql);

   $newCartId = rand(0, 99999999);

   //create cart for new user
   $sql = "INSERT INTO shopping_cart (cart_id, total_price, user_id, company_id, active) VALUES($newCartId, 0, \"$userId\", \"quik-parcel\", 1)";
   mysqli_query($conn, $sql);

   if(mysqli_error($conn)) {
     echo mysqli_error($conn);
   }
   else {
     header("Location: ../login.php?register=success");
     exit();
   }
 } else {
   header("Location: ../register.php?error=existinguser");
   exit();
 }

}
?>
