<?php
    session_start();
    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../shared/login/login.php');
    }

    include '../../connectionDB.php';
    $conn = openConnection();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../style/css/insertQuestion.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
            <form action="message.php" method="POST">
                <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></button>
            </form>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-select">
                    <select name="sltTest" required>
                        <option value="" selected disabled>TEST</option>
                        <?php getOptions($conn) ?>
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
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddMessage"])) {

                $testMessage = $_POST["sltTest"];
                $textMessage = $_POST["txtText"];        
                $titleMessage = $_POST["txtTitle"];
                $date = date("Y-m-d");

                $storedProcedure = " CALL Inserimento_Messaggio_Docente(:emailDocente, :testo, :titolo, :titoloTest, :dataInserimento)";
            
                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":emailDocente", $_SESSION["emailDocente"]);
                    $stmt -> bindValue(":testo", $textMessage);
                    $stmt -> bindValue(":titolo", $titleMessage);
                    $stmt -> bindValue(":titoloTest", $testMessage);
                    $stmt -> bindValue(":dataInserimento", $date);
                        
                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                header("Location: message.php");
            }

        } 

        function getOptions($conn){

            $sql = "SELECT * FROM Test WHERE EMAIL_DOCENTE = :emailDocente;";

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
        }
        
        closeConnection($conn);

        
    ?>
</html>