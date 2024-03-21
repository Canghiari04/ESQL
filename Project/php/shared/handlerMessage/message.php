<?php
    session_start();    

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
        <link rel="stylesheet" type="text/css" href="../../style/css/table_view_linear.css">
    </head>
    <body>
        <?php
            include "buildFormMessage.php";
            include "../../connectionDB.php";

            $conn = openConnection();

            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnUndo"])) {
                    $typeUser = $_POST["btnUndo"];

                    buildNavbar($typeUser);        
                    buildMessageTest($conn, $typeUser);
                }
            } else {
                /* tramite l'url viene acquisita la tipologia dell'utente, in maniera tale da compiere il corretto reindirizzamento tra i file */
                $url = $_SERVER["REQUEST_URI"];
                $tokens = explode('?', $url);
                $typeUser = $tokens[1];

                buildNavbar($typeUser);        
                buildMessageTest($conn, $typeUser);
            }
            
            closeConnection($conn);
        ?>       
    </body>
</html>