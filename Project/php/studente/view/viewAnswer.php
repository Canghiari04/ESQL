<?php
    include "../handlerData/buildForm.php";
    include "../handlerData/check.php";
    include "../handlerData/dataTest.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();  

    if(!isset($_SESSION["emailStudente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }

    if(isset($_SERVER["REQUEST_METHOD"])) {
        if(isset($_POST["btnViewAnswer"])) {
            $_SESSION["titleTest"] = $_POST["btnViewAnswer"]; 

            buildNavbar($conn);

            /* visualizzazione delle risposte date dallo studente per il test selezionato */
            buildFormAnswer($conn, $_SESSION["emailStudente"], $_SESSION["titleTest"]);
        } elseif(isset($_POST["btnUndo"])) {
            buildNavbar($conn);

            /* rebuild del form, qualora lo studente dovesse navigare tra le differenti componenti */
            buildFormAnswer($conn, $_SESSION["emailStudente"], $_SESSION["titleTest"]);
        }
    }

    function buildNavbar($conn) {
        echo '
            <!DOCTYPE html>
            <html>
                <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
                    <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
                    <link rel="stylesheet" type="text/css" href="../../style/css/form_option.css">
                    <link rel="stylesheet" type="text/css" href="../../style/css/form_query.css">
                </head>
                <body>
                    <div class="container">
                        <div class="navbar">
                            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a> 
        ';

        buildButtonPhoto($conn, "../view/viewAnswer.php");

        echo '
                <a href="viewTest.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
            </div>
        ';
    }
                
    function buildFormAnswer($conn, $email, $titleTest) {
        /* acquisizione di tutte le risposte date dallo studente rispetto ai quesiti appartenenti al test */
        $result = getAnswerTest($conn, $email, $titleTest); 

        if(isset($result)) {
            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                /* in base alla tipologia di quesito è diversificata la visualizzazione del form */
                if(getTypeQuestion($conn, $row -> ID_QUESITO, $titleTest) == "CHIUSA") { 
                    buildFormCheck($conn, $row -> ID_QUESITO, $titleTest, null, null, false, false);
                } else {
                    buildFormQuery($conn, $row -> ID_QUESITO, $titleTest, null, null, false, false);
                }
            }
        }

        /* reindirizzamento alla pagina contenente le soluzioni del test */
        buildButtonSolution($conn, $_SESSION["emailStudente"], $titleTest);

        echo '
                    </div>
                </body>
            </html>
        ';
    }
                
    closeConnection($conn);
?>    