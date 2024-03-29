<?php
    include "buildFormMessage.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if((!isset($_SESSION["emailStudente"])) AND (!isset($_SESSION["emailDocente"]))) {
        header("Location: ../login/login.php");
    } 
    
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnNewMessage"])) {
            buildPage($conn, $_POST["btnNewMessage"]);
         } elseif(isset($_POST["btnAddMessage"])) {
            insertNewMessage($conn, $manager, $_POST["btnAddMessage"], strtoupper($_POST["txtText"]), strtoupper($_POST["txtTitle"]), $_POST["sltTest"], date("Y-m-d"));
            buildPage($conn, $_POST["btnAddMessage"]);
        }
    }

    function buildPage($conn, $typeUser) {
        echo ' 
            <!DOCTYPE html>
            <html> 
                <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
                    <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
                    <link rel="stylesheet" type="text/css" href="../../style/css/insertMessage.css">
                </head>
                <body>
                    <div class="navbar">
                        <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
                        '.buildButtonUndo($typeUser).'
                    </div>
                    <form action="" method="POST">
                        <div class="container">
                            <div class="div-select-message">
                                <select name="sltTest" required>
                                    <option selected disabled>TEST</option>                                    
                                    '.getOptions($conn, $typeUser).' 
                                </select>
                            </div>
                            <div class="div-select-message">
                                <div class="div-title">
                                    <textarea class="input-title" type="text" name="txtTitle" placeholder="TITOLO DEL MESSAGGIO" required></textarea>
                                </div>
                                <div class="div-text">
                                    <textarea class="input-text" type="text" name="txtText" placeholder="TESTO DEL MESSAGGIO" required></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="btnAddMessage" value="'.$typeUser.'">Add</button>
                    </form>
                </body>
            </html>
        ';
    }

    function getOptions($conn, $typeUser) { // funzione attuata per acquisire tutti test disponibili 
        $var = "";
        
        if($typeUser == "Teacher") { // diversificazione della query in base alla tipologia dell'utente
            $sql = "SELECT TITOLO FROM Test WHERE (Test.EMAIL_DOCENTE=:emailDocente);";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":emailDocente", $_SESSION["emailDocente"]);

                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        } else {
            $sql = "SELECT TITOLO FROM Test;";

            try {
                $result = $conn -> prepare($sql);
    
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }     
        }

        if(isset($result)) {
            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                $var = $var. '<option value="'.$row -> TITOLO.'">'.$row -> TITOLO.'</option>'; 
            }
        }       
        
        return $var;
    }

    function insertNewMessage($conn, $manager, $typeUser, $textMessage, $titleMessage, $titleTest, $date) {
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

            $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento di un nuovo messaggio dal docente: '.$_SESSION["emailDocente"].'', 'Timestamp' => date('Y-m-d H:i:s')];
            writeLog($manager, $document);
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

            $sql = "SELECT MAX(ID) AS CURRENT_ID FROM Messaggio"; // query adottata per acquisire l'ultimo numero progressivo all'interno della tabella Messaggio

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

            $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento di un nuovo messaggio dallo studente: '.$_SESSION["emailStudente"].' verso il docente: '.$_SESSION["emailDocente"].'', 'Timestamp' => date('Y-m-d H:i:s')];
            writeLog($manager, $document);
        }
    }        

    function getEmailTeacher($conn, $titleTest) { // funzione adottata per estrapolare l'email del docente riferita al test
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