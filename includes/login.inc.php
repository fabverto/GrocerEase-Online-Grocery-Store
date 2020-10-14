<?php
  if(isset($_POST["login-submit"])) {
    //import the file with the sql connection
    require "dbh.inc.php";

    //Grab the username and password from both of the login input fields
    $userId = $_POST["input-username"];
    $password= $_POST["input-password"];

    //If either of these are empty redirect back to same page with error msg
    if(empty($userId) || empty($password)) {
      header("Location: ../login.php?error=emptyfields");
      exit();
    }
    else {
      //Create the query and turn it to a prepared statement
      $sql = "SELECT * FROM user WHERE user_id=?;";
      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt, $sql)) {
        //If this fails redirect back with error
        header("Location: ../login.php?error=sqlerror");
        exit();
      }
      else {

        //bind the variable (?) in the query with $userId and execute query
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        //If the query is succesful, put the result in associative list
        if($row = mysqli_fetch_assoc($result)) {
          /*If inputted password matches actual password, direct to main page
          with user_id and member_type saved in the session*/
          if ($password == $row["password"]) {
            session_start();
            $_SESSION["userId"] = $row["user_id"];
            $_SESSION["userType"] = $row["member_type"];

            if($_SESSION["userType"] == "shopper") {
              header("Location: ../index.php?login=success");
              exit();
            }
            else if($_SESSION["userType"] == "store_manager") {
              header("Location: ../storemanage.php?login=success");
              exit();
            }
          }
          //if not redirect back with error msg
          else {
            header("Location: ../login.php?error=wrongpassword");
            exit();
          }
        }
        else {
          header("Location: ../login.php?error=nouser");
          exit();
        }
      }
    }
  }
  else {
    header("Location: ../login.php");
    exit();
  }

?>
