<?php 
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            <?php 
                include 'addRecord.php';
                include '../connectionDB.php';
                include '../css/insert.css';
            ?>
        </style>
    </head>
    <body>
        <?php 
            $conn = openConnection();

            buildNavbar();
            buildForm();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddTable"])) {
                    $sql = strtoupper($_POST["txtAddTable"]);

                    /* suddivisione della query nei token principali, per ottenere il nome della tabella di riferimento */
                    $tokens = explode(" ", $sql);

                    if($tokens[0] == "CREATE") {
                        $tokenName = explode("(", $tokens[2]);
                        
                        try {
                            $result = $conn -> prepare($sql);
                            
                            /* creazione della tabella effettiva contenuta nello stesso DB, ESQLDB */
                            $result -> execute();
                            
                            /* creazione della tabella di esercizio, contenente tutti i meta-dati */
                            $emailTeacher = $_SESSION["email"];
                            insertTableExercise($conn, $tokenName[0], $emailTeacher);
                            
                            /* inserimento dei record all'interno della tabella contenente meta-dati */
                            insertRecord($conn, $sql, $tokenName[0]);
                        } catch(PDOException $e) {
                            /* funzioni che rendono compatibili caratteri speciali rispetto allo script, dovuto principalmente ad un uso spropositato di spaziature */
                            echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";
                            echo "<script>document.querySelector('.input-textbox').value=".json_encode($sql).";</script>";
                        }
                    } else {
                        echo "<script>document.querySelector('.input-tips').value='Sono accettate solo query CREATE';</script>";
                    }
                }
            }

            function buildNavbar() {
                echo '
                    <div class="navbar">
                        <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
                        <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
                    </div>
                ';
            }

            function buildForm() {
                echo '
                    <form action="" method="POST">
                        <div class="container">
                            <div class="div-tips">
                                <textarea class="input-tips" disabled></textarea>
                            </div>
                            <div class="div-textbox">
                                <textarea class="input-textbox" type="text" name="txtAddTable" required></textarea>
                            </div>
                        </div>
                        <button class="button-insert" type="submit" name="btnAddTable">Add</button>
                    </form>
                ';
            }

            closeConnection($conn);
        ?>
    </body>
</html>