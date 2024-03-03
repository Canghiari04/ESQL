<?php
    session_start();
    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../login/login.php');
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../style/css/navbar_horizontal.css">
    </head>
    <body>
        <div class="navbar">
            <a href="index.php?"><img class="zoom-on-img ESQL" width="112" height="48" src="../style/img/ESQL.png"></a>
            <a class="a-href" href="../statistic.php">Statistiche</a>
            <a class="a-href" href="#">Messaggi</a>
            <a class="a-href" href="#">Studenti</a>
            <a class="a-href" href="table_exercise.php">Tabelle</a>
            <a class="a-href" href="test.php">Test</a>
            <a class="a-href" href="../index.php?">Logout</a>
        </div>
        
    </body>
</html>