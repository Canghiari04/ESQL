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
                <?php
                    include "../handlerData/buildForm.php";
                    include "../handlerData/dataTest.php";
                    include "../../connectionDB.php";
                    
                    $conn = openConnection();

                    buildButtonUndo($_SESSION["nameCallerPage"]);
                ?>
            </div>
            <?php
                if(isset($_SERVER["REQUEST_METHOD"])) {
                    if(isset($_POST["btnViewSolution"])) {
                        $titleTest = $_POST["btnViewSolution"];
                        
                        buildFormSolution($conn, $titleTest);
                    }
                }
                
                function buildFormSolution($conn, $titleTest) {
                    /* acquisizione di tutti i quesiti appartenenti al test */
                    $arrayIdQuestion = getQuestionTest($conn, $titleTest);
                    
                    foreach($arrayIdQuestion as $i) {
                        if(getTypeQuestion($conn, $i, $titleTest) == "CHIUSA") { 
                            buildFormCheck($conn, $i, $titleTest, false, true);
                        } else {
                            buildFormQuery($conn, $i, $titleTest, false, true);
                        }
                    }
                }

                function buildButtonUndo($namePage) {
                    echo '
                        <form action="'.$namePage.'" method="POST">
                            <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
                        </form>
                    ';
                }
                
                closeConnection($conn);
            ?>    
        </div>
    </body>
</html>