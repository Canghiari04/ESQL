<?php
    include "addTest.php";
    include "../../connectionDB.php";
    
    session_start();
    $conn = openConnection();
    
    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnAddTest"])) {
            $_SESSION["titleTest"] = $_POST["txtTitle"];

            insertTest($conn, $_SESSION["emailDocente"],  $_POST["sltViewAnswers"], $_FILES["txtPhoto"]["tmp_name"], $_POST["txtTitle"]);
        }
    }

    closeConnection($conn);
?>
<!DOCTYPE html>
<hmtl>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insertTest.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../test.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="container">
                <div class="div-select">
                    <select name="sltViewAnswers" required>
                        <option value="" selected disabled>VISUALIZZA RISPOSTE</option>    
                        <option value="false">NO</option>
                        <option value="true">SI</option>
                    </select>
                    <label class="custom-file-upload">CARICA FOTO<input type="file" name="txtPhoto"></input></label>
                </div>
                <div>
                    <textarea class="input-textbox-test" type="text" name="txtTitle" placeholder="TITOLO DEL TEST" required></textarea>
                </div>
            </div>
            <button class="button-test" type="submit" name="btnAddTest">Add</button>
        </form>
    </body>
</hmtl>