<?php
  session_start();
  include_once "./includes/dbh.inc.php";
?>
<!DOCTYPE html>
<html>
<head>

 <script>
 function goBack() {
  window.history.back()
}
 </script>
  <script src="https://kit.fontawesome.com/8b234391c4.js"></script>
 <link rel="stylesheet" href="style.css">
<style>
 body, html {
 height: 100%;
 margin: 0;
 }
.bg {
  background-image: url("images/foodBorder.jpg");
  height: 100%;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;

 }


</style>
</head>
<body>
 <div class="bg">
 <div class="register-box-wrapper">
   <h2>Register</h2>
 <div class="register-box">
   <form class="register-form" action="includes/register.inc.php" method="post">
     <div class="textbox">
       <i class="fas fa-child"></i>
       <input type="text" placeholder="Username" onfocus="this.placeholder = ''" onBlur="this.placeholder = 'Username'" name="input-username" value="">
     </div>
      <div class ="textbox">
       <i class="fas fa-unlock"></i>
       <input type ="password" placeholder="Password" onfocus="this.placeholder = ''" onBlur="this.placeholder = 'Password'" name="password_1" value="">
     </div>
       <div class ="textbox">
       <i class="fas fa-unlock"></i>
       <input type ="password" placeholder="Re-enter Password" onfocus="this.placeholder = ''" onBlur="this.placeholder = 'Re-enter Password'" name="password_2" value="">
     </div>
      <input class="btn" type="submit" name="register-submit" value="Register">
   </form>
</div>
<div class="error text-center">
  <?php
     if(isset($_GET["error"])) {
       if($_GET["error"] == "emptyfields") {
         echo "Error. Please fill in all fields.";
       }
       if($_GET["error"] == "existinguser") {
         echo "Error. That user already exists. Please enter another username.";
       }
       if($_GET["error"] == "pwmismatch") {
         echo "Error. The password did not match.";
       }
     }
  ?>
</div>
</div>

</body>
</html>
