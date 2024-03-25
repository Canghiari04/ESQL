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
        if(isset($_POST["btnViewSolution"])) {
            $values = $_POST["btnViewSolution"];
            $tokens = explode("|?|", $values);

            $_SESSION["titleTestSolution"] = $tokens[0];
            $_SESSION["namePageSolution"] = $tokens[1];

            buildPage($conn, $tokens[1], $tokens[0]);
        } elseif(isset($_POST["btnUndo"])) {
            buildPage($conn, $_SESSION["namePageSolution"], $_SESSION["titleTestSolution"]);
        }
    }

    function buildPage($conn, $namePage, $titleTest) {
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

        buildButtonPhoto($conn, "../view/viewSolution.php");
        buildButtonUndo($namePage);

        echo '
            </div>
        ';

        buildFormSolution($conn, $titleTest);
        
        echo '
                    </div>
                </body>
            </html>
        ';
    }    

    function buildFormSolution($conn, $titleTest) {
        /* acquisizione di tutti i quesiti appartenenti al test */
        $arrayIdQuestion = getQuestionTest($conn, $titleTest);

        foreach($arrayIdQuestion as $i) {
            /* stampa del form del quesito a seconda della tipologia */
            if(getTypeQuestion($conn, $i, $titleTest) == "CHIUSA") { 
                buildFormCheck($conn, $i, $titleTest, null, null, false, true);
            } else {
                buildFormQuery($conn, $i, $titleTest, null, null, false, true);
            }
        }
    }

    closeConnection($conn);
?>
