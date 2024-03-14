<?php
    session_start();

    if ((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: ../login/login.php");
    } 

    include "buildFormMessage.php";
    include "../../connectionDB.php";
                
    $conn = openConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnViewMessages"])) {
            /* memorizzazione della tipologia di utente, attuata per definire i reindirizzamenti */
            $typeUser = $_POST["btnViewMessages"];
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
                buildButtonUndo($typeUser);
            ?>
        </div>
        <div class="container">
            <?php 
                
                        buildFormMessages($conn, $typeUser);
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
                        $sql = "SELECT * FROM Messaggio WHERE (ID NOT IN (SELECT ID_MESSAGGIO_STUDENTE FROM Messaggio_Studente WHERE (Messaggio_Studente.EMAIL_STUDENTE=:emailStudente)));";

                        try {
                            $result = $conn -> prepare($sql);
                            $result -> bindValue(":emailStudente", $_SESSION["emailStudente"]);

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