<?php
    include "addQuestion.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();
    $manager = openConnectionMongoDB();

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
        <link rel="stylesheet" type="text/css" href="../../style/css/insertQuestion.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <form action="../question.php" method="POST">
                <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-select">
                    <select name="sltDifficulty" required>
                        <option value="" selected disabled>DIFFICOLTÀ</option>    
                        <option value="BASSO">BASSO</option>
                        <option value="MEDIO">MEDIO</option>
                        <option value="ALTO">ALTO</option>
                    </select>
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox-question" type="text" name="txtDescription" placeholder="TESTO DELLA DOMANDA" required></textarea>
                </div>
            </div>
            <button type="submit" name="btnAddQuestion">Add</button>
        </form>
    </body>
    <?php 
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnInsertQuestion"])) {
                $_SESSION["typeQuestion"] = $_POST["btnInsertQuestion"]; // inizializzazione del campo della sessione sulla tipologia del quesito in evidenza 
            } elseif(isset($_POST["btnAddQuestion"])) {
                if(checkTable($conn, $_SESSION["emailDocente"])) {
                    if(checkNumberRows($conn, $_SESSION["emailDocente"])) {
                        insertQuestion($conn, $manager, $_SESSION["typeQuestion"], getLastId($conn, $_SESSION["titleCurrentTest"]), $_SESSION["titleCurrentTest"], $_POST["sltDifficulty"], 0, $_POST["txtDescription"]);
                        header("Location: insertAfferent.php");
                        exit();
                    } else {
                        echo "<script type='text/javascript'>alert(".json_encode("Nessuna tabella ha record al suo interno, inserisci qualche dato prima di creare dei quesiti.").");</script>";
                    }
                } else {
                    echo "<script type='text/javascript'>alert(".json_encode("Nessuna tabella rilevata, inserisci qualche collezione prima di creare dei quesiti.").");</script>";
                }
            }
        }
        
        closeConnection($conn);
    ?>
</html>