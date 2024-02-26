<?php 
    session_start();
?>
<!DOCTYPE html>
<hmtl>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../css/insertTest.css">
        <?php 
            include 'addTest.php';
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../test.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-select">
                    <select name="sltViewAnswers" required>
                        <option value="" selected disabled>VISUALIZZA RISPOSTE</option>    
                        <option value="false">NO</option>
                        <option value="true">SI</option>
                    </select>
                    <label class="custom-file-upload">
                        <input type="file" name="nptPhotoTest">  
                        SELEZIONA FOTO
                    </label>
                </div>
                <div>
                    <textarea class="input-textbox-test" type="text" name="txtTitle" placeholder="TITOLO DEL TEST" required></textarea>
                </div>
            </div>
            <button class="button-test" type="submit" name="btnAddTest">Add</button>
        </form>
    </body>
    <?php
        $conn = openConnection();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnAddTest'])) {
                if(checkQuestion($conn)){
                    $viewAnswers = $_POST['sltViewAnswers'];
                    $fileTest = $_POST['nptPhotoTest'];
                    $titleTest = $_POST['txtTitle'];

                    $_SESSION['titleTest'] = $titleTest;

                    insertTest($conn, $_SESSION['email'], $viewAnswers, $fileTest, $titleTest);
                    header('Location: insertComposition.php');
                } else {
                    echo "<script>document.querySelector('.input-textbox-test').value=".json_encode("NESSUN QUESITO PRESENTE, INSERISCI QUALCHE DOMANDA PRIMA DI CREARE DEI TEST").";</script>";
                }
            }
        }
    ?>
</hmtl>