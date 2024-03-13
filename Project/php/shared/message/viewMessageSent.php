<?php
    session_start();

    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../shared/login/login.php');
    }

    include '../../connectionDB.php';
                
    $conn = openConnection();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnViewMessage"])) {
            $values = $_POST["btnViewMessage"];
            $tokens = explode("|?|", $values);
            $idMessage = $tokens[0];
            $typeUser = $tokens[1];
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
                        buildFormMessage($conn, $typeUser, $idMessage);
                    }
                }

                function buildButtonUndo($typeUser) {
                    echo '
                        <form action="message.php" method="POST">
                            <button class="button-undo" type="submit" name="btnUndo" value="'.$typeUser.'"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
                        </form>
                    ';
                }
                
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
                
                function deployMessage($conn, $email, $text, $title, $titleTest, $date) {
                    echo '
                        <div>
                            <div class="div-message">
                                <div class="div-content">
                                    <div class="div-name">
                                        <label class="label-name">'.getNameSurname($conn, $email).'</label>
                                    </div>
                                    <p>Oggetto del messaggio <span>'.$title.'</span></p>
                                    <p>Messaggio di <span>'.getNameSurname($conn, $email).'</span>, relativo al test <span>'.$titleTest.'</span>.</p>
                                    <textarea type="text" disabled>"'.$text.'"</textarea>
                                    <div class="div-data">
                                        <label class="label-data">'.$date.'</label>
                                    </div>
                                </div>
                            </div>
                        </div>       
                    ';
                }
                
                function getNameSurname($conn, $email) {
                    $sql = "SELECT Utente.NOME, Utente.COGNOME FROM Utente WHERE (Utente.EMAIL=:email);";
                    
                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":email", $email);
                        
                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }
                    
                    $row = $result -> fetch(PDO::FETCH_OBJ);
                    return ($row -> NOME.' '.$row -> COGNOME);
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
</html>