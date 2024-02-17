<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <style>
        <?php 
            include 'addQuestion.php';
            include '../connectionDB.php';
            include '../css/insertQuestion.css';
        ?>
    </style>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../question.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-select">
                    <select name="sltDifficolta" required>
                        <option value="BASSO">BASSO</option>
                        <option value="MEDIO">MEDIO</option>
                        <option value="ALTO">ALTO</option>
                    </select>
                    <input type="number" name="txtNumeroRisposte" min="1" placeholder="NUMERO RISPOSTE" required>  
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox-question" type="text" name="txtDescrizione" placeholder="TESTO DELLA DOMANDA" required></textarea>
                </div>
            </div>
            <button type="submit" name="btnAddQuestion">Add</button>
        </form>
    </body>
    <?php 
        $conn = openConnection();

        /* attraverso l'url viene estrapolata la tipologia di domanda, da cui ne scaturisce un successivo inserimento all'interno dell'apposita tabella */
        $url = $_SERVER['REQUEST_URI'];
        $str = explode("?", $url);
        $type = $str[1];

        /* viene salvato il tipo della domanda tramite la sessione, dati i controlli successivi inerenti all'inserimento */
        $_SESSION["typeQuestion"] = $type;
        
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddQuestion"])) {
                if(checkTable($conn, $_SESSION['email'])) {
                    $difficulty = $_POST["sltDifficolta"];
                    $numAnswers = $_POST["txtNumeroRisposte"];
                    $description = $_POST["txtDescrizione"];
                    
                    $_SESSION["txtQuestion"] = $description;
                    
                    insertQuestion($conn, $type, $difficulty, $numAnswers, $description);
                    header("Location: insertAnswer.php");
                } else {
                    $errorQuestion = 'NESSUNA TABELLA PRESENTE, INSERISCI QUALCHE COLLEZIONE PRIMA DI CREARE DEI QUESITI';
                    echo "<script>document.querySelector('.input-textbox-question').value=".json_encode($errorQuestion).";</script>";
                }
            }
        }
        
        closeConnection($conn);
    ?>
</html>