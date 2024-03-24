<?php
    include "addRow.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();

    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>           
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insertRow.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <form action="../specifics/specificRow.php" method="POST">
                <button class="button-image" name="btnUndo" type="submit"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-tips"> 
                    <textarea class="input-tips" disabled></textarea>
                </div>
                <div class="div-textbox-generative">
                    <?php              
                        if($_SERVER["REQUEST_METHOD"] == "POST") {
                            if(isset($_POST["btnInsertForm"])) {
                                /* build del form di inserimento */
                                identifyAttributes($conn);
                            } elseif(isset($_POST["btnInsertData"])) {
                                /* inserimento dei dati ottenuti da input all'interno della tabella */
                                insertData($conn);

                                /* rebuild del form per nuovi inserimenti all'interno della collezione */
                                identifyAttributes($conn);
                            }
                        }

                        closeConnection($conn);
                    ?>
                </div>  
            </div>
            <button class="button-data" name="btnInsertData">Add</button>  
        </form>
    </body>
</html>