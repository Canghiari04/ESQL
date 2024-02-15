<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include '../css/insertQuestion.css';
                include '../connectionDB.php';
            ?>
        </style>
    </head>
    <?php 
        $conn = openConnection();

        $url = $_SERVER['REQUEST_URI'];
        $str = explode("?", $url);
        $type = $str[1];
        
        buildNavbar();
        buildFormQuestion($conn, $type);

        /* funzione per rendere la navbar adattiva rispetto alla pagina php chiamante */
        function buildNavbar() {
            echo '
                <div class="navbar">
                    <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
                    <a href="../question.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
                </div>
            ';
        }


        /* creazione dinamica del form, data la necessità di ulteriori campi di inserimento */
        function buildFormQuestion($conn, $type) {
            echo '
                <form action="insertAnswer.php" method="POST">
                    <div class="container">
                        <div class="div-select">
                            <select name="sltDifficolta" required>
                                <option value="BASSO">BASSO</option>
                                <option value="MEDIO">MEDIO</option>
                                <option value="ALTO">ALTO</option>
                            </select>
                            <input type="number" name="txtNumeroRisposte" min="1" required>
            ';
                        
            $sql = "SELECT * FROM Tabella_Esercizio WHERE (EMAIL_DOCENTE=:email);";
            $emailTeacher = $_SESSION["email"];

            try {
                $result = $conn -> prepare($sql); 
                $result -> bindValue(":email", $emailTeacher);

                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            if($result) {
                echo '
                    <select name="sltNomeTabella" required>
                ';

                while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                    echo '
                        <option value="'.$row -> ID.'">'.$row -> NOME.'</option>
                    ';
                }

                echo '
                    </select>
                ';
            }       
                        
            echo '  
                        </div>
                        <div class="div-textbox">
                            <textarea class="input-textbox-question" type="text" name="txtDescrizione" required></textarea>
                        </div>
                    </div>
                    <button type="submit" name="btnAddDomanda" value="'.$type.'">Add</button>
                </form>
            ';
        }

        closeConnection($conn);
    ?>
</html>