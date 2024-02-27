<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>           
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../css/insertAnswer.css">
        <?php 
            include 'addAnswer.php';
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../question.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-tips">
                    <select name="sltDifficulty" required>
                            <option value="" selected disabled>DIFFICOLTÀ</option>    
                            <option value="BASSO">BASSO</option>
                            <option value="MEDIO">MEDIO</option>
                            <option value="ALTO">ALTO</option>
                    </select>
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

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnAddAnswer'])) {
                $txtAnswer = $_POST['txtAnswer'];

                /* funzione che sovrascrive il placeholder della textarea tips, per visualizzare la domanda in questione, durante la stesura delle risposte collegate */
                printQuestion($_SESSION['txtQuestion']);

                /* inserimento della risposta fornita rispetto alla domanda in questione, fornendo tipo e id della domanda */
                addAnswer($conn, strtoupper($_SESSION['typeQuestion']), $_SESSION['idCurrentQuestion'], $txtAnswer);
            } elseif(isset($_POST['btnAddOption'])) {
                $values = $_POST['btnAddOption'];

                $tokens = explode('?', $values);
                
                $_SESSION['idCurrentQuestion'] = $tokens[0];
                $_SESSION['typeQuestion'] = getTypeQuestion($conn, $tokens[0]);
                $_SESSION['txtQuestion']  = $tokens[1];

                /* funzione che sovrascrive il placeholder della textarea tips, per visualizzare la domanda in questione, durante la stesura delle risposte collegate */
                printQuestion($_SESSION['txtQuestion']);
            }
        }

        function printQuestion($textQuestion) {
            echo "<script>document.querySelector('.input-tips').value=".json_encode("QUESITO: $textQuestion").";</script>";
        }

        closeConnection($conn);
    ?>
</html>