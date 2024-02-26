<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/navbar_button_dropdown_undo.css">
        <link rel="stylesheet" type="text/css" href="../css/specific_linear.css">        
        <?php
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../img/ESQL.png"></a>
            <div class="dropdown">
                <button class="dropbtn">Insert</button>
                <div class="dropdown-content">
                    <a href="../insert/insertRowForm.php">Form</a>
                    <a href="../insert/insertRowQuery.php">Query</a>
                </div>
            </div>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
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

                        $sql = 'SELECT * FROM '.$nameTable.'';
                
                        try {
                            $result = $conn -> prepare($sql);
                            
                            $result -> execute();
                        } catch (PDOException $e) {
                            echo 'Eccezione: '.$e -> getMessage().'<br>'; 
                        }

                        $sql = 'SELECT NOME FROM Attributo WHERE (ID_TABELLA = :idTabella);';  
                        
                        try{
                            $resultAttributes = $conn -> prepare($sql);
                            
                            $resultAttributes -> bindValue(':idTabella', $idTable);
                            
                            $resultAttributes -> execute(); 
                        } catch(PDOException $e){
                            echo 'Eccezione: '.$e -> getMessage().'<br>'; 
                        }
                        
                        $attributes = array();   

                        echo '
                            <div class="div-th"> 
                                <table class="table-head">   
                                    <tr>';  
                                        while($rowAttributes = $resultAttributes -> fetch(PDO::FETCH_OBJ)) {
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
                        closeConnection($conn);
                    }
                }
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
    ?>
</html>