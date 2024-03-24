<?php
    session_start();

    if(!isset($_SESSION["emailStudente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }

    include "../handlerData/buildForm.php";
    include "../../connectionDB.php";

    $conn = openConnection();

    if(isset($_SERVER["REQUEST_METHOD"])) {
        if(isset($_POST["btnPhotoTest"])) {
            $namePage = $_POST["btnPhotoTest"];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
    </head>
    <body>
        <div class="container">
            <div class="navbar">
                <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
                <?php
                    /* costruzione del bottone undo dinamica */
                    buildButtonUndo($namePage);
                ?>
            </div>
            <?php
                    }
                }

                closeConnection($conn);
            ?>    
        </div>
    </body>
</html>