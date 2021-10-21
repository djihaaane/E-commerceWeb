
<?php
session_start();
ob_start();
if (isset($_SESSION['loggedin'])) {
    header('Location: index.php?dashboard');
    exit;
}else{
 $idfErr="";
            //Including the constants.php file to get the database constants
      include_once dirname(__FILE__) . './includes/db.php';

        //connecting to mysql database
        $con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

        //Checking if any error occured while connecting
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
       function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
     function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }

   if(isset($_POST["submit"])) { 
if (empty($_POST["username"]) or empty($_POST["password"])){
    $idfErr="Champ(s) vide(s)";
}else{
            $stmt = $con->prepare('SELECT admin_email, password_admin, salt_admin FROM admins WHERE admin_email = ?');
                  // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
                  $stmt->bind_param('s', $_POST['username']);
                   if ($stmt->execute()){
        $stmt->bind_result($Username, $Password,$salt);
        while($stmt->fetch()){
        $user = array();
        $user["salt_admin"] = $salt;
        $user["email_admin"] = $Username;
        $user["password_admin"] = $Password;
        $user["test"] = true;
        }
        // verifying user password
    
            $hash = checkhashSSHA($salt, $_POST['password']);
           echo $hash;
           echo $_POST['password'];
             // push single product into final response array
            // check for password equality
            if ($Password == $hash) {
        // Verification success! User has loggedin!
        // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['admin_email'] = $_POST['username'];
        $_SESSION['admin_id'] = $id;
      header('Location: index.php?dashboard'); 
                exit;

            } else {
        $idfErr="Identifiants incorrects!"; 
    }
                      $stmt->close();
}else {
     $idfErr="Identifiants incorrects"; 
}
}
}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Login</title>
    <link rel="shortcut icon" href="favicon.png">
	<link rel="stylesheet" href="logstyle.css">
</head>
<body>
<div class="header">
<h1>Admin Login</h1> 
    <HR size="4px" color=#1a0f91 align=center width="80%"></HR>
</div>
<form class="box" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<input type="text" name="username" class="inputs" id="username" autocomplete="off" placeholder="E-mail Address" onfocus="this.placeholder=''" onblur="this.placeholder='Nom d&rsquo;utilisateur'">
<input type="password" name="password" class="inputs" id="password" placeholder="Password" onfocus="this.placeholder=''" onblur="this.placeholder='Mot de passe'">

<input type="submit" class="btnclass" name="submit" value="Log In">
 </form>
</body>
</html>