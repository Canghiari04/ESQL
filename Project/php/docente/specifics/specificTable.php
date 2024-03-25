<?php
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();
    
    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/specific_linear.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img ESQL" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <div class="container">
            <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST") {   
                    if(isset($_POST["btnSpecificTable"])) {
                        $sql = "SELECT Attributo.TIPO, Attributo.NOME, Attributo.CHIAVE_PRIMARIA FROM Tabella_Esercizio JOIN Attributo ON (Tabella_Esercizio.ID=Attributo.ID_TABELLA) WHERE (Tabella_Esercizio.ID=:idTable);";
                        
                        try {
                            $result = $conn -> prepare($sql);
                            $result -> bindValue(":idTable", $_POST["btnSpecificTable"]);

                            $result -> execute();
                        } catch (PDOException $e) {
                            echo "Eccezione: ".$e -> getMessage()."<br>"; 
                        }
                            
                        echo '
                            <div class="div-table"> 
                                <table>   
                                    <tr>  
                                        <th>NOME</th>
                                        <th>TIPO</th>
                                        <th>CHIAVE PRIMARIA</th>
                                    </tr>
                        ';
                            
                        if(isset($result)) {
                            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                                /* metodo che restituisce se l'attributo visualizzato costituisca o meno la chiave primaria della tabella */
                                $primaryKey = convertPrimaryKey($row -> CHIAVE_PRIMARIA);
                                
                                echo '
                                    <tr>  
                                        <th>'.$row -> NOME.'</th>
                                        <th>'.$row -> TIPO.'</th>
                                        <th>'.$primaryKey.'</th>
                                    </tr>
                                ';
                            }
                        }

                        echo '
                                </table>
                            </div>
                        ';
                    }
                }

                function convertPrimaryKey($value) {
                    if($value == 0) {
                        return "NO";
                    } else {
                        return "SI";
                    }
                }
                
                closeConnection($conn);
            ?>
        </div>
    </body>
</html>