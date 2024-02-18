<?php 
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>           
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/insertRow.css">
        <?php 
            include 'addRow.php';
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <div class="container">
            <div class="div-tips">
                <textarea class="input-tips" placeholder="SQLSTATE: POSSIBILI ERRORI DI SINTASSI" disabled></textarea>
            </div>
            <div class="div-textbox">
                <?php             
                    $conn = openConnection();
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST["btnInsertRow"])) {
                            $att=$_POST["btnInsertRow"];
                            $_SESSION["IdTable"]=$att;
                            IdentifyAttributes($conn,$att);
                        }elseif (isset($_POST["btnInsertData"])) {
                            insertData($conn);  
                            IdentifyAttributes($conn,$_SESSION["IdTable"]);
                            
                        }
                    }
                ?>
            </div>
        </div>
    </body>
</html>