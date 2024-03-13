<?php
    session_start();

    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../shared/login/login.php');
    }

    include '../../connectionDB.php';

    $conn = openConnection();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnNewMessage"])) {
            $typeUser = $_POST["btnNewMessage"];
        
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insertQuestion.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <?php 
                buildButtonUndo($typeUser);
            ?>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-select">
                    <select name="sltTest" required>
                        <option selected disabled>TEST</option>
                        <?php getOptions($conn, $typeUser) ?>
                    </select>
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox-question" type="text" name="txtTitle" placeholder="TITOLO DEL MESSAGGIO" required></textarea>
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox-question" type="text" name="txtText" placeholder="TESTO DEL MESSAGGIO" required></textarea>
                </div>
            </div>
            <button type="submit" name="btnAddMessage">Add</button>
        </form>
    </body>
    <?php 
            } elseif(isset($_POST["btnAddMessage"])) {
                $textMessage = $_POST["txtText"];        
                $titleMessage = $_POST["txtTitle"];
                $titleTest = $_POST["sltTest"];
                $date = date("Y-m-d");

                insertNewMessage($conn, $typeUser, $textMessage, $titleMessage, $titleTest, $date);
            }
        }

        function getOptions($conn, $typeUser){
            if($typeUser == "Teacher") {
                $sql = "SELECT TITOLO FROM Test WHERE (Test.EMAIL_DOCENTE=:emailDocente);";

                try{
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":emailDocente", $_SESSION["emailDocente"]);
    
                    $result -> execute();
                }catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
    
                if(isset($result)) {
                    while($row = $result->fetch(PDO::FETCH_OBJ)) {
                        echo '<option value="'.$row -> TITOLO.'">'.$row -> TITOLO.'</option>';
                    }
                }         
            } else {
                $sql = "SELECT TITOLO FROM Test;";

                try{
                    $result = $conn -> prepare($sql);
    
                    $result -> execute();
                }catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
    
                if(isset($result)) {
                    while($row = $result->fetch(PDO::FETCH_OBJ)) {
                        echo '<option value="'.$row -> TITOLO.'">'.$row -> TITOLO.'</option>';
                    }
                }         
            }
        }

        function buildButtonUndo($typeUser) {
            echo '
                <form action="message.php" method="POST">
                    <button class="button-undo" type="submit" name="btnUndo" value="'.$typeUser.'"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
                </form>
            ';
        }

        function insertNewMessage($conn, $typeUser, $textMessage, $titleMessage, $titleTest, $date) {
            if($typeUser == "Teacher") {
                $storedProcedure = "CALL Inserimento_Messaggio_Docente(:emailDocente, :testo, :titolo, :titoloTest, :dataInserimento)";

                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":emailDocente", $_SESSION["emailDocente"]);
                    $stmt -> bindValue(":testo", $textMessage);
                    $stmt -> bindValue(":titolo", $titleMessage);
                    $stmt -> bindValue(":titoloTest", $titleTest);
                    $stmt -> bindValue(":dataInserimento", $date);
                    
                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }                
            } else {
                $storedProcedureTeacher = "CALL Inserimento_Messaggio_Docente(:emailDocente, :testo, :titolo, :titoloTest, :dataInserimento)";

                try {
                    $stmtTeacher = $conn -> prepare($storedProcedureTeacher);
                    $stmtTeacher -> bindValue(":emailDocente", getEmailTeacher($conn, $titleTest));
                    $stmtTeacher -> bindValue(":testo", $textMessage);
                    $stmtTeacher -> bindValue(":titolo", $titleMessage);
                    $stmtTeacher -> bindValue(":titoloTest", $titleTest);
                    $stmtTeacher -> bindValue(":dataInserimento", $date);
                    
                    $stmtTeacher -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                $sql = "SELECT MAX(ID) AS CURRENT_ID FROM Messaggio";

                try {
                    $result = $conn -> prepare($sql);

                    $result -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                $row = $result -> fetch(PDO::FETCH_OBJ);
                $idMessage = $row -> CURRENT_ID;
                
                $storedProcedureStudent = "CALL Inserimento_Messaggio_Studente(:idMessaggio, :emailStudente);";

                try {
                    $stmtStudent = $conn -> prepare($storedProcedureStudent);
                    $stmtStudent -> bindValue(":idMessaggio", $idMessage);
                    $stmtStudent -> bindValue(":emailStudente", $_SESSION["emailStudente"]);
                    
                    $stmtStudent -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }                
            }
        }        

        function getEmailTeacher($conn, $titleTest) {
            $sql = "SELECT Test.EMAIL_DOCENTE FROM Test WHERE (Test.TITOLO=:titoloTest)";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":titoloTest", $titleTest);

                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }                

            $row = $result -> fetch(PDO::FETCH_OBJ);
            return $row -> EMAIL_DOCENTE;
        }
        
        closeConnection($conn);  
    ?>
</html>