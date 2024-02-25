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
            include 'addAfferent.php';
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
                    <textarea class="input-tips" placeholder="INSERISCI TUTTE LE TABELLE RIFERITE ALLA DOMANDA CREATA" disabled></textarea>
                    <button type="submit" name="btnAddTable">Insert</button>
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
            if(isset($_POST['btnAddTable'])) {
                if (isset($_POST['checkbox']) && !empty($_POST['checkbox'])) {
                    $values = $_POST['checkbox'];

                    insertAfferent($conn, $_SESSION['idCurrentQuestion'], $values);
                    header('Location: ../question.php');
                    exit;
                } else {
                    echo "<script>document.querySelector('.input-tips').value=".json_encode("DEVI SELEZIONARE ALMENO UNA DELLE TABELLE PRESENTI").";</script>";
                }
            }
        }
    
        function buildForm($conn, $email) {
            $sql = 'SELECT * FROM Tabella_Esercizio WHERE (EMAIL_DOCENTE=:email);';
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(':email', $email);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox[]" value="'.$row -> ID.'">
                        <label>'.$row -> NOME.'</label>
                    </div>
                ';
            }
        }

        closeConnection($conn);
    ?>
</html>