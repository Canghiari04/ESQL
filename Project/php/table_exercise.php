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
                float: right;
                display: block;
                color: black;
                background-color: white;
                border: 2px solid rgb(224, 224, 224);
                border-radius: 10px;
                text-align: center;
                padding: 8px 24px;
                text-decoration: none;
            }

            button:hover {
                transition: color .4s;
                color: #ff3131;
                border-color: #ff3131;
            }
            
            .navbar {
                overflow: hidden;
                background-color: white;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .zoom-on-img {
                margin-top: 10px;
                margin-left: 15px;
                transition: transform .4s;
            }

            .zoom-on-img:hover {
                transform: scale(1.099);
            }

            .undo {
                float: right;
                margin-right: 25px;
                margin-top: 15px;
                margin-bottom: 15px;
            }

            .background {
                height: 100%;
                background-image: url("img/background.png");
                background-repeat: no-repeat;
                background-size: cover;
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
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="img/ESQL.png"></a>
            <a href="handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        </div>

        <div>
            <?php 
                include 'connectionDB.php';
                $conn = openConnection();
                $query = "SELECT * FROM Tabella_Esercizio ";
                $query_run = $conn -> query($query);

                if($query_run) {
                    while($row = $query_run -> fetch(PDO::FETCH_OBJ))
                        echo '
                            <div class="div-table">
                                <form action="specifics.php" method="GET">
                                    <table>
                                        <tr>
                                            <th> '.$row -> NOME.'</th>
                                            <th> '.$row -> DATA_CREAZIONE.'</th>
                                            <th> '.$row -> NUM_RIGHE.'</th>
                                            <th> <button type="submit" name="btnSpecific" value='.$row -> ID.'>LINK</button> </th>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        ';
                } else {
                    echo ' <script> alert("No Record / Data Found")</script> ';
                }
            ?>
        </div>
    </body>
</html>