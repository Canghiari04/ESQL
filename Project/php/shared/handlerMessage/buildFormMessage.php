<?php
    /* metodo che permette di visualizzare correttamente la navbar */
    function buildNavbar($typeUser) {
        if($typeUser == "Teacher") {
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


    /* funzione che permette di visualizzare tutti i messaggi inviati dallo specifico utente */
    function buildMessageTest($conn, $typeUser) {
        /* in base alla tipologia e alla mail è diversificata l'interrogazione posta al database */
        if($typeUser == "Teacher") {
            $sql = "SELECT * FROM Messaggio WHERE (ID NOT IN (SELECT Messaggio_Studente.ID_MESSAGGIO_STUDENTE FROM Messaggio_Studente)) AND (Messaggio.EMAIL_DOCENTE=:emailDocente);";
            
            try{
                $result = $conn -> prepare($sql);
                $result -> bindValue(":emailDocente", $_SESSION["emailDocente"]);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        } else {
            $sql = "SELECT * FROM Messaggio, Messaggio_Studente WHERE (Messaggio.ID=Messaggio_Studente.ID_MESSAGGIO_STUDENTE) AND (Messaggio_Studente.EMAIL_STUDENTE=:emailStudente);";
            
            try{
                $result = $conn -> prepare($sql);
                $result -> bindValue(":emailStudente", $_SESSION["emailStudente"]);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        }
            
        echo '
            <div class="center">
                <div class="div-th"> 
                    <table class="table-head-message">   
                        <tr>  
                            <th>TITOLO</th>
                            <th>TEST RIFERIMENTO</th>
                            <th>DATA</th>
                        </tr>
                    </table>
                </div>
        ';
            
        if(isset($result)) {
            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                echo '
                        <div class="div-td">
                            <table class="table-list">   
                                <tr>  
                                    <th>'.$row -> TITOLO.'</th>
                                    <th>'.$row -> TITOLO_TEST.'</th>
                                    <th>'.$row -> DATA_INSERIMENTO.'</th>
                                    <th>
                                        <form action="viewMessageSent.php" method="POST">
                                            <button class="table-button" type="submit" name="btnViewMessage" value= "'.$row -> ID.'|?|'.$typeUser.'">View Message</button>
                                        </form>
                                    </th>
                                </tr>
                            </table>
                        </div>
                    </div>
                ';
            }
        }     
    }              

    /* definizione del bottone undo all'interno della navbar */
    function buildButtonUndo($typeUser) {
        echo '
            <form action="message.php" method="POST">
                <button class="button-undo" type="submit" name="btnUndo" value="'.$typeUser.'"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        ';
    }  

    /* costruzione del form per la visualizzazione dei messaggi inviati e ricevuti */
    function deployMessage($conn, $email, $text, $title, $titleTest, $date) {
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
    
    /* metodo che permette di memorizzare il nome e il cognome dell'utente, utilizzati per la visualizzazione del messaggio */
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
?>