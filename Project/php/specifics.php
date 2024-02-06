<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <style>
            html, body {
                font-family: 'Public Sans';
                height: 100%;
            }

            button {
                background-color: transparent;
                border: none;
            }

            .navbar {
                background-color: white;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .zoom-on-img {
                transition: transform .4s;
            }

            .zoom-on-img:hover {
                transform: scale(1.099);
            }

            .ESQL {
                margin-top: 10px;
                margin-left: 15px;
                margin-bottom: 10px;
            }

            .undo {
                float: right;
                margin-right: 25px;
                margin-top: 15px;
                margin-bottom: 15px;
            }
            
            .div-hide {
                color: white;
            }

            table {
                table-layout: fixed;
                width: 100%;
            }

            .div-table {
                display: flex;
                justify-content: space-between; /* Adjust as needed */
                padding: 30px;
                margin: 50px 150px;
                background-color: rgb(240, 240, 240);
                border: 2px solid rgb(224, 224, 224);
                border-radius: 10px;
                align-items: center;
            }
        </style>
    </head>
    <body>
        <form>
            <div class="navbar">
                <a><img class="zoom-on-img ESQL" width="112" height="48" src="img/ESQL.png"></a>
                <a href="table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
            </div>
            <div>
                <?php 
                    include 'connectionDB.php';
                    $conn = openConnection();
                    try {
                        if ($_SERVER["REQUEST_METHOD"] == "GET") {   
                            $spec = $_GET["btnSpecific"];                         
                            $sql = "SELECT Attributo.ID, Attributo.TIPO, Attributo.NOME FROM Combinazione, Tabella_Esercizio, Attributo WHERE (Combinazione.ID_TABELLA = Tabella_Esercizio.ID) AND (Combinazione.ID_ATTRIBUTO = Attributo.ID) AND (Tabella_Esercizio.ID = $spec)";
                            $result = $conn -> prepare($sql);
                            $result -> execute();


                            if($result) {
                                while($row = $result->fetch(PDO::FETCH_OBJ)){
                                    $val=$row->ID;

                                    /* tolto dal form il metodo POST per l'eliminazione del meta-dato */

                                    echo '
                                    <div class="div-table">
                                            <form>        
                                                <a> '.$row -> NOME.'</a>
                                                <a> '.$row -> TIPO.'</a>
                                            </form>
                                        </div>
                                    ';
                                }
                            }
                            else {
                                echo '<script> alert("No Record / Data Found")</script>';
                            }
                            
                        }
                        closeConnection($conn);
                    } catch(Exception $e) {
                        echo 'Eccezione individuata: '. $e -> getMessage();
                    } 
                    
                ?>
            </div>
        </form>
    </body>
</html>

