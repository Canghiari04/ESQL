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
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (isset($_POST["deleteSpecific"])) {
                            echo ' bella ciao';
                            deleteAttribute($conn, $idAttributo = $_POST["deleteSpecific"]);
                            
                        } elseif (isset($_POST["updateSpecific"])) {
                            updateAttribute($conn);
                        } elseif (isset($_POST["addSpecific"])) {
                            addAttribute($conn);
                        }
                    }
                    else {
                            echo '<script> alert("No Record / Data Found")</script>';
                    }
                    
                    
                    function deleteAttribute($conn, $idAttributo){
                        $sql = "DELETE FROM COMBINAZIONE WHERE ID_ATTRIBUTO = $idAttributo";
                        $stmt = $conn -> prepare($sql);        
                        $stmt -> execute();
                       
                        
                        closeConnection($conn);
                        header("Location: table_exercise.php");
                    }
                
                    function updateAttribute($conn) {
                        /* reindirizzare al file .php per aggiornare il contenuto degli attributi */
                
                        closeConnection($conn);
                    }
                
                    function addAttribute($conn) {
                        /* reindirizzare al file .php per aggiungere attributi alle tabelle (uso di un form con checkbox per l'inserimento) */
                
                        closeConnection($conn);
                    }
                ?>
            </div>
        </form>
    </body>
</html>

