<?php
    include "buildFormMessage.php";
    include "../../connectionDB.php";
    
    session_start();
    $conn = openConnection();

    if ((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: ../login/login.php");
    } 
        
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnViewMessage"])) {
            /* tramite il tag value del bottone è memorizzato l'id del messaggio e la tipologia di utente */
            $values = $_POST["btnViewMessage"];
            $tokens = explode("|?|", $values);
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
                /* definizione del bottone undo, a cui è passato la tipologia di utente per impostare il corretto reindirizzamento */
                buildButtonUndo($tokens[1]);
            ?>
        </div>
        <div class="container">
            <?php  
                        buildFormMessage($conn, $tokens[1], $tokens[0]);
                    }
                }
                
                /* funzione che permette la visualizzazione del messaggio inviato */
                function buildFormMessage($conn, $typeUser, $idMessage) {
                    if($typeUser == "Teacher") {
                        $sql = "SELECT * FROM Messaggio WHERE (Messaggio.ID=:idMessaggio);";
                        
                        try{
                            $result = $conn -> prepare($sql);
                            $result -> bindValue(":idMessaggio", $idMessage);
                            
                            $result -> execute();
                        }catch(PDOException $e) {
                            echo "Eccezione ".$e -> getMessage()."<br>";
                        }
                    
                        $row = $result -> fetch(PDO::FETCH_OBJ);
                        deployMessage($conn, $row -> EMAIL_DOCENTE, $row -> TESTO, $row -> TITOLO, $row -> TITOLO_TEST, $row -> DATA_INSERIMENTO);
                    } else {
                        $sql = "SELECT * FROM Messaggio, Messaggio_Studente WHERE (Messaggio.ID=Messaggio_Studente.ID_MESSAGGIO_STUDENTE) AND (Messaggio.ID=:idMessaggio);";
                    
                        try{
                            $result = $conn -> prepare($sql);
                            $result -> bindValue(":idMessaggio", $idMessage);
                            
                            $result -> execute();
                        }catch(PDOException $e) {
                            echo "Eccezione ".$e -> getMessage()."<br>";
                        }
                        
                        $row = $result -> fetch(PDO::FETCH_OBJ);
                        deployMessage($conn, $row -> EMAIL_STUDENTE, $row -> TESTO, $row -> TITOLO, $row -> TITOLO_TEST, $row -> DATA_INSERIMENTO);
                    }
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
</html>