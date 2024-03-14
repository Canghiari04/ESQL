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
        <link rel="stylesheet" type="text/css" href="../../style/css/insertRow.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-tips">
                    <textarea class="input-tips" placeholder="SQLSTATE: POSSIBILI ERRORI DI SINTASSI" disabled></textarea>
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox" type="text" name="txtAddRow" required></textarea>
                </div>
            </div>
            <button class="button-insert" type="submit" name="btnAddData">Add</button>
        </form>
        <?php 
            include "addRow.php";
            include "../../connectionDB.php";
            
            $conn = openConnection();
            $manager = openConnectionMongoDB();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddData"])) {
                    $sql = $_POST["txtAddRow"];

                    $tokens = explode('(', trim($sql));
                    $tokensHeader = explode(' ', $tokens[0]);

                    /* controllo riferito a query di inserimento */
                    if($tokensHeader[0] == "INSERT") {

                        /* controllo di uguaglianza tra la tabella riferita da query rispetto alla collezione selezionata */
                        if($tokensHeader[2] == getTableName($conn)) {
                            try {
                                $result = $conn -> prepare($sql);

                                /* inserimento effettivo dei dati all'interno della tabella */
                                $result -> execute();

                                $rowInserted = $result ->rowCount();

                                for($i = 0; $i < $rowInserted; $i++){ 
                                    $storedProcedure = "CALL Inserimento_Manipolazione_Riga(:idTabella);";
                                    
                                    try {
                                        $stmt = $conn -> prepare($storedProcedure);
                                        $stmt -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
                                        
                                        $stmt -> execute();
                                    } catch(PDOException $e) {
                                        echo "Eccezione ".$e -> getMessage()."<br>";
                                    }
                                }


                            } catch(PDOException $e) {

                                echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";    
                                echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";    
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