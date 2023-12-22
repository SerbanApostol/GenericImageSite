<?php //parte pentru autentificare
  session_start();
  if (isset($_SESSION['expiring_on']) && time() > $_SESSION['expiring_on']) {
    //sesiunea a expirat
    session_unset();
    //session_destroy();
	session_regenerate_id(true);
    //session_start();
  }
    
  //conectarea la baza de date
  $servername = "localhost";
  $username = "GikaC";
  $password = "badsi_des";
    
  try {
    $conn = new PDO("mysql:host=$servername;dbname=GenericImageSite", $username, $password);
//    echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">Connected successfully to DB</p>";
    }
  catch(PDOException $e)
    {
    echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">Connection failed: " . $e->getMessage() . "</p>";
    }
  
  if(!isset($_SESSION["user"])) {
    $_SESSION["user"] = "Guest";
    $_SESSION["loggedin"] = null;
	$_SEESION["current_page"] = 1;
  } else {
    $_SESSION['expiring_on'] = time() + 3600;
  }
	
  function test_input($data) { //functie de a asigura ca input-ul nu poate face rau, in caz ca era malitios
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
?>

<!DOCTYPE html>
<html>
 <head>
  <title>Generic Image Site</title>
  <style>
    a:link, a:visited {
		text-decoration: none;
		color: white;
	}
	a:hover, a:active {
		text-decoration: underline;
		color: red;
	}
  </style>
 </head>
 <body>
  
  <div style="position: fixed; top: 0px; left: 0px; width: 100vw; height: 60px; background-color: black; color: white;">
    <a href='index.php' style="color: white;">
     <img src="Logo.png" style="width: 60px; height: 50px; position: fixed; top: 5px; left: 5px;" alt="GIS Logo">
     <span style="font-weight: bold; font-size: 40px; position: fixed; top: 5px; left: 70px;">Generic Image Site</span>
    </a>
    <?php //crearea unui buton de login sau link de delogare
	  if(!isset($_SESSION["loggedin"])) {
	    echo "<input type=\"button\" value=\"Login\" style=\"position: fixed; top: 10px; right: 8px;\" onClick=\"document.location.href='login.php'\" />";
	    $error = "Log in to post";
	  } else {
	    echo "<p style=\"position: fixed; top: -5px; right: 8px;\">Hi, <a href=\"index.php?user=" . $_SESSION["user"] . "\">" . $_SESSION["user"] . "</a>!</p>";
	    echo "<a href='index.php?logout=true' style=\"position: fixed; top: 30px; right: 8px;\">Logout</a>";
        $error = "";
	  }
      //delogarea
      if(isset($_GET["logout"])) {
        session_unset();
	    session_regenerate_id(true);
        $_SESSION["user"] = null;
        $_SESSION["loggedin"] = null;
	    $_SEESION["current_page"] = 1;
      }
	  
	  $time = date('Y-m-d-H-i-s');
	  if(!isset($_GET["post"]) && !isset($_POST["post"])) {
	    if(!isset($_FILES["image"]["tmp_name"])) {
		  $error = 'Please select an image';
	    } else {
		  $target_dir = "/Uploads/";
		  $imageFileType = strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
		  $target_file = $target_dir . $time . $_SESSION["user"] . "." . $imageFileType;
		  $uploadOk = 1;
		  if(isset($_POST["submit"])) {
		    $check = getimagesize($_FILES["image"]["tmp_name"]);
		    if($check === false) {
			  $error = "File is not an image.";
			  $uploadOk = 0;
		    } else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			  $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed";
			  $uploadOk = 0;
		    }
		    if($uploadOk == 1) {
			  if (move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . $target_file)) {
			    try {
			      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		          $conn->exec("INSERT INTO Posts (User, Time, Image, Text) VALUES('" . $_SESSION["user"] . "', '" . substr($time, 0, 10) . " " . substr($time, 11, 2) . ":" . substr($time, 14, 2) . ":" . substr($time, 17, 2) . "', '" . $target_file . "', '" . addslashes($_POST["Text"]) . "');");
			      $error = "Post successfully uploaded";
			    }
			    catch(PDOException $e) {
			      $error = "There was an error while posting: " . $e->getMessage() . ". Please try again later";
			    }
			  } else {
			    $error = "Sorry, there was an error uploading your file. Please try again later";
			  }
		    }
		  }
	    }
	  } else if(isset($_POST["submit"])) {
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec("UPDATE Posts SET User = '" . $_SESSION["user"] . "', Time = '" . substr($time, 0, 10) . " " . substr($time, 11, 2) . ":" . substr($time, 14, 2) . ":" . substr($time, 17, 2) . "', Text = '" . addslashes(trim($_POST["Text"])) . "' WHERE IDPost = " . $_POST["post"] . ";");
		$error = "Post successfully updated";
	  }
    ?>
  </div>
  
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data" method="POST" name="newpost" style="position: fixed; top: 120px; left: 20px;">
   <textarea name="Text" id="Text" rows="8" cols="50" maxlength="200">
    <?php
	  if(isset($_GET["post"])) {
	    $stmt1 = $conn->prepare("SELECT Text FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
	    $stmt1->execute();
	    $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
	    echo test_input($result1["Text"]);
	  }
	?>
   </textarea><br>
    <?php
	  if(!isset($_GET["post"])) {
	    echo 'Select an image file to upload: <input type="file" name="image"><br><br>';
	  } else {
	    echo '<input type="hidden" name="post" value="' . $_GET["post"] . '">';
	  }
      if(isset($_SESSION["loggedin"])) {
	    echo "<input type='submit' name='submit' value='Post'><br><br>";
	  }
    ?>
	<span style="background-color: #d0d0d0;"><?php echo $error; ?></span>
  </form>
  <img src="PostBackground.png" style="z-index: -3; height: 100vh; position: fixed; right: 0px; top: 0px;">
  
 </body>
</html>



