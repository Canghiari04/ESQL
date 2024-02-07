<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include 'css/specifics.css'
            ?>
        </style>
    </head>
    <body>
        <?php 
            include 'connectionDB.php';
            $conn = openConnection();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnAddTable"])) {
                    buildNavbar("table_exercise");
                    
                    /* inserimento di una nuova tabella esercizio da parte del docente */
                } elseif(isset($_POST["btnAddQuestion"])) {
                    buildNavbar("question");
                }
            }

            closeConnection($conn);

            /* funzione per rendere la navbar adattiva rispetto alla pagina php chiamante */
            function buildNavbar($value) {
                echo '
                    <div class="navbar">
                        <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
                        <a href="'.$value.'.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
                    </div>
                ';
            }
        ?>
    </body>
</html>