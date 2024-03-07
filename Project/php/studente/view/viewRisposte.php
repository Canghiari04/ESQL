<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/table_exercise.css">
        <?php 
            include 'connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="viewTest.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <?php
            $conn = openConnection();  

            if(isset($_SERVER["REQUEST_METHOD"])) {
                if(isset($_POST["btnViewRisposte"])) {
                    $nameTest = $_POST["btnViewRisposte"];
                    $email = $_SESSION["email"];

                    $sql = "SELECT * FROM ;";
                    
                    try {
                        $result = $conn -> prepare($sql);
                        
                        $result -> execute();
                        $numRows = $result -> rowCount();
                        
                        if($numRows > 0) {
                            echo '
                                
                            ';

                            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                                echo '
                                   
                                ';
                            }
                        }
                    } catch(PDOException $e) {
                        echo 'Eccezione '.$e -> getMessage().'<br>';
                    }
                }
            }
        ?>
    </body>
</html>