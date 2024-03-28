<?php
    function buildNavbar($typeUser) {
        if($typeUser == "Teacher") { // diversificazione del reindirizzamento a seconda della tipologia dell'utente
            $nameFile = "../../docente/handlerDocente.php";
        } else {
            $nameFile = "../../studente/handlerStudente.php";
        }

        echo '
            <div class="navbar">
                <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
                <form action="newMessage.php" method="POST">
                    <button type="submit" class="button-navbar-first" name="btnNewMessage" value="'.$typeUser.'">New Message</button>
                </form>
                <form action="viewMessagesReceived.php" method="POST">
                    <button type="submit" class="button-navbar-second" name="btnViewMessages" value="'.$typeUser.'">Received Messages</button>
                </form>
                <a href="'.$nameFile.'"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
            </div>
        ';
    }

    function buildMessageTest($conn, $typeUser) {
        if($typeUser == "Teacher") { // diversificazione della query a seconda della tipoligia
            $sql = "SELECT * FROM Messaggio WHERE (ID NOT IN (SELECT Messaggio_Studente.ID_MESSAGGIO_STUDENTE FROM Messaggio_Studente)) AND (Messaggio.EMAIL_DOCENTE=:email);";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":email", $_SESSION["emailDocente"]);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $numRows = $result -> rowCount();
            if($numRows > 0) {
                $row = $result -> fetch(PDO::FETCH_OBJ);    
                deployMessage($conn, $row -> EMAIL_DOCENTE, $row -> TESTO, $row -> TITOLO, $row -> TITOLO_TEST, $row -> DATA_INSERIMENTO);
            }
        } else {
            $sql = "SELECT * FROM Messaggio, Messaggio_Studente WHERE (Messaggio.ID=Messaggio_Studente.ID_MESSAGGIO_STUDENTE) AND (Messaggio_Studente.EMAIL_STUDENTE=:email);";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":email", $_SESSION["emailStudente"]);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $numRows = $result -> rowCount();
            if($numRows > 0) {  
                $row = $result -> fetch(PDO::FETCH_OBJ);
                deployMessage($conn, $row -> EMAIL_STUDENTE, $row -> TESTO, $row -> TITOLO, $row -> TITOLO_TEST, $row -> DATA_INSERIMENTO);
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

    function deployMessage($conn, $email, $text, $title, $titleTest, $date) { // definizione del form per la visualizzazione dei messaggi 
        echo '
            <div>
                <div class="div-message">
                    <div class="div-content">
                        <div class="div-name">
                            <label class="label-name">'.$email.'</label>
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
    
    function getNameSurname($conn, $email) { // funzione attuata per restituire il nome e cognome dell'utente
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
?>