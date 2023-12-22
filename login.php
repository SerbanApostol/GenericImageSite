<?php //pagina de login, in care se modifica sau nu nivelul de acces
  session_start();
  if (isset($_SESSION['expiring_on']) && time() > $_SESSION['expiring_on']) {
    //sesiunea a expirat
    session_unset();
    //session_destroy();
	session_regenerate_id(true);
    //session_start();
  }
  
  //are macar o ora de trait
  $_SESSION['expiring_on'] = time() + 3600;
?>

<?php
	
  function test_input($data) { //functie de a asigura ca input-ul nu poate face rau, in caz ca era malitios
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  
  $user = "";
  $pswd = "";
  $error = "";
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "GikaC";
    $password = "badsi_des";
    
    try {
      $conn = new PDO("mysql:host=$servername;dbname=GenericImageSite", $username, $password);
	  
	  if(isset($_POST['login'])) {
        $stmt = $conn->prepare("SELECT PassHash FROM Users WHERE User = '" . test_input($_POST["user"]) . "';");
	    $stmt->execute();
	    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
		if(isset($result["PassHash"])) {
	      if(password_verify($_POST["pswd"], $result["PassHash"])) {
	        $_SESSION["user"] = test_input($_POST["user"]);
	        $_SESSION["loggedin"] = "yes";
	        $_SESSION["expiring_at"] = time() + 3600;
			$_SESSION["current_page"] = 1;
	        $user = "";
	        $pswd = "";
	        $error = "";
	        header("Location: http://localhost/index.php");
	        exit();
	      } else {
	        $user = $_POST["user"];
	        $error = "Wrong password";
	      }
		} else {
	      $user = $_POST["user"];
	      $error = "Wrong username";
	    }
      } elseif (isset($_POST['newUser'])) {
	    $user = test_input($_POST["user"]);
        $stmt = $conn->prepare("SELECT User FROM Users WHERE User = '" . $user . "';");
	    $stmt->execute();
	    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
		if(isset($result["User"]) || $user == "Guest") {
		  $user = $_POST["user"];
	      $error = "Username already in use";
		} else {
		  if(strlen($_POST["pswd"]) < 6 || strlen($_POST["pswd"]) > 60) {
		    $user = $_POST["user"];
	        $error = "The password must be between 6 and 60 characters long";
	      } else {
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $conn->exec("INSERT INTO Users (User, PassHash) VALUES('" . $user . "', '" . password_hash($_POST["pswd"], PASSWORD_DEFAULT) . "');");
	        $_SESSION["user"] = $user;
	        $_SESSION["loggedin"] = "yes";
	        $_SESSION["expiring_at"] = time() + 3600;
			$_SESSION["current_page"] = 1;
	        header("Location: http://localhost/index.php");
		    exit();
		  }
		}
      }
	  
    }
    catch(PDOException $e)
    {
      echo "<p style=\"position: fixed; bottom: -5px; right: 8px; color: white;\">An error occurred: " . $e->getMessage() . "</p>";
    }
    
  }
?>

<html>
 <head>
  <title>Login Page</title>
 </head>
 <body>
  <input type="button" value="Continue as guest" style="position: fixed; top: 10px; left: 8px;" onClick="document.location.href='index.php'">
  <form style="position: relative; top: 25vh; left: 5vw; width: 30vw;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
   
   <h1>Please login or<br>create new user</h1>
   <input type="text" maxlength="20" placeholder="Username" name="user" value="<?php echo test_input($user); ?>"><br><br>
   <input type="password" placeholder="Password" name="pswd"><br><br>
   <input type="submit" name="login" value="Login">&ensp;
   <input type="submit" name="newUser" value="New Account"><br><br>
   <span style="background-color: #ff9090;"><?php echo $error; ?></span>
   
  </form>
  <img src="Logo.png" style="z-index: -3; height: 100vh; position: fixed; right: 0px; top: 0px;">
 </body>
</html>