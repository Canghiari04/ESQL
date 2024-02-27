<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/specific_linear.css">        
        <?php
            include '../../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
        <form action="../insert/insertRowForm.php" method="POST">
            <button class="button-navbar-form" type="submit" name="btnInsertForm">Insert Form</button>
        </form>
        <form action="../insert/insertRowQuery.php" method="POST">
            <button class="button-navbar-query" type="submit" name="btnInsertQuery">Insert Query</button>
        </form>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <div>
            <?php 
                //VARIARE INTERNAMENTE DISPOSIZIONE DEL CODICE, AFFINCHE POSSA STAMPARE INDIPENDENTEMENTE DAL BTN
                $conn = openConnection();
                
                //DOVREBBE BASTARE COSI DATO CHE NON DA ERRORE
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if(isset($_POST['btnViewRow'])) {
                        $idTable = $_POST['btnViewRow'];    
                        $_SESSION['idCurrentTable'] = $idTable;   
                                                
                        $nameTable = getTableName($conn, $idTable);
                        $resultNameAttributes = getAttributesNames($conn, $idTable);
                        $result = getValues($conn, $nameTable);

                        $attributes = array();   

                        echo '
                            <div class="div-th"> 
                                <table class="table-head">   
                                    <tr>';  
                                        while($rowAttributes = $resultNameAttributes -> fetch(PDO::FETCH_OBJ)) {
                                            array_push($attributes, $rowAttributes -> NOME);
                                            
                                            echo '<th>'.$rowAttributes -> NOME.'</th>';
                                        }
                        echo '
                                    </tr>
                                </table>
                            </div>
                        ';
        
                        if(isset($result)) {
                            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                                echo '
                                    <div class="div-td">
                                        <table class="table-list">   
                                            <tr>';
                                                foreach($attributes as $value){
                                                    $attributeName = $value;
                                                    echo '<th>'.$row -> $attributeName .'</th>';                                                            
                                                }
                                echo '
                                            </tr>
                                        </table>
                                    </div>
                                ';
                            }
                        }                
                    }
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
    <?php
        function getTableName($conn, $idTable){
            $sql= 'SELECT NOME FROM Tabella_Esercizio WHERE ID = :idTabella;';

            try{
                $result = $conn -> prepare($sql);

                $result -> bindValue(':idTabella', $idTable);
                
                $result -> execute();
                $row = $result -> fetch(PDO::FETCH_OBJ);
            }catch(PDOException $e){
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            return $row -> NOME;
        }

        function getValues($conn, $nameTable) {
            $sql = 'SELECT * FROM '.$nameTable.';';
                
            try {
                $result = $conn -> prepare($sql);
                
                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione: '.$e -> getMessage().'<br>'; 
            }
            
            return $result;
        }

        function getAttributesNames($conn, $idTable){
            $sql = 'SELECT NOME FROM Attributo WHERE (ID_TABELLA = :idTabella);';  
                        
            try{
                $resultNameAttributes = $conn -> prepare($sql);
                
                $resultNameAttributes -> bindValue(':idTabella', $idTable);
                
                $resultNameAttributes -> execute(); 
            } catch(PDOException $e){
                echo 'Eccezione: '.$e -> getMessage().'<br>'; 
            }

            return $resultNameAttributes;
        }
    ?>
</html>