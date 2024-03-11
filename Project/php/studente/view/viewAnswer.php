<?php
    session_start();

    if(!isset($_SESSION["emailStudente"])) {
        header("Location: ../../login/login.php");
    }
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
                <a href="viewTest.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
            </div>
            <?php
                include "../handlerData/buildForm.php";
                include "../handlerData/dataTest.php";
                include "../../connectionDB.php";
                
                $conn = openConnection();  
                
                if(isset($_SERVER["REQUEST_METHOD"])) {
                    if(isset($_POST["btnViewRisposte"])) {
                        $_SESSION["titleTest"] = $_POST["btnViewRisposte"];

                        /* visualizzazione delle risposte date dallo studente per il test selezionato */
                        buildFormAnswer($conn, $_SESSION["emailStudente"], $_SESSION["titleTest"]);
                    } elseif(isset($_POST["btnUndo"])) {
                        buildFormAnswer($conn, $_SESSION["emailStudente"], $_SESSION["titleTest"]);
                    }
                }
                
                function buildFormAnswer($conn, $email, $titleTest) {
                    /* acquisizione di tutte le risposte date dallo studente rispetto ai quesiti appartenenti al test */
                    $result = getAnswerTest($conn, $email, $titleTest); 

                    if(isset($result)) {
                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                            if(getTypeQuestion($conn, $row -> ID_QUESITO, $titleTest) == "CHIUSA") { 
                                buildFormCheck($conn, $row -> ID_QUESITO, $titleTest, false, false);
                            } else {
                                buildFormQuery($conn, $row -> ID_QUESITO, $titleTest, false, false);
                            }
                        }
                    }

                    /* reindirizzamento alla pagina contenente l'insieme delle soluzioni al test */
                    buildButtonSolution($conn, $_SESSION["emailStudente"], $titleTest);
                }
                
                closeConnection($conn);
            ?>    
        </div>
    </body>
</html>