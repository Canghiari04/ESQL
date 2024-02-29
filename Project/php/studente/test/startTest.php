<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="">
        <?php 
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
            <a href="handlerStudente.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        </div>
        <div class="navbar">
            <?php
                $conn = openConnection();  
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {   
                    if(isset($_POST['btnStartTest'])) {
                        $titleTestStarted = $_POST['btnStartTest'];
                        $result=getTestQuestion($conn, $titleTestStarted);

                        if(isset($result)){
                            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                                echo getQuestionDescription($conn, $row -> ID);
                                if(getTypeQuestion($conn, $row -> ID) == 'CHIUSA'){
                                    buildFormCheck($conn, $row -> ID);
                                }
                                else{
                                    
                                }
                                
                            }
                        }

                    }
                }
            ?>
        </div>
    </body>
    <?php
        function getTestQuestion($conn, $titleTestStarted){
            $sql = 'SELECT Quesito.ID FROM Test, Composizione, Quesito WHERE (Test.TITOLO=Composizione.TITOLO_TEST) AND (Composizione.ID_QUESITO=Quesito.ID) AND (Test.TITOLO=:titoloTest);';           

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':titoloTest', $titleTestStarted);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>'; 
            }

            return $result;

        }

        function getQuestionDescription($conn, $idQuestion){
            $sql = 'SELECT DESCRIZIONE FROM Quesito WHERE ID=:IdQuesito';

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':IdQuesito', $idQuestion);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>'; 
            }

            $row = $result -> fetch(PDO::FETCH_OBJ);

            return $row -> DESCRIZIONE;
        }


        function buildFormCheck($conn, $idQuestion) {
            $sql = 'SELECT * FROM Opzione_Risposta WHERE ID_DOMANDA_CHIUSA=:IdQuesito;';
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':IdQuesito', $idQuestion);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>'; 
            }

            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox[]" value="'.$row -> ID.'?'.$idQuestion.'">
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
                
            }
            echo '
                <form action="" method="POST">
                    <th><button class="" type="submit" name="btnStartTest" value="gay">INVIA</button></th>
                </form>
            ';
        }

        function buildFormQuery(){

        }

        function getTypeQuestion($conn, $idQuestion) {
            $sql = 'SELECT * FROM Quesito JOIN Domanda_Chiusa ON (ID = ID_DOMANDA_CHIUSA) WHERE (Quesito.ID = :idQuesito);';

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':idQuesito', $idQuestion);

                $result -> execute();
                $numRows = $result -> rowCount();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().' <br>';
            }

            if($numRows > 0) {
                return 'CHIUSA';
            } else {
                return 'CODICE';
            }
        }

        function getSketchCode($conn, $idQuestion){
            $sql = 'SELECT * FROM SketchCodice  WHERE ID_DOMANDA_CHIUSA=:IdQuesito';

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':IdQuesito', $idQuestion);

                $result -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>'; 
            }

            $row = $result -> fetch(PDO::FETCH_OBJ);

            return $row -> DESCRIZIONE;
        }

    ?>
</html>
