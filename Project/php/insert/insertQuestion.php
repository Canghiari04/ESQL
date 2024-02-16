<!DOCTYPE html>
<html>
    <style>
        <?php 
            include '../css/insertQuestion.css';
        ?>
    </style>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../img/ESQL.png"></a>
            <a href="../question.php"><img class="zoom-on-img undo" width="32" height="32" src="../img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-select">
                    <select name="sltDifficolta" required>
                        <option value="BASSO">BASSO</option>
                        <option value="MEDIO">MEDIO</option>
                        <option value="ALTO">ALTO</option>
                    </select>
                    <input type="number" name="txtNumeroRisposte" min="1" required>  
                </div>
                <div class="div-textbox">
                    <textarea class="input-textbox-question" type="text" name="txtDescrizione" required></textarea>
                </div>
            </div>
            <button type="submit" name="btnAddQuestion" value="'.$type.'">Add</button>
        </form>
    </body>
    <?php 
        include 'addQuestion.php';
        include '../connectionDB.php';
        
        $conn = openConnection();

        $url = $_SERVER['REQUEST_URI'];
        $str = explode("?", $url);
        $type = $str[1];

        $_SESSION["typeQuestion"] = $type;

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnAddQuestion"])) {
                $difficulty = $_POST["sltDifficolta"];
                $numAnswers = $_POST["txtNumeroRisposte"];
                $description = $_POST["txtDescrizione"];
                
                insertQuestion($conn, $type, $difficulty, $numAnswers, $description);
                header("Location: insertAnswer.php");
            }
        }
        
        closeConnection($conn);
    ?>
</html>