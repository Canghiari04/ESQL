<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>           
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/insertAnswer.css">
        <?php 
            include 'addAnswer.php';
            include '../connectionDB.php';
        ?>
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
                    <textarea class="input-textbox" type="text" name="txtAnswer" placeholder="TESTO DELLA RISPOSTA" required></textarea>
                </div>
            </div>
            <button class="button-insert" type="submit" name="btnAddAnswer">Add</button>
        </form>
    </body>
    <?php 
        $conn = openConnection();

        /* funzione che sovrascrive il placeholder della textarea tips, per visualizzare la domanda in questione, durante la stesura delle risposte collegate */
        printQuestion($conn, $_SESSION['idCurrentQuestion'], $_SESSION["txtQuestion"]);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnAddAnswer'])) {
                $txtAnswer = $_POST['txtAnswer'];

                /* inserimento della risposta fornita rispetto alla domanda in questione, fornendo tipo e id della domanda */
                addAnswer($conn, strtoupper($_SESSION['typeQuestion']), $_SESSION['idCurrentQuestion'], $txtAnswer);

                /* controllo che il numero di risposte fornito sia coerente rispetto al numero atteso dalla domanda; in caso affermativo reindirizzamento alla main page */
                if(checkNumAnswer($conn, strtoupper(($_SESSION['typeQuestion'])), $_SESSION['idCurrentQuestion'])) {
                    header('Location: insertAfferent.php');
                    exit;
                }
            }
        }

        function printQuestion($conn, $idQuestion, $textQuestion) {
            echo "<script>document.querySelector('.input-tips').value=".json_encode("QUESITO: ".$textQuestion."\r\n\r\nNUMERO DI RISPOSTE ATTESE: ".getNumberAnswers($conn, $idQuestion)).";</script>";
        }

        closeConnection($conn);
    ?>
</html>