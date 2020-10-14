<?php
  //initiate session with userId and userType
  session_start($options = ["userId", "userType"]);
  include_once "./includes/dbh.inc.php";

    if(!isset($_SESSION["userType"])) {
      $userType = $_SESSION["userType"];
      if($userType == "store_manager") {
        header("Location: ../storemanage.php");
      }
    }

  //If userId is empty in the session, redirect back to login
  if(!isset($_SESSION["userId"])) {
    header("Location: ./login.php");
    exit();
  }
?>

<!DOCTYPE html>

<html>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">
  </script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js">
  </script>
  <link rel="stylesheet" href="style.css">
<style>

  .slideShows {display: none;}

  #borderImage {

  border: 30px solid transparent;
  padding: 30px;
  border-image-source: url(images/foodBorder.jpg);
  border-image-slice: 20;
  }

  ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
  overflow: hidden;
  background-color: #333;
  }

  li {
  float: left;
  }

  li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  }

 li a:hover:not(.active) {
 background-color: #209f80;
 }

  <!-- banner font----------------------->
  h1.verdana {
    font face = "verdana";
    font size = 90px;
  }

  p {
    font face = "verdana";
    font size = 50px;
  }
  .active {
  background-color: #209f80;;
}
/*Logout Button */
li2 {
  float: right;
}

li2 a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}
li2 a:hover:not(.active) {
  background-color: #209f80;
}

.intro-card{
  position: absolute;
  top: 37%;
  left: 25%;
  font-size: 32px;
  color: white;
  width: 800px;
  text-align: center;
  background: rgba(0,0,0, 0.65);
  border-radius: 25px;
  padding: 30px;
}

.footer {
  background: #333;
  height: 150px;
  border-radius: 0 0 25px 25px;
}

</style>


</head>

<body>
  <div style="background-color: #25cd8a; height: 100px; text-align: center; text-shadow: 3px 3px #209f80; pointer-events: none; user-select: none;-moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; border-radius: 25px 25px 0 0;">
  <h1 style="transform: translateY(25px); color: white;">GrocerEase</h1>
</div>

<ul>
  <li><a class="active" href="index.php">Home</a></li>
  <li><a href="produce.php">Catalog</a></li>
  <li><a href="shoppinglist.php">Shopping List</a></li>
  <li><a href="shoppingCart.php">Shopping Cart</a></li>
  <li2><a href="includes/logout.inc.php">Logout</a></li2>
</ul>

<!-- Slide Show ------------------------------------------------------------------------------->
<div>
  <div class="intro-card">
    GrocerEase is a certified Online Grocery Shopping service that provides fresh food and fast shipping for various groceries in Canada.
  </div>
  <div class = "pictureShow" style="height: 50%; overflow: hidden;">
    <img class="slideShows" img src= "images/fresh-vegetables.jpg" style="width:100%">
    <img class="slideShows" img src= "images/fresh.jpg" style="width:100%">
    <img class="slideShows" img src= "images/veggies.jpg" style="width:100%">
  </div>
</div>

  <div class="footer">
  </div>
<!-- Script for SlideShow ------------------------------------------------------------------------------>
  <script>
var slideIndex = 0;
slideShow();

function slideShow() {
  var i;
  var x = document.getElementsByClassName("slideShows");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  slideIndex++;
  if (slideIndex > x.length) {slideIndex = 1}
  x[slideIndex-1].style.display = "block";
  setTimeout(slideShow, 2000);
}
</script>
</body>
</html>
