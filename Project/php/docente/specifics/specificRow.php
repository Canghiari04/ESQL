<?php
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();
    
    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/specific_linear.css">        
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
        <form action="../insert/insertRowForm.php" method="POST">
            <button class="button-navbar-first" type="submit" name="btnInsertForm">Insert Form</button>
        </form>
        <form action="../insert/insertRowQuery.php" method="POST">
            <button class="button-navbar-second" type="submit" name="btnInsertQuery">Insert Query</button>
        </form>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <div>
            <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if(isset($_POST["btnViewRow"])) {
                        /* campo della sessione utilizzato per visualizzare correttamente le specifiche qualora l'evento undo sia elaborato */
                        $_SESSION["idCurrentTable"] = $_POST["btnViewRow"];   

                        buildSpecificsTable($conn, $_POST["btnViewRow"]);
                    } elseif(isset($_POST["btnUndo"])) {
                        buildSpecificsTable($conn, $_SESSION["idCurrentTable"]);
                    }
                } 
                /* stampa delle specifiche dopo eliminazione di record, per permettere la visualizzazione della navbar */
                elseif(isset($_SESSION["recordDeleted"])) {    
                    unset($_SESSION["recordDeleted"]);
                    buildSpecificsTable($conn, $_SESSION["idCurrentTable"]);
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
    <?php
        function buildSpecificsTable($conn, $idTable) {
            $nameAttributes = array();   
            $nameTable = getTableName($conn, $idTable);

            $arrayNamePrimaryKey = getNamePrimaryKey($conn, $nameTable);
            $resultNames = getAttributesNames($conn, $idTable);
            $resultValues = getValues($conn, $nameTable);

            echo '
                <div class="container">
                    <div class="div-table"> 
                        <table>   
                            <tr>
            ';  

            while($rowNames = $resultNames -> fetch(PDO::FETCH_OBJ)) {
                echo '<th>'.$rowNames -> NOME.'</th>';

                /* nel vettore sono memorizzati i nomi degli attributi */
                array_push($nameAttributes, $rowNames -> NOME);
            }

            echo '
                    <th>DELETE</th>
                </tr>
            ';

            $numRows = $resultValues -> rowCount();
            if($numRows > 0) {
                while($rowValues = $resultValues -> fetch(PDO::FETCH_OBJ)) {
                    echo '<tr>';

                    /* ciclo attuato sui nomi degli attributi, in maniera tale da concordare l'estrapolazione dei dati rispetto all'oggetto PDO contenente i record della tabella */
                    foreach($nameAttributes as $name) {
                        echo '<th>'.$rowValues -> $name.'</th>';                                                            
                    }

                    echo '<th>';

                    $valuePrimaryKey = "";
                    foreach($arrayNamePrimaryKey as $namePrimaryKey) {
                        $valuePrimaryKey = $valuePrimaryKey."".$rowValues -> $namePrimaryKey."|?|";
                    }
                    
                    echo "
                        <form action='../delete/deleteTable.php' method='POST'>
                            <button class='button-image' type='submit' name='btnDeleteRecord' value='".$nameTable.'|?|'.$valuePrimaryKey."'>
                                <img class='delete-img' width='24' height='24' src='../../style/img/delete.png'>
                            </button>
                        </form>
                    ";

                    echo '
                            </th>
                        </tr>
                    ';
                }
            }               
            
            echo '
                        </table>
                    </div>
                </div>
            ';
        }

        /* funzione utilizzata per acquisire il nome della tabella esercizio */
        function getTableName($conn, $idTable){
            $sql= "SELECT NOME FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=:idTabella);";

            try{
                $result = $conn -> prepare($sql);
                $result -> bindValue(":idTabella", $idTable);
                
                $result -> execute();
            }catch(PDOException $e){
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
            
            $row = $result -> fetch(PDO::FETCH_OBJ);
            return $row -> NOME;
        }

        /* funzione contenitrice i field che compongono la chiave primaria della tabella */
        function getNamePrimaryKey($conn, $nameTable) {
            $arrayNamePrimaryKey = array();

            $sql = "SHOW KEYS FROM ".$nameTable.";";

            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()." <br>";
            }

            $numRows = $result -> rowCount();
            if($numRows > 0) {    
                while($row = $result -> fetch(PDO::FETCH_ASSOC)) {
                    array_push($arrayNamePrimaryKey, $row["Column_name"]);
                } 
            }

            return $arrayNamePrimaryKey;
        }

        /* funzione attuata per estrapolare i nomi di tutti i field che compongono la tabella */
        function getAttributesNames($conn, $idTable){
            $sql = "SELECT NOME FROM Attributo WHERE (Attributo.ID_TABELLA=:idTabella);";  
                        
            try{
                $result = $conn -> prepare($sql);
                $result -> bindValue(":idTabella", $idTable);
                
                $result -> execute(); 
            } catch(PDOException $e){
                echo "Eccezione: ".$e -> getMessage()."<br>"; 
            }

            return $result;
        }

        /* ottenuto il nome della tabella, sono estrapolati tutti i dati che contiene */
        function getValues($conn, $nameTable) {
            $sql = "SELECT * FROM ".$nameTable.";";
                
            try {
                $result = $conn -> prepare($sql);
                
                $result -> execute();
            } catch (PDOException $e) {
                echo "Eccezione: ".$e -> getMessage()."<br>"; 
            }
            
            return $result;
        }
    ?>
</html>