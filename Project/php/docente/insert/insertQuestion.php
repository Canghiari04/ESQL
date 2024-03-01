<?php
    session_start();
    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../login/login.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insertQuestion.css">
        <?php 
            include 'addQuestion.php';
            include '../../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../question.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
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
        $conn = openConnection();

        /* attraverso l'url viene estrapolata la tipologia di domanda, da cui ne scaturisce un successivo inserimento all'interno dell'apposita tabella */
        $url = $_SERVER['REQUEST_URI'];
        $str = explode('?', $url);
        $type = $str[1];

        /* viene salvato il tipo della domanda tramite la sessione, dati i controlli successivi inerenti all'inserimento */
        $_SESSION['typeQuestion'] = $type;
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnAddQuestion'])) {
                if(checkTable($conn, $_SESSION['emailDocente'])) {
                    $difficulty = $_POST['sltDifficulty'];
                    $description = $_POST['txtDescription'];
                    $numAnswers = 0;
                    
                    $_SESSION['txtQuestion'] = $description;
                    
                    insertQuestion($conn, $type, $difficulty, $numAnswers, $description);
                    header('Location: insertAfferent.php');
                    exit;
                } else {
                    echo "<script>document.querySelector('.input-textbox-question').value=".json_encode("NESSUNA TABELLA PRESENTE, INSERISCI QUALCHE COLLEZIONE PRIMA DI CREARE DEI QUESITI").";</script>";
                }
            }
        }
        
        closeConnection($conn);
    ?>
</html>