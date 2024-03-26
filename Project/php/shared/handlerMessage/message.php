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
        <link rel="stylesheet" type="text/css" href="../../style/css/table_view_linear.css">
    </head>
    <body>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnUndo"])) {
                    /* metodi acquisiscono tramite il POST la tipologia dell'utente */
                    buildNavbar($_POST["btnUndo"]);        
                    buildMessageTest($conn, $_POST["btnUndo"]);
                }
            } else {
                /* tramite l'url viene acquisita la tipologia dell'utente, in maniera tale da compiere il corretto build delle pagine */
                $url = $_SERVER["REQUEST_URI"];
                $tokens = explode('?', $url);

                buildNavbar($tokens[1]);        
                buildMessageTest($conn, $tokens[1]);
            }
            
            closeConnection($conn);
        ?>       
    </body>
</html>