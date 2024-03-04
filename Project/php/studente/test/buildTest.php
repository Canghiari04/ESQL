<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_option.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_query.css">
        <?php 
            include 'startTest.php';      
            include '../../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../viewTest.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <div class="container">
            <form action="insertAnswer.php" method="POST">
                <?php
                    session_start();
                    $conn = openConnection();  

                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {   
                        if(isset($_POST['btnStartTest'])) {
                            $titleTest = $_POST['btnStartTest'];


                            openTest($conn, $_SESSION["emailStudente"], $titleTest);
                            buildPage($conn, $titleTest);                        
                        } elseif(isset($_POST["btnRestartTest"])){
                            $titleTest = $_POST["btnRestartTest"];

                            buildPage($conn, $titleTest);
                        }
                    }

                    function buildPage($conn, $titleTest) {
                        /* funzione restituente l'insieme degli id dei quesiti che compongono il test selezionato */
                        $result = getQuestionTest($conn, $titleTest);
        
                        if(isset($result)) {
                            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                                /* condizione necessaria per differenziare la visualizzazione dei quesiti */
                                if(getTypeQuestion($conn, $row -> ID, $titleTest) == "CHIUSA"){
                                    buildFormCheck($conn, $row -> ID, $titleTest);
                                } else {
                                    buildFormQuery($conn, $row -> ID, $titleTest);
                                }
                            }
                        }
                    }

                    closeConnection($conn);
                ?>
                <button type="submit" name="btnSaveExit">Exit</button>
                <button type="submit" name="btnSendTest">Send</button>
            </form>
        </div>
    </body>
</html>
