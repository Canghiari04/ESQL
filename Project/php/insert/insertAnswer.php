<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include '../css/insertAnswer.css';
            ?>
        </style>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-tips">
                    <textarea class="input-tips" disabled></textarea>
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox" type="text" name="txtAnswer" required></textarea>
                </div>
            </div>
            <button class="button-insert" type="submit" name="btnAddRisposta">Add</button>
        </form>
    </body>
    <?php 
        include 'addAnswer.php';
        include '../connectionDB.php';
        
        $conn = openConnection();

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddRisposta"])) {
                if(checkNumAnswer($conn, $_SESSION["typeQuestion"])) {
                    echo 'cambio pagina';
                } else {
                    //Qui dovrei fare l'insert della risposta alla domanda
                    echo 'rimango nella pagina';
                    $textAnswer = $_POST["txtAnswer"];
                }
            }
        }

        closeConnection($conn);
    ?>
</html>