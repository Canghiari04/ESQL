<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/specifics.css">
        <?php
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <div>
            <?php 
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if(isset($_POST['btnViewRow'])) {
                        $conn = openConnection();
                            $idTable = $_POST['btnViewRow'];        
                            $nameTable = getTableName($conn,$idTable);
                            $sql = 'SELECT * FROM '.$nameTable.'';
                
                            try {
                                $result = $conn -> prepare($sql);
                                $result -> execute();

                                try{
                                    $sql = 'SELECT NOME FROM Attributo WHERE (ID_TABELLA=:idTable);';  
                                    $resultAttributes = $conn -> prepare($sql);
                                    $resultAttributes -> bindValue(':idTable', $idTable);
                                    $resultAttributes -> execute(); 

                                } catch(PDOException $e){
                                    echo 'Eccezione: '.$e -> getMessage().'<br>'; 
                                }

                            } catch (PDOException $e) {
                                echo 'Eccezione: '.$e -> getMessage().'<br>'; 
                            }
                            
                            $attributes=array();         
                            echo '
                                <div class="div-th"> 
                                    <table class="table-head">   
                                        <tr>';  

                                            while($rowAttributes = $resultAttributes->fetch(PDO::FETCH_OBJ)) {
                                                echo '<th>'.$rowAttributes->NOME.'</th>';
                                                array_push($attributes,$rowAttributes->NOME);
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
                                                        $attributeName= $value;
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
</html>


<?php
    function getTableName($conn,$idTable){
        $sql= 'SELECT NOME FROM TABELLA_ESERCIZIO WHERE ID = :idTable ';
        try{
            $result = $conn -> prepare($sql);           
            $result -> bindValue(':idTable', $idTable);
            $result -> execute();

            $row = $result -> fetch(PDO::FETCH_OBJ);
            $nameTable= $row -> NOME;
            return $nameTable;

        }catch(PDOException $e){
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }    
    }
?>