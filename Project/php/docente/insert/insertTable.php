<?php
    include "addTable.php";
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
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddTable"])) {
                    $sql = strtoupper($_POST["txtAddTable"]);

                    $tokens = explode(' ', $sql); // acquisiti i token per compiere i controlli del caso
                    if($tokens[0] == "CREATE") { // controllo che sia una query DDL e di nessun altro tipo
                        if(str_contains($sql, "PRIMARY")) { // controllo presenza di un dominio unique dato che mysql potrebbe permettere la creazione della tabella anche in sua assenza
                                $tokenName = explode('(', $tokens[2]);
                                
                                try {
                                    $result = $conn -> prepare($sql);
                                    
                                    $result -> execute(); // creazione della tabella effettiva solamente se rispettata la sintassi sql
                                    
                                    insertTableExercise($conn, $manager, $tokenName[0], $_SESSION["emailDocente"]); // inserimento nella tabella Tabella_Esercizio della nuova collezione
                                    insertRecord($conn, $manager, $sql, $tokenName[0]); // inserimento dei meta-dati che caratterizzano la tabella 

                                    $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento Tabella titolo: '.$tokenName[0].'. Creata dal Docente email: '.$_SESSION["emailDocente"].'', 'Timestamp' => date('Y-m-d H:i:s')];
                                    writeLog($manager, $document); // scrittura log inserimento di una tabella
                                } catch(PDOException $e) { // in caso di eccezioni della query CREATE sono visualizzate a schermo tramite la textarea
                                    echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>"; // metodo in grado di rendere compatibile caratteri speciali con la visualizzazione a schermo dell'eccezione
                                    echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";                                
                                }
                            } else {
                                echo "<script>document.querySelector('.input-tips').value='SQLSTATE: NESSUNA PRIMARY KEY RILEVATA';</script>";
                                echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";
                            }
                    } else {
                        echo "<script>document.querySelector('.input-tips').value='SQLSTATE: SONO ACCETTATE SOLO QUERY CREATE';</script>";
                        echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";
                    }
                }
            }
            
            closeConnection($conn);
        ?>
    </body>
</html>