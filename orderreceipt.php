<!-- https://www.youtube.com/watch?v=0wYSviHeRbs -->
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
  $userId = $_SESSION["userId"];
?>

<!DOCTYPE html>

<html>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">
  </script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js">
    </script>
<style>
<link rel="stylesheet" href="style.css">

#borderImage {

  border: 30px solid transparent;
  padding: 30px;
  border-image-source: url(images/foodBorder.jpg);
  border-image-slice: 20;
  }

  h1.verdana {
    font face = "verdana";
    font size = 90px;
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
 </style>
 </head>

<body>
  <div style="background-color: #25cd8a; height: 100px; text-align: center; text-shadow: 3px 3px #209f80; pointer-events: none; user-select: none;-moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; border-radius: 25px 25px 0 0;">
    <h1 style="transform: translateY(25px); color: white;">GrocerEase</h1>
  </div><ul>
  <li><a href="index.php">Home</a></li>
  <li><a class="active" href="produce.php">Catalog</a></li>
  <li><a href="shoppinglist.php">Shopping Listss</a></li>
  <li><a href="shoppingCart.php">Shopping Cart</a></li>
  <li2><a href="logout.php">Logout</a></li2>
 </ul>
 <br />
<div class="container" style="width:700px;">
  <div style="clear:both"></div>
      <br />
      <h3>Thank you for your order!</h3>
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th width="40%">Item Name</th>
            <th width="10%">Quantity</th>
            <th width="20%">Price</th>
            <th width="15%">Total</th>
          </tr>
          <?php
            $sql = "SELECT cart_id FROM order_receipt WHERE order_number=".$_GET["orderNo"].";";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $cartId = $row["cart_id"];

            $total = 0;
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1!!!!!!!!!!!!!!
            //COMPLEX QUERY SELECT INFORMATION OF ALL ITEMS IN A CART ALONG WITH THE QUANTITY PLACED IN THE CART OF EACH ITEM FROM IS_PUT_IN TABLE
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $sql = "SELECT S.item_id, S.name, S.price, S.is_on_sale, S.sale_price, I.quantity_of_item FROM item S INNER JOIN is_put_in I ON I.item_id=S.item_id AND I.user_id=\"$userId\" AND I.cart_id=$cartId";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0) {
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
                      echo $row["sale_price"] * $row["quantity_of_item"];
                    }
                    else {
                      echo $row["price"] * $row["quantity_of_item"];
                    }
                  ?>
                </td>
              </tr>
          <?php
              }
            }

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
          </tr>
        </table>

      </div>
      <form action="includes/placeorder.php" method="post" style="text-align: center;">
        <a class="new-item-btn" style="padding: 10px; color: white !important; text-decoration: none !important;" href="produce.php">Back to catalog</a>
      </form>
    </div>
  </div>
  <br />
  </body>
</html>
