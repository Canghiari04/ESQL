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
            
            table {
                table-layout: fixed;
                width: 100%;
            }

            .div-th-table {
                display: flex;
                justify-content: space-between; /* Adjust as needed */
                padding: 30px;
                margin: 30px 150px;
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
                            $sql = "SELECT Attributo.ID, Attributo.TIPO, Attributo.NOME, Attributo.CHIAVE_PRIMARIA FROM Tabella_Esercizio, Attributo WHERE (Tabella_Esercizio.ID = Attributo.ID_TABELLA) AND (Tabella_Esercizio.ID = $spec)";
                            $result = $conn -> prepare($sql);
                            $result -> execute();

                            echo'
                            <div class="div-th-table"> 
                                <table>   
                                    <tr>  
                                        <th> Nome attributo </th>
                                        <th> Tipo attributo </th>
                                        <th> Chiave primaria </th>
                                    </tr>
                                </table>
                            </div>';

                            if($result) {
                                while($row = $result->fetch(PDO::FETCH_OBJ)){
                                    $key = convertPrimaryKey($row -> CHIAVE_PRIMARIA);

                                    /* tolto dal form il metodo POST per l'eliminazione del meta-dato */
                                    echo '
                                    <div class="div-table">
                                            <form>   
                                                <table>   
                                                    <tr>  
                                                        <th> '.$row -> NOME.' </th>
                                                        <th> '.$row -> TIPO.' </th>
                                                        <th> '.$key.' </th>
                                                    </tr>
                                                </table>
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

<?php
    
    function convertPrimaryKey($value) {
        if($value == 0) {
            return "No";
        } else {
            return "Si";
        }
    }

?>