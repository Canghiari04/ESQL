<?php 
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include 'addQuestion.php';
                include '../connectionDB.php';
                include '../css/insert.css';
            ?>
        </style>
    </head>

    <?php 
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnInsertDomandaChiusa"])) {
                buildNavbar("question");
                buildFormQuestion($conn, "AddDomandaChiusa");

                /* */
            } elseif(isset($_POST["btnInsertDomandaCodice"])) {
                buildNavbar("question");
                buildFormQuestion($conn, "AddDomandaCodice");

                /* */
            } else {
                /* RICORDARE DI PASSARE TUTTI I CAMPI DEL FORM */
                insertQuestion($conn); 
            }
        }

        /* funzione per rendere la navbar adattiva rispetto alla pagina php chiamante */
        function buildNavbar($value) {
            echo '
                <div class="navbar">
                    <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
                    <a href="../'.$value.'.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
                </div>
            ';
        }

        /* creazione dinamica del form, data la necessità di ulteriori campi di inserimento */
        function buildFormQuestion($conn, $value) {
            echo '
                <form action="" method="POST">
                    <div class="container">
                        <div class="div-select">
                            <select name="sltDifficolta" required>
                                <option value="BASSO">BASSO</option>
                                <option value="MEDIO">MEDIO</option>
                                <option value="ALTO">ALTO</option>
                            </select>
                            '.getNameTests($conn).'  
                            <input type="number" name="txtNumeroRisposte" min="1">
                        </div>
                        <div class="div-textbox">
                            <textarea class="input-textbox-question" type="text" name="txt'.$value.'" required></textarea>
                        </div>
                    </div>
                    <button type="submit" name="btn'.$value.'">Add</button>
                </form>
            ';
        }

        /* restituisce tutti i titoli dei test esistenti, in modo tale da associare il quesito rispetto al test voluto */
        function getNameTests($conn) {
            $sql = "SELECT TITOLO FROM Test";

            try {
                $result = $conn -> prepare($sql); 

                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            if($result) {
                echo '
                    <div class="">
                        <select name="sltNomeTest">
                ';

                while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                    echo '
                        <option value="'.$row -> TITOLO.'">'.$row -> TITOLO.'</option>
                    ';
                }

                echo '
                        </select>
                    </div>
                ';
            }
        }

        closeConnection($conn);
    ?>
</html>