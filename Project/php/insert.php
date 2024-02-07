<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include 'css/specifics.css'
            ?>
        </style>
    </head>

    <?php 
        include 'connectionDB.php';
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnInsertTable"])) {
                buildNavbar("table_exercise");
                
                /* inserimento di una nuova tabella esercizio da parte del docente 
                    - guardare file addRecord.php
                */
            } elseif(isset($_POST["btnInsertQuestion"])) {
                buildNavbar("question");

                /* permettere l'inserimento mediante query scritta dal docente, con accorgimenti relativi alla sintassi oppure fornire lo scheletro della query 
                    - controlli che siano rispettati i vari constraint
                    - controlli che indicano se si tratta di una domanda codice oppure di una domanda chiusa
                    - controlli che stabiliscano che i dati selezionati siano esistenti 
                */
            }
        }

        /* funzione per rendere la navbar adattiva rispetto alla pagina php chiamante */
        function buildNavbar($value) {
            echo '
                <div class="navbar">
                    <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
                    <a href="'.$value.'.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
                </div>
            ';
        }
        
        closeConnection($conn);
    ?>
</html>