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
                <form action="viewAnswer.php" method="POST">
                    <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
                </form>
            </div>
            <?php
                include "../handlerData/buildForm.php";
                include "../handlerData/dataTest.php";
                include "../../connectionDB.php";
                
                session_start();
                $conn = openConnection();  
                
                if(isset($_SERVER["REQUEST_METHOD"])) {
                    if(isset($_POST["btnViewSolution"])) {
                        buildFormSolution($conn, $_SESSION["titleTest"]);
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
                
                closeConnection($conn);
            ?>    
        </div>
    </body>
</html>