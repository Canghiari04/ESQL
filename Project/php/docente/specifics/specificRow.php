<?php
    session_start();
    
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
        <?php
            include "../../connectionDB.php";
        ?>
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
                $conn = openConnection();
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if(isset($_POST["btnViewRow"])) {
                        $idTable = $_POST["btnViewRow"];    
                        $_SESSION["idCurrentTable"] = $idTable;   

                        buildSpecificsTable($conn, $idTable);
                    } elseif(isset($_POST["btnUndo"])) {
                        buildSpecificsTable($conn, $_SESSION["idCurrentTable"]);
                    }
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
    <?php
        function buildSpecificsTable($conn, $idTable) {
            /* array che conterrà i nomi di tutti i field */
            $nameAttributes = array();   

            /* oggetto PDO contenente tutti i nomi degli attributi della tabella */
            $resultNames = getAttributesNames($conn, $idTable);

            /* oggetto PDO contenente tutti i record della tabella */
            $resultValues = getValues($conn, getTableName($conn, $idTable));

            echo '
                <div class="div-th"> 
                    <table class="table-head">   
                        <tr>
            ';  

            while($rowNames = $resultNames -> fetch(PDO::FETCH_OBJ)) {
                echo '<th>'.$rowNames -> NOME.'</th>';

                /* nel vettore sono salvati i nomi degli attributi */
                array_push($nameAttributes, $rowNames -> NOME);
            }

            echo '
                        </tr>
                    </table>
                </div>
            ';

            $numRows = $resultValues -> rowCount();
            if($numRows > 0) {
                while($rowValues = $resultValues -> fetch(PDO::FETCH_OBJ)) {
                    echo '
                        <div class="div-td">
                            <table class="table-list">   
                                <tr>
                    ';

                    /* ciclo attuato sui nomi degli attributi, in maniera tale da concordare l'estrapolazione dei dati rispetto all'oggetto PDO contenente i record della tabella */
                    foreach($nameAttributes as $name){
                        echo '<th>'.$rowValues -> $name.'</th>';                                                            
                    }

                    echo '
                                </tr>
                            </table>
                        </div>
                    ';
                }
            }                
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

        /* ottenuto il nome della tabella esercizio, sono individuati tutti i record che la contraddistinguono */
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
    ?>
</html>