<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../css/specifics.css">
        <?php
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../test.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <div>
            <?php 
                $conn = openConnection();

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {   
                    if(isset($_POST['btnSpecificTest'])) {
                        $titleTest = $_POST['btnSpecificTest']; 
                        $email = $_SESSION['email'];   

                        $sql = 'SELECT Quesito.DESCRIZIONE FROM Test, Composizione, Quesito WHERE (Test.TITOLO=Composizione.TITOLO_TEST) AND (Composizione.ID_QUESITO=Quesito.ID) AND (Test.TITOLO=:titoloTest) AND (Test.EMAIL_DOCENTE=:emailDocente);';
                        
                        try {
                            $result = $conn -> prepare($sql);
                            $result -> bindValue(':titoloTest', $titleTest);
                            $result -> bindValue(':emailDocente', $email);

                            $result -> execute();
                        } catch (PDOException $e) {
                            echo 'Eccezione '.$e -> getMessage().'<br>'; 
                        }
                            
                        if(isset($result)) {
                            while($row = $result->fetch(PDO::FETCH_OBJ)) {
                                echo '
                                    <div class="div-question">
                                        <table>   
                                            <tr>  
                                                <th>Quesito</th>
                                            </tr>
                                            <tr>  
                                                <td>'.$row -> DESCRIZIONE.'</tc>
                                            </tr>
                                            <tr>  
                                                <button>Drop Question</button>
                                            </tr>
                                        </table>
                                    </div>
                                ';
                            }
                        } 
                    }
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
</html>