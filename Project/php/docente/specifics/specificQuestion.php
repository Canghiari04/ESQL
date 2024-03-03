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
        <link rel="stylesheet" type="text/css" href="../../style/css/specific_box.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <form action="../insert/insertOption.php" method="POST">
                <button class="button-navbar" type="submit" name="btnAddOption" value="">Add Option</button>
            </form>
            <form action="../question.php" method="POST">
                <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        </div>
        <div class="center">
            <?php 
                include "../../connectionDB.php";

                $conn = openConnection();

                if($_SERVER["REQUEST_METHOD"] == "POST") {
                    if(isset($_POST["btnSpecificQuestion"])) {
                        $values = $_POST["btnSpecificQuestion"];
                        $tokens = explode("?", $values);

                        $idQuestion = $tokens[0];
                        $titleTest = $tokens[1];
                        $descriptionQuestion = $tokens[2];

                        $_SESSION["idCurrentQuestion"] = $idQuestion;
                        $_SESSION["descriptionCurrentQuestion"] = $descriptionQuestion;
                        setTypeQuestion($conn, $idQuestion, $titleTest);   
                        
                        buildSpecificQuestion($conn, $_SESSION["typeQuestion"], $idQuestion, $titleTest);     
                    } elseif(isset($_POST["btnUndo"])) {
                        buildSpecificQuestion($conn, $_SESSION["typeQuestion"], $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"]);     
                    }
                } else {
                    buildSpecificQuestion($conn, $_SESSION["typeQuestion"], $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"]);     
                }

                function buildSpecificQuestion($conn, $type, $idQuestion, $titleTest) {
                    if($type == "CHIUSA") {
                        $sql = "SELECT ID, TESTO FROM Opzione_Risposta WHERE (Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito) AND (Opzione_Risposta.TITOLO_TEST=:titoloTest);";
                    } else {
                        $sql = "SELECT ID, TESTO FROM Sketch_Codice  WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest);";
                    }

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":idQuesito", $idQuestion);
                        $result -> bindValue(":titoloTest", $titleTest);

                        $result -> execute();
                    } catch (PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }
                        
                    if(isset($result)) {
                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
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
                                                <td><button class="drop-btn" name="btnDropOption" value="'.$type.'?'.$row -> ID.'?'.$idQuestion.'?'.$titleTest.'">Drop Option</button></td>
                                            </form>
                                        </tr>
                                    </table>
                                </div>
                            ';
                        }
                    }
                }

                function setTypeQuestion($conn, $idQuestion, $titleTest) {
                    $sql = "SELECT * FROM Domanda_Chiusa WHERE (Domanda_Chiusa.ID_DOMANDA_CHIUSA=:idQuesito) AND (Domanda_Chiusa.TITOLO_TEST=:titoloTest);";

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":idQuesito", $idQuestion);
                        $result -> bindValue(":titoloTest", $titleTest);

                        $result -> execute();
                        $numRows = $result -> rowCount();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }

                    if($numRows > 0) {
                        $_SESSION["typeQuestion"] = "CHIUSA";
                    } else {
                        $_SESSION["typeQuestion"] = "CODICE";
                    }
                }
                
                closeConnection($conn);
            ?>
        <div>
    </body>
</html>