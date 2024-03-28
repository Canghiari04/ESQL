<?php
    include "../connectionDB.php";
    
    session_start();
    $conn = openConnection();

    if((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: login/login.php");
    } 

    $url = $_SERVER["REQUEST_URI"]; // acquisito tramite URL la tipologia dell'utente, in modo tale da compiere il correttamento reindirizzamento
    $tokens = explode('?', $url);
    $typeUser = $tokens[1];

    function getUndo($typeUser) {
        if((isset($_SESSION["emailDocente"])) AND ($typeUser == "Teacher")) {
            echo '<a href="../docente/handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>';
        } elseif((isset($_SESSION["emailStudente"])) AND ($typeUser == "Student")) {
            echo '<a href="../studente/handlerStudente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>';
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
            <?php
                getUndo($typeUser) 
            ?>
        </div>
        <div class="container">
            <div class="div-statistic">
                <div class="single-div">
                    <?php 
                        $sql = "SELECT * FROM Test_Completati;";
                                
                        try {
                            $result = $conn -> prepare($sql);

                            $result -> execute();
                        } catch(PDOException $e) {
                            echo "Eccezione: ".$e -> getMessage()."<br>"; 
                        }
                                    
                        echo '
                            <h2>Classifica studenti in base al numero di test completati</h2>
                            <table>   
                                <tr> 
                                    <th>POSIZIONE</th>
                                    <th>STUDENTE</th>
                                    <th>TEST COMPLETATI</th>
                                </tr>
                        ';
                                    
                        $numRows = $result -> rowCount();
                        if($numRows > 0) {
                            $cont = 1;

                            while($row = $result->fetch(PDO::FETCH_OBJ) and $cont < 11) {
                                echo '  
                                    <tr>  
                                        <th>'.$cont.'</th>  
                                        <th>'.$row -> CODICE.'</th>
                                        <th>'.$row -> NUMERO.'</th>
                                    </tr>
                                ';

                                $cont++;
                            }

                            echo '</table>';
                        }

                        closeConnection($conn);
                    ?>
                </div>
                <div class="single-div">
                    <?php 
                        $conn = openConnection();

                        $sql = "SELECT * FROM Risposte_Corrette;";
                                
                        try {
                            $result = $conn -> prepare($sql);

                            $result -> execute();
                        } catch(PDOException $e) {
                            echo "Eccezione: ".$e -> getMessage()."<br>"; 
                        }
                                    
                        echo '
                                <h2>Classifica studenti in base al numero di risposte corrette</h2>
                                <table class="table-head">   
                                    <tr>  
                                        <th>POSIZIONE</th>
                                        <th>STUDENTE</th>
                                        <th>RISPOSTE CORRETTE</th>                  
                                        <th>RISPOSTE COMPLETATE</th>
                                        <th>PERCENTUALE</th>
                                    </tr>
                        ';
                                    
                        $numRows = $result -> rowCount();
                        if($numRows > 0) {
                            $cont = 1;

                            while($row = $result->fetch(PDO::FETCH_OBJ) and $cont < 11) {
                                $perc = ($row -> PERC) * 100;
                                
                                echo '
                                    <tr>  
                                        <th>'.$cont.'</th>  
                                        <th>'.$row -> CODICE.'</th>
                                        <th>'.$row -> NUMEROCORR.'</th>
                                        <th>'.$row -> NUMERORIS.'</th>
                                        <th>'.$perc.'%</th>
                                    </tr>
                                ';

                                $cont++;
                            }

                            echo '</table>';
                        }
                        
                        closeConnection($conn);
                    ?>
                </div>
                <div class="single-div">
                    <?php 
                        $sql = "SELECT * FROM Risposte_Inserite;";
                                
                        try {
                            $result = $conn -> prepare($sql);

                            $result -> execute();
                        } catch(PDOException $e) {
                            echo "Eccezione: ".$e -> getMessage()."<br>"; 
                        }
                                    
                        echo '
                            <h2>Classifica dei questiti in base al numero di risposte inserite</h2>
                            <table class="table-head">   
                                <tr>  
                                    <th>POSIZIONE</th>
                                    <th>QUESITO</th>
                                    <th>RISPOSTE</th>                  
                                </tr>
                        ';
                                    
                        $numRows = $result -> rowCount();
                        if($numRows > 0) {
                            $cont = 1;

                            while($row = $result->fetch(PDO::FETCH_OBJ) and $cont < 11) {
                                echo '
                                    <tr>
                                        <th>'.$cont.'</th>  
                                        <th>'.$row -> ID_QUESITO.'</th>
                                        <th>'.$row -> NUMERO.'</th>
                                    </tr>
                                ';

                                $cont++;
                            }

                            echo '</table>';
                        }

                        closeConnection($conn);
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>