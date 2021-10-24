<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
} 
$OldMdpErr="";
$NewMdpErr="";
 if (isset($_POST['btnChangePassword'])) {
      include_once dirname(__FILE__) . '/Constants.php';

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
     
     function checkhashSSHA($salt, $password) {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }
                
        $stmt = $con->prepare('SELECT Password, salt FROM maison WHERE Maison_ID = 1');
                   if ($stmt->execute()){
        $stmt->bind_result($Password,$salt);
        while($stmt->fetch()){
        $user = array();
        $user["salt"] = $salt;
        $user["Password"] = $Password;
        }
            $hash = checkhashSSHA($salt, $_POST['pswd1']);
                      
            if (($Password == $hash)) {
                if($_POST['pswd2']==$_POST['pswd3']){
                    if(preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/", $_POST['pswd2'])){
        $hash = hashSSHA($_POST['pswd2']);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
 
       $stmt = $con->prepare("UPDATE `maison` SET `Password` =?,`salt` =?
             WHERE `maison`.`Maison_ID` = 1");
            $stmt->bind_param("ss",  $encrypted_password,$salt);
        if($stmt->execute()){
            session_destroy();
         // Redirecting To Home Page
         header("Location: index.php");}
                    }else{
                      $NewMdpErr="Mot de passe faible, il doit contenir au minimum 6 caractères dont au moins un chiffre, une lettre majuscule, une minuscule.";  
                    }
                }else{
                    $NewMdpErr="Mot de passe non identiques.";
                }
        }else {
                $OldMdpErr="Mot de passe incorrect.";
            }
                 
    }
 }
?>


<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modification du mot de passe</title>
    <link rel="shortcut icon" href="favicon.png">
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">  <!-- Pour les icones--> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="changePswdStyle.css"> 
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6">
                <div class="panel panel-default">
                <div class="panel-heading">   
                <h3 class="text-primary">Modification du mot de passe</h3></div>
                <div class="panel-body">
            
                    <form action="" method="post">
                        <div class="form-group">
                            <input type="password" name="pswd1" id="pswd1" value="" class="form-control" placeholder="Mot de passe actuel">
                        </div>
                        <div class="form-group">
                            <input type="password" name="pswd2" id="pswd2" value="" class="form-control" placeholder="Nouveau mot de passe">
                        </div>
                        <div class="form-group">
                            <input type="password" name="pswd3" id="pswd3" value="" class="form-control" placeholder="Confirmer le nouveau mot de passe">
                                <?php
                    if ($OldMdpErr != "") {
                        echo '<div class="alert alert-danger"><strong>Erreur: </strong> ' . $OldMdpErr . '</div>';
                    }
                    if ($NewMdpErr != "") {
                        echo '<div class="alert alert-danger"><strong>Erreur: </strong> ' . $NewMdpErr . '</div>';
                    }
                    ?>
                        </div>
                        <div class="form-group">
                            <input type="submit" id="changer" name="btnChangePassword" class="btn btn-primary" value="Confirmer"/>
                            <input type="button" id="retour" class="btn btn-default" onclick="location.href='profil.php';" value="Retour vers le profil"/>
                        </div>
                      
                    </form>
                </div>
                </div>
        </div>
    </div>
</div>
</body>
</html>
