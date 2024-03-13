<?php
    session_start();

    if(!isset($_SESSION["emailDocente"]) || !isset($_SESSION["emailStudente"])) {
        header("Location: ../../shared/login/login.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/table_view_linear.css">
    </head>
    <body>
        <?php
            include "../../connectionDB.php";

            $conn = openConnection();

            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnUndo"])) {
                    $typeUser = $_POST["btnUndo"];

                    buildNavbar($typeUser);        
                    buildMessageTest($conn, $typeUser);
                }
            } else {
                $url = $_SERVER["REQUEST_URI"];
                $tokens = explode('?', $url);
                $typeUser = $tokens[1];

                buildNavbar($typeUser);        
                buildMessageTest($conn, $typeUser);
            }

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
                            <button type="submit" class="button-navbar-second" name="btnViewMessages" value="'.$typeUser.'">View Messages</button>
                        </form>
                        <a href="'.$nameFile.'"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
                    </div>
                ';
            }


            function buildMessageTest($conn, $typeUser) {
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
        ?>       
    </body>
</html>