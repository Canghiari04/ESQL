<?php
    session_start();

    if ((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: login/login.php");
    } 

    include "../connectionDB.php";

    /* tramite l'url viene acquisita la tipologia dell'utente, in maniera tale da compiere il corretto reindirizzamento tra i file */
    $url = $_SERVER["REQUEST_URI"];
    $tokens = explode('?', $url);
    $typeUser = $tokens[1];

    function getUndo($typeUser) {
        if((isset($_SESSION["emailDocente"])) AND ($typeUser == "Teacher")) {
            echo ' 
                <a href="../docente/handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a> 
            ';
        } elseif((isset($_SESSION["emailStudente"])) AND ($typeUser == "Student")) {
            echo ' 
                <a href="../studente/handlerStudente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a> 
            ';
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../style/css/statistic.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../style/img/ESQL.png"></a>
            <?php getUndo($typeUser) ?>
        </div>
        <div class="container">
            <?php 
                $conn = openConnection();

                $sql = "SELECT * FROM Test_Completati;";
                        
                try {
                    $result = $conn -> prepare($sql);

                    $result -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione: ".$e -> getMessage()."<br>"; 
                }
                            
                echo '
                    <div class="div-th"> 
                        <h2>Classifica studenti in base al numero di test completati</h2>
                        <table class="table-head">   
                            <tr> 
                                <th>Posizione</th>
                                <th>Studente</th>
                                <th>Test completati</th>
                            </tr>
                        </table>
                    </div>
                ';
                            
                if(isset($result)) {
                    $cont = 1;
                    while($row = $result->fetch(PDO::FETCH_OBJ) and $cont < 11) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">   
                                    <tr>  
                                        <th>'.$cont.'</th>  
                                        <th>'.$row -> CODICE.'</th>
                                        <th>'.$row -> NUMERO.'</th>
                                    </tr>
                                </table>
                            </div>
                        ';

                        $cont = $cont + 1;
                    }
                }

                closeConnection($conn);
            ?>
        </div>
        <div>
            <?php 
                $conn = openConnection();

                $sql = "SELECT * FROM Risposte_Corrette;";
                        
                try {
                    $result = $conn -> prepare($sql);

                    $result -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione: ".$e -> getMessage()."<br>"; 
                }
                            
                echo '
                    <div class="div-th">                   
                        <h2>Classifica studenti in base al numero di risposte corrette</h2>
                        <table class="table-head">   
                            <tr>  
                                <th>Posizione</th>
                                <th>Studente</th>
                                <th>Risposte corrette</th>                  
                                <th>Risposte completate</th>
                                <th>Percentuale</th>
                            </tr>
                        </table>
                    </div>
                ';
                            
                if(isset($result)) {
                    $cont = 1;
                    while($row = $result->fetch(PDO::FETCH_OBJ) and $cont < 11) {
                        $perc = ($row -> PERC) * 100;
                        
                        echo '
                            <div class="div-td">                            
                                <table class="table-list">   
                                    <tr>  
                                        <th>'.$cont.'</th>  
                                        <th>'.$row -> CODICE.'</th>
                                        <th>'.$row -> NUMEROCORR.'</th>
                                        <th>'.$row -> NUMERORIS.'</th>
                                        <th>'.$perc.'%</th>
                                    </tr>
                                </table>
                            </div>
                        ';

                        $cont = $cont + 1;
                    }
                }
                
                closeConnection($conn);
            ?>
        </div>
        <div>
            <?php 
                $conn = openConnection();

                $sql = "SELECT * FROM Risposte_Inserite;";
                        
                try {
                    $result = $conn -> prepare($sql);

                    $result -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione: ".$e -> getMessage()."<br>"; 
                }
                            
                echo '
                    <div class="div-th"> 
                        <h2>Classifica dei questiti in base al numero di risposte inserite</h2>
                        <table class="table-head">   
                            <tr>  
                                <th>Posizione</th>
                                <th>Quesito</th>
                                <th>Risposte</th>                  
                            </tr>
                        </table>
                    </div>
                ';
                            
                if(isset($result)) {
                    $cont = 1;
                    while($row = $result->fetch(PDO::FETCH_OBJ) and $cont < 11) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">   
                                    <tr>
                                        <th>'.$cont.'</th>  
                                        <th>'.$row -> ID_QUESITO.'</th>
                                        <th>'.$row -> NUMERO.'</th>
                                    </tr>
                                </table>
                            </div>
                        ';

                        $cont = $cont + 1;
                    }
                }

                closeConnection($conn);
            ?>
        </div>
    </body>
</html>