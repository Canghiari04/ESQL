<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/specific_box.css">
        <?php
            include '../../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../question.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <?php 
            $conn = openConnection();

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST['btnSpecificQuestion'])) {
                    $idQuestion = $_POST['btnSpecificQuestion'];

                    $type = getTypeQuestion($conn, $idQuestion);

                    if($type == 'CHIUSA') {
                        $sql = 'SELECT TESTO FROM Opzione_Risposta WHERE (Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito);';
                    } else {
                        $sql = 'SELECT TESTO FROM Sketch_Codice  WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito);';
                    }

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(':idQuesito', $idQuestion);

                        $result -> execute();
                    } catch (PDOException $e) {
                        echo 'Eccezione '.$e -> getMessage().'<br>';
                    }
                        
                    if(isset($result)) {
                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                            $varAnswer = $idQuestion.'?'.$row -> TESTO;

                            echo '
                                <div class="div-question">
                                    <table>   
                                        <tr>  
                                            <th>Risposta</th>
                                        </tr>
                                        <tr>  
                                            <td>'.$row -> TESTO.'</td>
                                        </tr>
                                        <tr>
                                            <form action="../delete/deleteQuestion.php" method="POST">
                                                <td><button class="drop-btn" name="btnDropAnswer" value="'.$varAnswer.'">Drop Answer</button></td>
                                            </form>
                                        </tr>
                                    </table>
                                </div>
                            ';
                        }
                    }   
                }
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
            
            closeConnection($conn);
        ?>
    </body>
</html>