<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <style>
            <?php
                include 'css/specifics.css';
            ?>
        </style>
    </head>
    <body>
        <form>
            <div>
                <?php 
                    include 'connectionDB.php';
                    $conn = openConnection();

                    if ($_SERVER["REQUEST_METHOD"] == "GET") {   
                        if(isset($_GET["btnSpecificTable"])) {
                            buildNavbar("table_exercise");
                            
                            $idTable = $_GET["btnSpecificTable"];                         
                            $sql = "SELECT Attributo.TIPO, Attributo.NOME, Attributo.CHIAVE_PRIMARIA FROM Tabella_Esercizio JOIN Attributo ON (Tabella_Esercizio.ID=Attributo.ID_TABELLA) WHERE (Tabella_Esercizio.ID=:idTable)";
                            
                            try {
                                $result = $conn -> prepare($sql);
                                $result -> bindValue(':idTable', $idTable);
                                $result -> execute();
                                
                                echo '
                                    <div class="div-th"> 
                                        <table class="table-head">   
                                            <tr>  
                                                <th>Nome</th>
                                                <th>Tipo</th>
                                                <th>Chiave primaria</th>
                                            </tr>
                                        </table>
                                    </div>
                                ';

                                if($result) {
                                    while($row = $result->fetch(PDO::FETCH_OBJ)) {
                                        /* metodo che restituisce se l'attributo visualizzato costituisca o meno la chiave primaria dellla tabella */
                                        $primaryKey = convertPrimaryKey($row -> CHIAVE_PRIMARIA);

                                        echo '
                                            <div class="div-td">
                                                <table class="table-list">   
                                                    <tr>  
                                                    <th>'.$row -> NOME.'</th>
                                                        <th>'.$row -> TIPO.'</th>
                                                        <th>'.$primaryKey.'</th>
                                                    </tr>
                                                </table>
                                            </div>
                                        ';
                                    }
                                }
                            } catch (PDOException $e) {
                                echo 'Eccezione: '. $e -> getMessage(); 
                            }
                        } elseif (isset($_GET["btnSpecificQuestion"])) {
                            buildNavbar("question");
                            
                            $idQuestion = $_GET["btnSpecificQuestion"];
                            $sql = "SELECT O.ID, O.TESTO FROM Quesito AS Q, Domanda_Chiusa AS DC, Opzione_Risposta AS O WHERE (Q.ID=DC.ID_DOMANDA_CHIUSA) AND (DC.ID_DOMANDA_CHIUSA=O.ID_DOMANDA_CHIUSA) AND (O.ID_DOMANDA_CHIUSA=:idQuestion)";
                            
                            try {
                                $result = $conn -> prepare($sql);
                                $result -> bindValue(":idQuestion", $idQuestion);
                                $result -> execute();
                                
                                if($result) {
                                    while($row = $result->fetch(PDO::FETCH_OBJ)) {
                                        echo '
                                            <div class="div-Question">
                                                <table>   
                                                    <tr>  
                                                        <th>'.$row -> TESTO.'</th>
                                                    </tr>
                                                </table>
                                            </div>
                                        ';
                                    }
                                }
                            } catch (PDOException $e) {
                                echo 'Eccezione: '. $e -> getMessage();
                            }
                        }
                    }

                    closeConnection($conn);
                ?>
            </div>
        </form>
    </body>
</html>

<?php
    /* funzione per la costruzione della navbar, in modo tale che risulti adattiva rispetto alla pagina .php chiamante */
    function buildNavbar($value){
        echo '
            <div class="navbar">
                <a><img class="zoom-on-img ESQL" width="112" height="48" src="img/ESQL.png"></a>
                <a href="'.$value.'.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
            </div>
        ';
    }

    function convertPrimaryKey($value) {
        if($value == 0) {
            return "No";
        } else {
            return "Si";
        }
    }
?>