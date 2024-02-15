<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include 'addQuestion.php';
                include '../connectionDB.php';
                include '../css/insertQuestion.css';
            ?>
        </style>
    </head>
    <?php 
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddDomanda"])) {
                $difficulty = $_POST["sltDifficolta"];
                $description = $_POST["txtDescrizione"];
                $numAnswers = $_POST["txtNumeroRisposte"];
                $nameTable = $_POST["sltNomeTabella"];

                insertRecord($conn, $type, $difficulty, $description, $numAnswers, $nameTable);
            }
        }
        
        closeConnection($conn);
    ?>
</html>