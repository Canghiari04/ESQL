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

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddTable"])) {
                $sql = strtoupper($_POST["txtAddTable"]);

                /* suddivisione della query nei token principali, per ottenere il nome della tabella di riferimento */
                $tokens = explode(" ", $sql);

                try {
                    $result = $conn -> prepare($sql);

                    /* creazione della tabella effettiva */
                    $result -> execute();

                    /* inserimento di tutti i dati all'interno delle collezioni di meta-dati */
                    insertTableExercise($conn, $tokens[2]);

                    /* inserimento dei record all'interno delle tabelle meta-dati */
                    insertRecord($conn, $sql, $tokens[2]);
                } catch(PDOException $e) {
                    echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> 
                    getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";
                    echo "<script>document.querySelector('.input-text').value=".json_encode($sql).";</script>";
                }
            }
        }

        /* funzione per rendere la navbar adattiva rispetto alla pagina php chiamante */
        function buildNavbar() {
            echo '
                <div class="navbar">
                    <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
                    <a href="../php/table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
                </div>
            ';
            buildForm();
        }

        function buildForm() {
            echo '
                <form action="" method="POST">
                    <div class="container">
                        <div class="div-tips">
                            <textarea class="input-tips" disabled></textarea>
                        </div>
                        <div class="div-query">
                            <textarea class="input-text" disabled></textarea>
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