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
	$_SESSION["current_page"] = 1;
    $_SESSION['expiring_on'] = time() + 3600;
  } else if(isset($_SESSION["loggedin"])) {
    $text = "";
	$reply_to = "";
	$idcomm = null;
    try {
		if(isset($_GET["like"])) {
		  $stmt1 = $conn->prepare("SELECT User FROM " . (isset($_GET["comm"]) ?  "LikesComm" : "LikesPosts") . " WHERE User = '" . $_SESSION["user"] . "'" . (isset($_GET["comm"]) ?  "AND IDComment = '" . $_GET["comm"] . "'" : " AND IDPost = '" . $_GET["post"] . "'") . ";");
		  $stmt1->execute();
		  $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
		  if(isset($result1["User"])) {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec("DELETE FROM " . (isset($_GET["comm"]) ?  "LikesComm" : "LikesPosts") . " WHERE User = '" . $_SESSION["user"] . "'" . (isset($_GET["comm"]) ?  " AND IDComment = '" . $_GET["comm"] . "'" :  "AND IDPost = '" . $_GET["post"] . "'") . ";");
		  } else {
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec("INSERT INTO " . (isset($_GET["comm"]) ?  "LikesComm" : "LikesPosts") . " (User" . (isset($_GET["comm"]) ?  ", IDComment" : ", IDPost") . ") VALUES('" . $_SESSION["user"] . "'" . (isset($_GET["comm"]) ?  ", '" . $_GET["comm"] . "'" : ", '" . $_GET["post"] . "'") . ");");
		  }
		  if(isset($_GET["comm"])) {
		   header("Location: http://localhost/commentpost.php?post=" . $_GET["post"] . "#comm" . $_GET["comm"]);
           exit();		   
		  }
		}
		if(isset($_POST["comment"])) {//iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
		  if(isset($_POST["idcomm"])) {
	        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $conn->exec("UPDATE Comments SET Text = '" . addslashes(trim($_POST["Text"])) . "' WHERE IDComment = " . $_POST["idcomm"] . ";");
		  } else {
		    if(trim($_POST["Text"]) != "") {
		      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		      $conn->exec("INSERT INTO Comments (User, IDPost, Text" . (isset($_POST["comm"]) ?  ", IDComm" : "") . ") VALUES ('" . $_SESSION["user"] . "', " . $_GET["post"] . ", '" . addslashes(trim($_POST["Text"])) . "'" . (isset($_POST["comm"]) ?  ", " . $_POST["comm"] : "") . ")");
		    } else {
		      $text = "Comment cannot be empty!";
			  $reply_to = $_POST["comm"];
		    }
		  }
		}//ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
		if(isset($_GET["reply_to"])) {
		  $reply_to = $_GET["reply_to"];
		}
		if(isset($_GET["act"])) {
		  if(isset($_GET["comm"])) {//iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
		    $stmt2 = $conn->prepare("SELECT User FROM Comments WHERE IDPost = '" . $_GET["post"] . "' AND IDComment = '" . $_GET["comm"] . "';");
		    $stmt2->execute();
		    $result2 = $stmt2->fetch(\PDO::FETCH_ASSOC);
		    if($_GET["act"] == "edit" && $_SESSION["user"] == $result2["User"]) {
			  $stmt1 = $conn->prepare("SELECT Text FROM Comments WHERE IDComment = '" . $_GET["comm"] . "';");
		      $stmt1->execute();
		      $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
			  $text = test_input($result1["Text"]);
			  $reply_to = $result1["IDComm"];
			  $idcomm = $_GET["comm"];
		    } else if($_GET["act"] == "del" && $_SESSION["user"] == $result2["User"]) {
			  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			  $conn->exec("DELETE FROM Comments WHERE IDComment = '" . $_GET["comm"] . "';");
			  header("Location: http://localhost/commentpost.php?post=" . $_GET["post"]);
			  exit();
		    }//ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
		  } else {
		    $stmt2 = $conn->prepare("SELECT User FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
		    $stmt2->execute();
		    $result2 = $stmt2->fetch(\PDO::FETCH_ASSOC);
		    if($_GET["act"] == "edit" && $_SESSION["user"] == $result2["User"]) {
			  header("Location: http://localhost/newpost.php?post=" . $_GET["post"]);
			  exit();
		    } else if($_GET["act"] == "del" && $_SESSION["user"] == $result2["User"]) {
		      $stmt1 = $conn->prepare("SELECT Image FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
		      $stmt1->execute();
		      $result1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
		      if (!unlink($result1["Image"])) {  
                echo "Post cannot be deleted due to an error";  
              }
			  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			  $conn->exec("DELETE FROM Posts WHERE IDPost = '" . $_GET["post"] . "';");
			  header("Location: http://localhost/index.php");
			  exit();
		    }
		  }
		}
	}
	catch(PDOException $e) {
      echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">An error occurred: " . $e->getMessage() . "</p>";
    }
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
  
  if(isset($_GET["post"])) {
	
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
		echo "<a href='newpost.php' style=\"position: fixed; top: 30px; right: 65px;\">New Post</a>";
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
	?>
  </div>
  
  <div style="position: absolute; top: 60px; left: 8vw; width: 80vw; word-wrap: break-word;">
    <?php
	  if(!isset($_GET["post"])) {
	    echo 'Please select a post from the main page: <a href="index.php"><b>Generic Image Site</b></a>';
	  } else {
	   try {
        $stmt = $conn->prepare("SELECT IDPost, User, Time, Image, Text, (SELECT COUNT(*) FROM LikesPosts WHERE IDPost = p.IDPost) AS LikesNumber, (SELECT COUNT(*) FROM Comments WHERE IDPost = p.IDPost) AS CommentsNumber FROM Posts p" . (isset($_GET["post"]) ? " WHERE IDPost = '" . $_GET["post"] . "'" : "") . ";");
	    $stmt->execute();
	    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
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
		         ' . ($row["User"] == $_SESSION["user"] ? '<a href="commentpost.php?act=edit&post=' . $row["IDPost"] . '" title="Edit"><img src="Edit.png" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 6vh;"></a>
			                                               <img src="Del.png" title="Delete" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 1vh; cursor: pointer;" onclick="if (confirm(\'Are you sure you want to delete this post?\')) {window.location.assign(\'commentpost.php?act=del&post=' . $row["IDPost"] . '\')}">' : '') . '
		         <b><a href="index.php?user=' . $row["User"] . '" title="' . $row["User"] . '" style="color: black;">' . $row["User"] . '</a></b><br>
			     On ' . substr($row["Time"], 8, 3) . month(substr($row["Time"], 5, 2)) . " " . substr($row["Time"], 0, 4) . ", at " . substr($row["Time"], 11, 5) . '<br><br>
			     ' . test_input($row["Text"]) . '<br><br>
			     <img src="' . $row["Image"] . '" style="width: 60vw; height: auto; border: 1px solid #a0a0a0;"><br><br>
			     <a ' . (isset($_SESSION["loggedin"]) ? 'href="commentpost.php?post=' . $_GET["post"] . '&like=1"' : 'title="Login to like posts"') . ' style="text-decoration: none; color: ' . (isset($_SESSION["loggedin"]) ?($icon === "Liked" ? "#ffe0e0" : "black") : "lightgray") . ';">
			      <span style="border: 1px solid #d0d0d0; padding: 5px 5px 1px 5px; width: 24vh; font-size: 5.3vh; font-family: Arial, Helvetica, sans-serif;">
			  	   <img src="' . $icon . '.png" style="width: 4.5vh; height: 4vh;">
				   ' . ($icon === "Liked" ? "LIKED" : "LIKE") . '(' . $row["LikesNumber"] . ')' . '
				  </span>
			     </a>
			     <a href="commentpost.php?post=' . $row["IDPost"] . '" style="text-decoration: none; color: black; margin-left: 2vw;">
			      <span style="border: 1px solid #d0d0d0; padding: 5px 5px 1px 5px; width: 18vh; font-size: 5.3vh; font-family: Arial, Helvetica, sans-serif;">
				   <img src="Comment.png" style="width: 4.5vh; height: 4vh;">
				   COMMENT(' . $row["CommentsNumber"] . ')
			      </span><br><br>
			     </a>
			    </div>';
	   } catch(PDOException $e) {
          echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">An error occurred: " . $e->getMessage() . "</p>";
       }
	  }
	?>
	
   <div style="position: relative; margin: 6px; padding-top: 0.8vh; border: 2px solid lightgray; background-color: white;">
	<?php //comentarii
	  if(isset($_GET["post"])) {
	  try {//iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
	    //afisare comentarii din bd pt. postarea asta si formular de adaugat comment
		$stmt = $conn->prepare("SELECT IDComment, User, Text, (SELECT COUNT(*) FROM LikesComm WHERE IDComment = c.IDComment) AS LikesNumber, (SELECT COUNT(*) FROM Comments WHERE IDPost = c.IDPost AND IDComm = c.IDComment) AS CommentsNumber FROM Comments c WHERE IDPost = '" . $_GET["post"] . "' AND IDComm IS NULL;");
	    $stmt->execute();
	    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
	    foreach($result as $row) {
		  if(isset($_SESSION["loggedin"])) {
		    $stmt1 = $conn->prepare("SELECT User FROM LikesComm WHERE User = '" . $_SESSION["user"] . "' AND IDComment = '" . $row["IDComment"] . "';");
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
		  echo '<div id="comm' . $row["IDComment"] . '" style="position: relative; margin: 6px; padding: 0px 2vw; padding-top: 0.8vh; background-color: white;">
		         ' . ($row["User"] == $_SESSION["user"] ? '<a href="commentpost.php?post=' . $_GET["post"] . '&act=edit&comm=' . $row["IDComment"] . '#write_comm" title="Edit"><img src="Edit.png" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 6vh;"></a>
			                                               <img src="Del.png" title="Delete" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 1vh; cursor: pointer;" onclick="if (confirm(\'Are you sure you want to delete this post?\')) {window.location.assign(\'commentpost.php?post=' . $_GET["post"] . '&act=del&comm=' . $row["IDComment"] . '\')}">' : '') . '
		         <b><a href="index.php?user=' . $row["User"] . '" title="' . $row["User"] . '" style="color: black;">' . $row["User"] . '</a></b>
			     <p style="margin: 1.5vh 0px;">' . test_input($row["Text"]) . '</p>
			     <a ' . (isset($_SESSION["loggedin"]) ? 'href="commentpost.php?post=' . $_GET["post"] . '&like=1&comm=' . $row["IDComment"] . '"' : 'title="Login to like comments"') . ' style="color: ' . (isset($_SESSION["loggedin"]) ? ($icon === "Liked" ? "#ffe0e0" : "black") : "lightgray") . ';">
				   ' . ($icon === "Liked" ? "Liked" : "Like") . '(' . $row["LikesNumber"] . ')' . '
			     </a>
			     <a ' . (isset($_SESSION["loggedin"]) ? 'href="commentpost.php?post=' . $_GET["post"] . '&reply_to=' . $row["IDComment"] . '#comm' . $row["IDComment"] . '"' : 'title="Login to reply to comments"') . ' style="margin-left: 2vw; color: ' . (isset($_SESSION["loggedin"]) ? "black" : "lightgray") . ';">
				   Reply(' . $row["CommentsNumber"] . ')
			     </a><br>
			    </div>';
		  //reply-uri (rely-uri pe un singur nivel)
		  if(isset($_SESSION["loggedin"]) && isset($_GET["reply_to"])) {//iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
			if($_GET["reply_to"] == $row["IDComment"]) {
			  $reply_to = $_GET["reply_to"];
			  echo '<div style="position: relative; margin: 20px; padding: 0px 2vw; padding-top: 0.8vh; background-color: white;">';
			  echo '<b><a href="index.php?user=' . $_SESSION["user"] . '" title="' . $_SESSION["user"] . '" style="color: black;">' . $_SESSION["user"] . '</a></b><br><br>
				    <form method="POST" action="commentpost.php?post=' . $_GET["post"] . '">
				    <input type="hidden" name="comm" value="' . $reply_to . '">
				     <textarea name="Text" id="Text" rows="8" style="width: 60vw;" maxlength="200">';
			  echo $text;
			  echo '</textarea><br>
				    <input type="submit" value="Reply" name="comment">
				   </form>
				  </div>';
		    }
		  }//ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
		  $stmt1 = $conn->prepare("SELECT IDComment, User, Text, (SELECT COUNT(*) FROM LikesComm WHERE IDPost = c.IDPost AND IDComment = c.IDComment) AS LikesNumber FROM Comments c WHERE IDPost = '" . $_GET["post"] . "' AND IDComm = '" . $row["IDComment"] . "';");
	      $stmt1->execute();
	      $result1 = $stmt1->fetchAll(\PDO::FETCH_ASSOC);
	      foreach($result1 as $row1) {
		    if(isset($_SESSION["loggedin"])) {
		      $stmt2 = $conn->prepare("SELECT User FROM LikesComm WHERE User = '" . $_SESSION["user"] . "' AND IDComment = '" . $row1["IDComment"] . "';");
	          $stmt2->execute();
	          $result2 = $stmt2->fetch(\PDO::FETCH_ASSOC);
		      if(isset($result2["User"])) {
		        $icon = "Liked";
		      } else {
		        $icon = "GiveALike";
		      }
		    } else {
		      $icon = "GiveALike";
		    }
		    echo '<div id="comm' . $row1["IDComment"] . '" style="position: relative; margin-left: 3vw; padding: 0px 15px; padding-top: 1.5vh; background-color: white; border-left: 1px solid lightgray;">
		         ' . ($row1["User"] == $_SESSION["user"] ? '<a href="commentpost.php?post=' . $_GET["post"] . '&act=edit&comm=' . $row1["IDComment"] . '#write_comm" title="Edit"><img src="Edit.png" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 6vh;"></a>
			                                               <img src="Del.png" title="Delete" style="width: 4vh; height: auto; position: absolute; top: 0.5vh; right: 1vh; cursor: pointer;" onclick="if (confirm(\'Are you sure you want to delete this post?\')) {window.location.assign(\'commentpost.php?post=' . $_GET["post"] . '&act=del&comm=' . $row1["IDComment"] . '\')}">' : '') . '
		         <b><a href="index.php?user=' . $row1["User"] . '" title="' . $row1["User"] . '" style="color: black;">' . $row1["User"] . '</a></b>
			     <p style="margin: 1.5vh 0px;">' . test_input($row1["Text"]) . '</p>
			     <a ' . (isset($_SESSION["loggedin"]) ? 'href="commentpost.php?post=' . $_GET["post"] . '&like=1&comm=' . $row1["IDComment"] . '"' : 'title="Login to like comments"') . ' style="color: ' . (isset($_SESSION["loggedin"]) ?($icon === "Liked" ? "#ffe0e0" : "black") : "lightgray") . ';">
				   ' . ($icon === "Liked" ? "Liked" : "Like") . '(' . $row1["LikesNumber"] . ')' . '
			     </a><br>
			    </div>';
		  }
		}//fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
	  } catch(PDOException $e) {
        echo "<p style=\"position: fixed; bottom: -5px; right: 8px;\">An error occurred: " . $e->getMessage() . "</p>";
      }
	  }
	  if(isset($_SESSION["loggedin"]) && !isset($_GET["reply_to"])) {//iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
	    echo '<div id="write_comm" style="position: relative; margin: 6px 2vw; padding: 0px 6px; padding-top: 0.8vh; background-color: white; border-left: 3px solid gray; border-radius: 4px;">';
	    echo '<b><a href="index.php?user=' . $_SESSION["user"] . '" title="' . $_SESSION["user"] . '" style="color: black;">' . $_SESSION["user"] . '</a></b><br><br>
	          <form method="POST" action="commentpost.php?post=' . $_GET["post"] . '">
			   ' . (isset($idcomm) ? '<input type="hidden" name="idcomm" value="' . $idcomm . '">' : '') . '
	           <textarea name="Text" id="Text" rows="8" style="width: 60vw;" maxlength="200">';
	    echo $text;
        echo '</textarea><br>
              <input type="submit" value="Comment" name="comment" style="margin-bottom: 5px;">
             </form>
	        </div>';
      }//ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff
    ?>
   </div>
  </div>
  
 </body>
</html>



