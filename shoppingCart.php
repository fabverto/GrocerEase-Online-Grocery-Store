<?php
//initiate session with userId and userType
session_start($options = ["userId", "userType", "cartId"]);
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

//Get the user id and cart id of the current cart
$userId = $_SESSION["userId"];
$cartId = "";
$cartQuery = "SELECT cart_id FROM shopping_cart WHERE active=1 AND user_id=\"$userId\"";
$cartResult = mysqli_query($conn, $cartQuery);
if(mysqli_num_rows($cartResult) > 0) {
  $row = mysqli_fetch_array($cartResult);
  $cartId = $row["cart_id"];
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

h1.verdana {
  font face = "verdana";
  font size = 90px;
}

.add-btn {
  height: 40px;
  border-radius: 15px;
  background: #25cd8a;
  color: white;
  border: none;
  padding: 12px;
  font-size: 16px;
  cursor: pointer;
  margin: 12px 0;
  box-shadow: none;
  outline: none;
  transition: background-color ease-in-out 0.1s;
}

.add-btn:hover{
  background-color: #46ea88;
}

.add-btn:active{
  background-color: #209f80;
}

p {
  font face = "verdana";
  font size = 50px;
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

.remove-item{
  background: none;
  outline: none;
  border: none;
  color: #e60000;
}
.remove-item:hover{
  color: #f3645c;
}

.remove-item:active{
  color: #eb413d;
}

.new-item-btn{
  border: none;
  background: #ff8f85;
  outline: none !important;
  box-shadow: none;
  border-radius: 15px;
  color: white;
  transition: background-color ease-in-out 0.1s;
  height: 35px;
  padding: 0 10px 0 10px;
}
.new-item-btn:hover{
  background-color: #f3645c;
}

.new-item-btn:active{
  background-color: #eb413d;
}

.wrapper{
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 30px
}
 </style>
 </head>


<body>
  <div style="background-color: #25cd8a; height: 100px; text-align: center; text-shadow: 3px 3px #209f80; pointer-events: none; user-select: none;-moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; border-radius: 25px 25px 0 0;">
    <h1 style="transform: translateY(25px); color: white;">GrocerEase</h1>
  </div><ul>
  <li><a href="index.php">Home</a></li>
  <li><a href="produce.php">Catalog</a></li>
  <li><a href="shoppinglist.php">Shopping Lists</a></li>
  <li><a class="active" href="shoppingCart.php">Shopping Cart</a></li>
  <li2><a href="logout.php">Logout</a></li2>

</ul>

<div class="container wrapper">
<h3>Shopping Cart</h3>
<div class="success">
  <?php
    if(isset($_GET["transfertocart"])) {
      if($_GET["transfertocart"] == "success") {
        echo "All items in shopping list transferred to cart!";
      }
    }
  ?>
</div>
<div class="table-responsive" style="width: 650px;">
  <table class="table table-bordered">
    <tr>
      <th width="40%">Item Name</th>
      <th width="10%">Quantity</th>
      <th width="20%">Price</th>
      <th width="15%">Total</th>
      <th width="5%">Action</th>
    </tr>
    <?php
      $total = 0;
      //Get all the items in the user's cart and join it with the quantity of each item
      $sql = "SELECT S.item_id, S.name, S.price, S.is_on_sale, S.sale_price, I.quantity_of_item FROM item S INNER JOIN is_put_in I ON I.item_id=S.item_id AND I.user_id=\"$userId\" AND I.cart_id=$cartId";
      $result = mysqli_query($conn, $sql);
      if(mysqli_num_rows($result) > 0) {
        //Generate rows of all your items in your cart
        while($row = mysqli_fetch_array($result)){
    ?>
        <tr>
          <td><?php echo $row["name"]; ?></td>
          <td><?php echo $row["quantity_of_item"]; ?></td>
          <td>$
            <?php
              if($row["is_on_sale"] == 1) {
                echo $row["sale_price"];
              }
              else {
                echo $row["price"];
              }
            ?>
          </td>
          <td>$
            <?php
              if($row["is_on_sale"] == 1) {
                echo number_format($row["sale_price"] * $row["quantity_of_item"], 2, '.', '');
              }
              else {
                echo number_format($row["price"] * $row["quantity_of_item"], 2, '.', '');
              }
            ?>
          </td>
          <td>
            <form action="includes/removefromcart.php" method="post">
              <input class="remove-item" type="submit" name="remove-item" value="Remove">
              <input type="hidden" name="item-id" value="<?php echo $row["item_id"]; ?>">
            </form>
          </td>
        </tr>
    <?php
        }
      }

      //Get the total price
      $sql="SELECT * FROM shopping_cart WHERE user_id=\"$userId\" AND cart_id=$cartId";
      $result = mysqli_query($conn, $sql);
      if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $total = $row["total_price"];
      }
    ?>
    <tr>
      <td colspan="5" align="left">
        <?php
          $sql = "SELECT * FROM is_applied_to WHERE cart_id='$cartId' AND user_id='$userId'";
          $result = mysqli_query($conn, $sql);
          if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo "Coupon code ".$row["coupon_code"]." is already applied.";
          }
          else{
        ?>
        <div>Apply Coupon Code: </div>
        <form action="includes/applycoupon.inc.php" method="post" style="text-align: end;">
          <input type="text" placeholder="coupon code" name="coupon-code">
          <input type="submit" class="new-item-btn" name="apply-coupon" value="Apply">
        </form>
        <?php
          }
        ?>
      </td>
    </tr>
    <tr>

      <td colspan="3" align="right">Total</td>
      <td align="right">
        <?php
          $sql = "SELECT * FROM shopping_cart WHERE cart_id='$cartId' AND user_id='$userId'";
          $result = mysqli_query($conn, $sql);
          $row = mysqli_fetch_assoc($result);
          if($row["discounted_price"] != NULL) {
            echo(
              "<div style=\"text-decoration: line-through\">$".$row["total_price"]." </div>".
              "<div>$".$row["discounted_price"]."</div>"
            );
          }
          else {
            echo("\$$total");
          }
        ?>
      </td>
      <td></td>
    </tr>
  </table>

  <form action="includes/placeorder.inc.php" method="post" style="text-align: end;">
    <input type="submit" class="new-item-btn" name="generate-order" value="Place Order">
  </form>
</div>
</div>
</body>
</html>
