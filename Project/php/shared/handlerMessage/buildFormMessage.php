<?php
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