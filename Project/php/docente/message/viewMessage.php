<?php
    session_start();
    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../shared/login/login.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet"> 
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../style/css/insertRow.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
            <a href="message.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-textbox">
                    <textarea class="input-textbox" type="text" name="txtMessage" disabled></textarea>
                </div>
            </div>
        </form>
        <?php 
            include '../../connectionDB.php';
            
            $conn = openConnection();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnViewMessage"])) {
                    $idMessage = $_POST["btnViewMessage"];

                    $sql = "SELECT TESTO FROM MESSAGGIO WHERE ID = :idMessaggio;";
                    try{
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":idMessaggio", $idMessage);

                        $result -> execute();
                    }catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }
                        $row = $result -> fetch(PDO::FETCH_OBJ);
                        echo "<script>document.querySelector('.input-textbox').value='".$row -> TESTO."';</script>";
                    }
                }
            

            closeConnection($conn);
        ?>
    </body>
</html>