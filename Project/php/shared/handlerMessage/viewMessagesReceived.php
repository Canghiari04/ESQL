<?php
    include "buildFormMessage.php";
    include "../../connectionDB.php";
    
    session_start();
    $conn = openConnection();

    if ((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: ../login/login.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnViewMessages"])) {
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet"> 
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_messages.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <?php 
                buildButtonUndo($_POST["btnViewMessages"]);
            ?>
        </div>
        <div class="container">
            <?php 
                
                        buildFormMessages($conn, $_POST["btnViewMessages"]);
                    }
                }

                /* funzione che permette la visualizzazione di tutti i messaggi inviati */
                function buildFormMessages($conn, $typeUser) {
                    if($typeUser == "Teacher") {
                        $sql = "SELECT * FROM Messaggio, Messaggio_Studente WHERE (Messaggio.ID=Messaggio_Studente.ID_MESSAGGIO_STUDENTE);";

                        try {
                            $result = $conn -> prepare($sql);

                            $result -> execute();
                        } catch (PDOException $e) {
                            echo "Eccezione ".$e -> getMessage()."<br>";
                        }

                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                            deployMessage($conn, $row -> EMAIL_STUDENTE, $row -> TESTO, $row -> TITOLO, $row -> TITOLO_TEST, $row -> DATA_INSERIMENTO);
                        }
                    } else {
                        $sql = "SELECT * FROM Messaggio WHERE (ID NOT IN (SELECT ID_MESSAGGIO_STUDENTE FROM Messaggio_Studente));";

                        try {
                            $result = $conn -> prepare($sql);

                            $result -> execute();
                        } catch (PDOException $e) {
                            echo "Eccezione ".$e -> getMessage()."<br>";
                        }

                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                            deployMessage($conn, $row -> EMAIL_DOCENTE, $row -> TESTO, $row -> TITOLO, $row -> TITOLO_TEST, $row -> DATA_INSERIMENTO);
                        }
                    }
                }

                closeConnection($conn);
            ?>
        </div>
    </body>
</html>