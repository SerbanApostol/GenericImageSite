<?php //parte pentru autentificare
  session_start();
  if (isset($_SESSION['expiring_on']) && time() > $_SESSION['expiring_on']) {
    //sesiunea a expirat
    session_unset();
	session_regenerate_id(true);
  }
  //delogarea
  if(isset($_GET["logout"])) {
    session_unset();
	session_regenerate_id(true);
    $_SESSION["user"] = null;
    $_SESSION["loggedin"] = null;
	$_SESSION["current_page"] = 1;
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
	$_SESSION["current_page"] = 1;
    $_SESSION['expiring_on'] = time() + 3600;
  } else if(isset($_SESSION["loggedin"])) {
    try {
		if(isset($_GET["like"])) {
		  $stmt1 = $conn->prepare("SELECT User FROM LikesPosts WHERE User = '" . $_SESSION["user"] . "' AND IDPost = '" . $_GET["like"] . "';");
		  $stmt1->execute();
		  $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
		  if(isset($result1["User"])) {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec("DELETE FROM LikesPosts WHERE User = '" . $_SESSION["user"] . "' AND IDPost = '" . $_GET["like"] . "';");
		  } else {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec("INSERT INTO LikesPosts (User, IDPost) VALUES('" . $_SESSION["user"] . "', '" . $_GET["like"] . "');");
		  }
		  header("Location: http://localhost/index.php#post" . $_GET["like"]);
		}
		if(isset($_GET["act"])) {
		  $stmt2 = $conn->prepare("SELECT User FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
		  $stmt2->execute();
		  $result2 = $stmt2->fetch(\PDO::FETCH_ASSOC);
		  if($_GET["act"] == "edit" && $_SESSION["user"] == $result2["User"]) {
			header("Location: http://localhost/newpost.php?post=" . $_GET["post"]);
			exit();
		  } else if($_GET["act"] == "del" && $_SESSION["user"] == result2["User"]) {
		    $stmt1 = $conn->prepare("SELECT Image FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
		    $stmt1->execute();
		    $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
		    if (!unlink($result1["Image"])) {  
              echo "Post cannot be deleted due to an error";  
            }
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec("DELETE FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
		  }
		}
	}
	catch(PDOException $e) {
      echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">An error occurred: " . $e->getMessage() . "</p>";
    }
  }
  if(isset($_GET["page"])) {
    $_SESSION["current_page"] = $_GET["page"];
  }
  $_SESSION['expiring_on'] = time() + 3600;
  
  function month($integer) {
    switch($integer) {
	  case 1: return "January"; break;
	  case 2: return "February"; break;
	  case 3: return "March"; break;
	  case 4: return "April"; break;
	  case 5: return "May"; break;
	  case 6: return "June"; break;
	  case 7: return "July"; break;
	  case 8: return "August"; break;
	  case 9: return "September"; break;
	  case 10: return "October"; break;
	  case 11: return "November"; break;
	  case 12: return "December"; break;
	  default: return ""; break;
	}
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
	body {
		background-color: #e0e0e0;
	}
  </style>
 </head>
 <body>
  
  <div style="position: fixed; top: 0px; left: 0px; width: 100vw; height: 60px; background-color: black; color: white; z-index: 20;">
   <a href='index.php?page=1' style="color: white;">
    <img src="Logo.png" style="width: 60px; height: 50px; position: fixed; top: 5px; left: 5px;" alt="GIS Logo">
    <span style="font-weight: bold; font-size: 40px; position: fixed; top: 5px; left: 70px;">Generic Image Site</span>
   </a>
    <?php //crearea unui buton de login sau link de delogare
	  if(!isset($_SESSION["loggedin"])) {
	    echo "<input type=\"button\" value=\"Login\" style=\"position: fixed; top: 10px; right: 8px;\" onClick=\"document.location.href='login.php'\" />";
	  } else {
	    echo "<p style=\"position: fixed; top: -5px; right: 8px;\">Hi, <a href=\"index.php?user=" . $_SESSION["user"] . "\">" . $_SESSION["user"] . "</a>!</p>";
	    echo "<a href='newpost.php' style=\"position: fixed; top: 30px; right: 65px;\">New Post</a>";
	    echo "<a href='index.php?logout=true' style=\"position: fixed; top: 30px; right: 9px;\">Logout</a>";
	  }
    ?>
  </div>
  
  <div style="position: absolute; top: 60px; left: 8vw; width: 80vw; word-wrap: break-word;">
    <?php
	  try {
      $stmt = $conn->prepare("SELECT IDPost, User, Time, Image, Text, (SELECT COUNT(*) FROM LikesPosts WHERE IDPost = p.IDPost) AS LikesNumber, (SELECT COUNT(*) FROM Comments WHERE IDPost = p.IDPost) AS CommentsNumber FROM Posts p" . (isset($_GET["user"]) ? " WHERE User = '" . $_GET["user"] . "'" : "") . " ORDER BY LikesNumber DESC, Time DESC LIMIT " . (($_SESSION["current_page"] - 1) * 50) . ", 50;");
	  $stmt->execute();
	  $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
	  foreach($result as $row) {
	    if(isset($_SESSION["loggedin"])) {
		  $stmt1 = $conn->prepare("SELECT User FROM LikesPosts WHERE User = '" . $_SESSION["user"] . "' AND IDPost = '" . $row["IDPost"] . "';");
	      $stmt1->execute();
	      $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
		  if(isset($result1["User"])) {
		    $icon = "Liked";
		  } else {
		    $icon = "GiveALike";
		  }
		} else {
		  $icon = "GiveALike";
		}
	    echo '<div id="post' . $row["IDPost"] . '" style="position: relative; margin: 6px; padding: 0px 2vw; padding-top: 0.8vh; border: 2px solid lightgray; background-color: white;">
		       ' . ($row["User"] == $_SESSION["user"] ? '<a href="index.php?act=edit&post=' . $row["IDPost"] . '" title="Edit"><img src="Edit.png" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 6vh;"></a>
			                                             <img src="Del.png" title="Delete" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 1vh; cursor: pointer;" onclick="if (confirm(\'Are you sure you want to delete this post?\')) {window.location.assign(\'index.php?act=del&post=' . $row["IDPost"] . '\')}">' : '') . '
		       <b><a href="index.php?user=' . $row["User"] . '" title="' . $row["User"] . '" style="color: black;">' . $row["User"] . '</a></b><br>
			   On ' . substr($row["Time"], 8, 3) . month(substr($row["Time"], 5, 2)) . " " . substr($row["Time"], 0, 4) . ", at " . substr($row["Time"], 11, 5) . '<br><br>
			   ' . test_input($row["Text"]) . '<br><br>
			   <img src="' . $row["Image"] . '" style="width: 60vw; height: auto; border: 1px solid #a0a0a0;"><br><br>
			   <a ' . (isset($_SESSION["loggedin"]) ? 'href="index.php?like=' . $row["IDPost"] . '"' : 'title="Login to like posts"') . ' style="text-decoration: none; color: ' . (isset($_SESSION["loggedin"]) ?($icon === "Liked" ? "#ffe0e0" : "black") : "lightgray") . ';">
			    <span style="border: 1px solid #d0d0d0; padding: 5px 5px 1px 5px; width: 24vh; font-size: 5.3vh; font-family: Arial, Helvetica, sans-serif;">
				 <img src="' . $icon . '.png" style="width: 4.5vh; height: 4vh;">
				 ' . ($icon === "Liked" ? "LIKED" : "LIKE") . '(' . $row["LikesNumber"] . ')' . '
				</span>
			   </a>
			   <a href="commentpost.php?post=' . $row["IDPost"] . '" style="text-decoration: none; color: black; margin-left: 2vw;">
			    <span style="border: 1px solid #d0d0d0; padding: 5px 5px 1px 5px; width: 18vh; font-size: 5.3vh; font-family: Arial, Helvetica, sans-serif;">
				 <img src="Comment.png" style="width: 4.5vh; height: 4vh;">
				 COMMENT(' . $row["CommentsNumber"] . ')
			    </span>
			   </a><br><br>
			  </div>';
	  }
	  } catch(PDOException $e) {
        echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">An error occurred: " . $e->getMessage() . "</p>";
      }
	?>
	
   <div style="position: relative; top: 10px;">
	<?php
	  try {
	  $stmt = $conn->prepare("SELECT COUNT(*) AS PostsNumber FROM Posts;");
	  $stmt->execute();
	  $result1 = $stmt->fetch(\PDO::FETCH_ASSOC);
	  $result = floor($result1["PostsNumber"] / 50) + (($result1["PostsNumber"] % 50 == 0) ? 0 : 1);
	  if($_SESSION["current_page"] != 1) {
		echo '<input type="button" value="First Page" onClick="window.location.assign(index.php?page=1);">&ensp;';
		echo '<input type="button" value="Prevoius Page" onClick="window.location.assign(index.php?page=' . ($_SESSION["current_page"] - 1) . ');">&ensp;';
	  }
	  if($_SESSION["current_page"] - 2 > 0) {
		echo '<a href="index.php?page=' . ($_SESSION["current_page"] - 2) . '/">' . ($_SESSION["current_page"] - 2) . '</a>&ensp;';
	  }
	  if($_SESSION["current_page"] - 1 > 0) {
		echo '<a href="index.php?page=' . ($_SESSION["current_page"] - 1) . '/">' . ($_SESSION["current_page"] - 1) . '</a>&ensp;';
	  }
	  echo '<a href="index.php?page=' . $_SESSION["current_page"] . '/">' . $_SESSION["current_page"] . '</a>';
	  if($_SESSION["current_page"] < $result) {
		echo '&ensp;<a href="index.php?page=' . ($_SESSION["current_page"] + 1) . '/">' . ($_SESSION["current_page"] + 1) . '</a>';
	  }
	  if($_SESSION["current_page"] + 1 < $result) {
		echo '&ensp;<a href="index.php?page=' . ($_SESSION["current_page"] + 2) . '/">' . ($_SESSION["current_page"] + 2) . '</a>';
	  }
	  if($_SESSION["current_page"] != $result) {
		echo '&ensp;<input type="button" value="Next Page" onClick="window.location.assign(index.php?page=' . ($_SESSION["current_page"] + 1) . ');">';
		echo '&ensp;<input type="button" value="Last Page" onClick="window.location.assign(index.php?page=' . $result . ');">';
	  } 
	  } catch(PDOException $e) {
        echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">An error occurred: " . $e->getMessage() . "</p>";
      }
	?>
   </div>
  </div>
  
 </body>
</html>



