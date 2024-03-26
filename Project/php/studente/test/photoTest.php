<?php
    include "../handlerData/buildForm.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();

    if(!isset($_SESSION["emailStudente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }

    if(isset($_SERVER["REQUEST_METHOD"])) {
        if(isset($_POST["btnPhotoTest"])) {
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/image_test.css">
    </head>
    <body>
        <div class="container">
            <div class="navbar">
                <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
                <?php
                            buildButtonUndo($_POST["btnPhotoTest"]); // funzione attuata per garantire la costruzione dinamica del bottone undo rispetto alla pagina chiamante
                        }
                    }
                ?>
            </div>
            <?php  
                printPhoto(getPhoto($conn));

                closeConnection($conn);
            ?>    
        </div>
    </body>
    <?php 
        function getPhoto($conn) { 
            $sql = "SELECT FOTO FROM Test WHERE (TITOLO=:titoloTest);"; // query attuata per estrapolare l'immagine del test

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":titoloTest", $_SESSION["titleTest"]);
        
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
    
            $row = $result -> fetch(PDO::FETCH_OBJ);
            $filePhoto = $row -> FOTO;

            return $filePhoto;
        }

        function printPhoto($filePhoto) { // metodo definito per la visualizzazione della foto 
            echo '<img class="image-test" src="data:image/jpeg;base64,'.base64_encode($filePhoto).'" alt="FOTO NON DISPONIBILE">';
        }
    ?>
</html>