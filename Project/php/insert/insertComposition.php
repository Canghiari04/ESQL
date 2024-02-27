<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>  
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../css/insert_checkbox.css">
        <?php 
            include 'addComposition.php';
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-textbox">
                    <textarea class="input-tips" placeholder="INSERISCI TUTTI I QUESITI CHE COMPONGANO IL TEST" disabled></textarea>
                    <button type="submit" name="btnAddComposition">Insert</button>
                </div>
            </div>
            <?php
                $conn = openConnection();

                buildForm($conn, $_SESSION['email']);
            ?>
        </form>
    </body>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['btnAddComposition'])) {
                if (isset($_POST['checkbox']) && !empty($_POST['checkbox'])) {
                    $values = $_POST['checkbox'];

                    insertComposition($conn, $_SESSION['titleTest'], $values);
                } else {
                    echo "<script>document.querySelector('.input-tips').value=".json_encode("DEVI SELEZIONARE ALMENO UNO DEI QUESITI PRESENTI").";</script>";
                }
            } 
        }
    
        function buildForm($conn, $email) {
            $sql = 'SELECT * FROM Quesito;';
            
            try {
                $result = $conn -> prepare($sql);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox[]" value="'.$row -> ID.'">
                        <label>'.$row -> DESCRIZIONE.'</label>
                    </div>
                ';
            }
        }

        closeConnection($conn);
    ?>
</html>