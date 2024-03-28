<?php
    include "addRow.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();
    $manager = openConnectionMongoDB();

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
        <link rel="stylesheet" type="text/css" href="../../style/css/insertRow.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <form action="../specifics/specificRow.php" method="POST">
                <button class="button-image" name="btnUndo" type="submit"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-tips">
                    <textarea class="input-tips" placeholder="SQLSTATE: ..." disabled></textarea>
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox" type="text" name="txtAddRow" required></textarea>
                </div>
            </div>
            <button class="button-insert" type="submit" name="btnAddData">Add</button>
        </form>
        <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddData"])) {
                    $sql = stroupper($_POST["txtAddRow"]);

                    $tokens = explode('(', trim($sql));
                    $tokensHeader = explode(' ', $tokens[0]); // acquisiti i token di intestazione

                    if($tokensHeader[0] == "INSERT") { // controllo che la query sia DDL e di nessun altro tipo
                        if($tokensHeader[2] == getTableName($conn)) { // controllo di equivalenza nominativa tra la tabella definita da query rispetto alle collezioni mantenute nel database
                            try {
                                $result = $conn -> prepare($sql);

                                $result -> execute();
                            } catch(PDOException $e) {
                                echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";    
                                echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";    
                            }

                            $rowInserted = $result -> rowCount();
                            for($i = 0; $i < ($rowInserted - 1); $i++){ 
                                $storedProcedure = "CALL Inserimento_Manipolazione_Riga(:idTabella);"; // inserimento fittizio all'interno della tabella Manipolazione_Riga, attuato per innescare i trigger del database
                                    
                                try {
                                    $stmt = $conn -> prepare($storedProcedure);
                                    $stmt -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
                                    
                                    $stmt -> execute();
                                } catch(PDOException $e) {
                                    echo "Eccezione ".$e -> getMessage()."<br>";
                                }
                            }
                        } else {
                            echo "<script>document.querySelector('.input-tips').value='INSERISCI I DATI PER LA TABELLA SELEZIONATA';</script>";    
                        }
                    } else {
                        echo "<script>document.querySelector('.input-tips').value='SONO ACCETTATE SOLO QUERY INSERT INTO';</script>";
                    }
                }
            }

            closeConnection($conn);
        ?>
    </body>
</html>