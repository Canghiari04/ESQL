<?php
    include "addAfferent.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>  
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insert_checkbox.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <?php
                    buildForm($conn, $_SESSION["emailDocente"]); // costruzione del form per inserimento di nuove afferenza all'interno del database
                ?>
            </div>
            <div class="div-button">
                <button type="submit" name="btnAddAfferent">Insert</button>
            </div>
        </form>
    </body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddAfferent"])) {
                if(isset($_POST["checkbox"]) && !empty($_POST["checkbox"])) { // controllo che siano checkate delle tabelle
                    insertAfferent($conn, $manager, $_SESSION["idCurrentQuestion"], $_SESSION["titleCurrentTest"], $_POST["checkbox"]); // inserimento della nuova afferenza tra quesito, test e tabella all'interno della collezione Afferenza
                } else {
                    echo "<script type='text/javascript'>alert(".json_encode("Seleziona una tabelle presenti.").");</script>";
                } 
            }
        }
    
        function buildForm($conn, $email) {
            $sql = "SELECT * FROM Tabella_Esercizio WHERE (Tabella_Esercizio.EMAIL_DOCENTE=:email) AND (Tabella_Esercizio.NUM_RIGHE>0);"; // query che estrapola solamente le tabelle che abbiano record al loro interno
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":email", $email);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox[]" value="'.$row -> ID.'">
                        <label>'.$row -> NOME.'</label>
                    </div>
                ';
            }
        }

        closeConnection($conn);
    ?>
</html>