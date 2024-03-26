<?php
    include "buildFormMessage.php";
    include "../../connectionDB.php";

    session_start();    
    $conn = openConnection();

    if ((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: ../login/login.php");
    } 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_messages.css">
    </head>
    <body>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnUndo"])) {
                    buildNavbar($_POST["btnUndo"]);        
                    buildMessageTest($conn, $_POST["btnUndo"]);
                }
            } else {
                $url = $_SERVER["REQUEST_URI"]; // acquisito tramite URL la tipologia dell'utente
                $tokens = explode('?', $url);

                buildNavbar($tokens[1]);        
                buildMessageTest($conn, $tokens[1]);
            }
            
            closeConnection($conn);
        ?>       
    </body>
</html>