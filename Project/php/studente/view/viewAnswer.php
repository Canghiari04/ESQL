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
            buildFormAnswer($conn, $_SESSION["emailStudente"], $_SESSION["titleTest"]); // metodo ideato per visualizzare le risposte date dallo studente
        } elseif(isset($_POST["btnUndo"])) {
            buildNavbar($conn);
            buildFormAnswer($conn, $_SESSION["emailStudente"], $_SESSION["titleTest"]); // rebuild del form di visualizzazione qualora lo studente dovesse navigare tra le pagine
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
        $result = getAnswerTest($conn, $email, $titleTest); // funzione attuata per estrapolare tutte le risposte date dallo studente

        if(isset($result)) {
            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                if(getTypeQuestion($conn, $row -> ID_QUESITO, $titleTest) == "CHIUSA") { // diversificazione del form visualizzato a seconda della tipologia
                    buildFormCheck($conn, $row -> ID_QUESITO, $titleTest, false, false);
                } else {
                    buildFormQuery($conn, $row -> ID_QUESITO, $titleTest, null, null, false, false);
                }
            }
        }

        buildButtonSolution($conn, $_SESSION["emailStudente"], $titleTest); // metodo ideato per costruire il bottone che permette o meno la visualizzazione delle soluzioni

        echo '
                    </div>
                </body>
            </html>
        ';
    }
                
    closeConnection($conn);
?>    