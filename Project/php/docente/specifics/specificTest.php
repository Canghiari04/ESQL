<?php
    session_start();
    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../login/login.php');
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/specific_box.css">
        <?php
            include '../../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../test.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <div>
            <?php 
                $conn = openConnection();

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {   
                    if(isset($_POST['btnSpecificTest'])) {
                        $titleTest = $_POST['btnSpecificTest']; 
                        $email = $_SESSION['emailDocente'];   

                        $sql = "SELECT * FROM Test, Quesito WHERE (Test.TITOLO=Quesito.TITOLO) AND (Test.TITOLO=:titoloTest) AND (Test.EMAIL_DOCENTE=:emailDocente);";

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
                                $varQuestion = $titleTest.'?'.$row -> ID;

                                echo '
                                    <div class="div-question">
                                        <table>   
                                            <tr>  
                                              <th>Quesito</th>
                                            </tr>
                                            <tr>  
                                                <td>'.$row -> DESCRIZIONE.'</td>
                                            </tr>
                                            <tr>
                                                <form action="../delete/deleteTest.php" method="POST">
                                                    <td><button class="drop-btn" name="btnDropQuestion" value="'.$varQuestion.'">Drop Question</button></td>
                                                </form>
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