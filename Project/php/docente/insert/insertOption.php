<?php
    include "addOption.php";
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
        <link rel="stylesheet" type="text/css" href="../../style/css/insertAnswer.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <form action="../specifics/specificQuestion.php" method="POST">
                <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        </div>
        <?php    
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddOption"])) {
                    buildForm($conn, $_SESSION["typeQuestion"], $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"]); // costruzione del form per inserimento di risposte al quesito 
                    printQuestion($_SESSION["descriptionCurrentQuestion"]); // stampa all'interno della textarea dedicata del quesito in evidenza
                } elseif(isset($_POST["btnAddAnswer"])) {
                    addOption($conn, $manager, strtoupper($_SESSION["typeQuestion"]), getLastId($conn, strtoupper($_SESSION["typeQuestion"]), $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"]), $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"], strtoupper($_POST["txtAnswer"]), $_POST["sltSolution"]); // inserimento della nuova risposta all'interno del database
                    buildForm($conn, $_SESSION["typeQuestion"], $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"]);
                    printQuestion($_SESSION["descriptionCurrentQuestion"]);
                } 
            }   
        ?>
    </body>
    <?php 
        function printQuestion($textQuestion) {
            echo "<script>document.querySelector('.input-tips').value=".json_encode("QUESITO: $textQuestion").";</script>"; // metodo in grado di rendere compatibili caratteri speciali con la visualizzazione all'interno della textarea
        }

        function buildForm($conn, $type) {
            if($type == "CHIUSA") { // diversificazione del form visualizzato a seconda della tipologia
                echo '
                    <form action="" method="POST">
                        <div class="container">
                            <div class="div-tips">
                                <textarea class="input-tips" disabled></textarea>
                            </div>
                            <div class="div-textbox">
                                <textarea class="input-textbox" type="text" name="txtAnswer" placeholder="TESTO DELLA RISPOSTA" required></textarea>
                            </div>
                            <div class="div-select">    
                                <select name="sltSolution" required>
                                        <option value="" selected disabled>SOLUZIONE</option>    
                                        <option value="0">NO</option>
                                        <option value="1">SI</option>
                                </select>
                            </div>
                        </div>
                        <button class="button-insert" type="submit" name="btnAddAnswer">Add</button>
                    </form>
                ';
            } else {
                echo '
                    <form action="" method="POST">
                        <div class="container">
                            <div class="div-tips">
                                <textarea class="input-tips" disabled></textarea>
                            </div>
                            <div class="div-textbox">
                                <textarea class="input-textbox" type="text" name="txtAnswer" placeholder="TESTO DELLA RISPOSTA" required></textarea>
                            </div>
                            <div class="div-select">    
                                <select name="sltSolution" required>
                                        <option value="0" disabled>SOLUZIONE</option>  
                                        <option value="1">SI</option>  
                                </select>
                            </div>
                        </div>
                        <button class="button-insert" type="submit" name="btnAddAnswer">Add</button>
                    </form>
                ';
            }
            
        }

        closeConnection($conn);
    ?>
</html>