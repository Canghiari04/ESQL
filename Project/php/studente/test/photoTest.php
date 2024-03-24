<?php
    session_start();

    if(!isset($_SESSION["emailStudente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }

    include "../handlerData/buildForm.php";
    include "../../connectionDB.php";

    $conn = openConnection();

    if(isset($_SERVER["REQUEST_METHOD"])) {
        if(isset($_POST["btnPhotoTest"])) {
            $namePage = $_POST["btnPhotoTest"];
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
                            /* costruzione del bottone undo dinamica */
                            buildButtonUndo($namePage);
                        }
                    }
                ?>
            </div>
            <?php  
                $filePhoto = getPhoto($conn);
                printPhoto($filePhoto);

                /* funzione che restituisce il file dati dell'immagine del test */   
                function getPhoto($conn){

                    $sql = "SELECT * FROM Test WHERE (TITOLO=:titoloTest);";

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

                /* funzione che permette la visualizzazione dell'immagine del test */  
                function printPhoto($filePhoto){
                    echo '<img class="image-test" src="data:image/jpeg;base64,'.base64_encode($filePhoto).'" alt="FOTO NON DISPONIBILE">';
                }

                closeConnection($conn);
            ?>    
        </div>
    </body>
</html>