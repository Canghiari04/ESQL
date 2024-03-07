<?php
    session_start();

    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../../login/login.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet"> 
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insertTable.css">
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
                    <textarea class="input-textbox" type="text" name="txtAddTable" required></textarea>
                </div>
            </div>
            <button class="button-insert" type="submit" name="btnAddTable">Add</button>
        </form>
        <?php 
            include "addTable.php";
            include "../../connectionDB.php";
            
            $conn = openConnection();
            $manager = openConnectionMongoDB();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddTable"])) {
                    $sql = strtoupper($_POST["txtAddTable"]);

                    /* controllo della presenza della key PRIMARY all'interno della query di creazione della tabella */
                    if(str_contains($sql, "PRIMARY")) {
                            /* suddivisione della query nei token principali, per ottenere il nome della tabella di riferimento */
                            $tokens = explode(' ', $sql);
                            
                            if($tokens[0] == "CREATE") {
                                $tokenName = explode('(', $tokens[2]);
                                
                                try {
                                    $result = $conn -> prepare($sql);
                                    
                                    /* creazione della tabella effettiva contenuta nello stesso DB, ESQLDB */
                                    $result -> execute();
                                    
                                    /* creazione della Tabella_Esercizio, contenente tutti i meta-dati */
                                    $emailTeacher = $_SESSION["emailDocente"];
                                    insertTableExercise($conn, $tokenName[0], $emailTeacher);
                                    
                                    /* inserimento dei record che compogono la tabella effettiva nelle corrispettive tabelle meta-dati, Attributo e Vincolo_Integrita*/
                                    insertRecord($conn, $sql, $tokenName[0]);

                                    /* scrittura log inserimento di una tabella all'interno della Tabella_Esercizio */
                                    $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento tabella: '.$tokenName[0].' dal docente: '.$emailTeacher.'', 'Timestamp' => date('Y-m-d H:i:s')];
                                    writeLog($manager, $document);
                                } catch(PDOException $e) {
                                    /* funzioni che rendono compatibili caratteri speciali rispetto agli script delle textarea, dovuto principalmente ad un uso spropositato di spaziature */
                                    echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";
                                    echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";
                                }
                            } else {
                                echo "<script>document.querySelector('.input-tips').value='SONO ACCETTATE SOLO QUERY CREATE';</script>";
                            }
                    } else {
                        echo "<script>document.querySelector('.input-tips').value='NESSUNA PRIMARY KEY RILEVATA';</script>";
                    }
                }
            }
            
            closeConnection($conn);
        ?>
    </body>
</html>