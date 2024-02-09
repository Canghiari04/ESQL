<!DOCTYPE html>
<html>
    <head>   
        <style>
            <?php 
                include 'css/insert.css'
            ?>
        </style>
    </head>

    <?php 
        include 'connectionDB.php';
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["btnInsertTable"])) {
                buildNavbar("table_exercise");
                buildForm("AddTable");

                /* */
            } elseif(isset($_POST["btnInsertQuestion"])) {
                buildNavbar("question");
                buildForm("AddQuestion");

                /* */
            } else {
                if(isset($_POST["btnAddTable"])) {
                    $sql = $_POST["txtAddTable"];

                    /* la query presa da textbox, viene suddivisa in tutti i token che la compongono per controllare se si tratti di una query CREATE */
                    $tokens = explode(" ", $sql);

                    if($tokens[0] == "CREATE") {
                        $tokensAttributes = splitAttributes($sql);
                        $tokensPrimaryKey = splitPrimaryKey($sql);                       

                        try {
                            /* prima query per la creazione della tabella effettiva */
                            $result = $conn -> prepare($sql);

                            $result -> execute();

                            /* inserimento della tabella esercizio, quindi per i meta-dati, all'interno del database tramite stored procedure */
                            insertTableExercise($conn, $tokens[2]);
                            [$numRows, $idTabellaEsercizio] = getIdTableExercise($conn, $tokens);

                            if($numRows > 0) {
                                /* trovare il modo di immettere nella tabella effettiva un dominio che si colleghi alla tabella esercizio, per fasi successive di eliminazione del record e della tabella dal database */

                                // updateTable($conn, $idTabellaEsercizio, $tokens[2]);
                            }
                        } catch(PDOException $e) {
                            echo 'Eccezione '.$e -> getMessage().'<br>';
                        }

                        foreach($tokensAttributes as $attribute) {
                            $trimAttribute = trim($attribute);
                            $tokensAttribute = explode(" ", $trimAttribute);
                            
                            if($tokensAttribute[0] == "PRIMARY") {
                                updatePrimaryKey($conn, $tokens, $tokensPrimaryKey);
                            } else {
                                /* qua arriva con tutti i token pronti per l'inserimenti, manca solamente l'eliminazione delle parentesi */
                                insertAttribute($conn, $tokens, $tokensAttribute);
                            }
                        }

                        redirect();
                    } else{
                        echo 'Sono valide solo query CREATE';
                    }
                } elseif(isset($_POST["btnAddQuestion"])) {

                } 
            }
        }

        /* funzione per rendere la navbar adattiva rispetto alla pagina php chiamante */
        function buildNavbar($value) {
            echo '
                <div class="navbar">
                    <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
                    <a href="'.$value.'.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
                </div>
            ';
        }

        function buildForm($value) {
            echo '
                <form action="" method="POST">
                    <div class="container">
                        <div class="div-tips">
                            <textarea class="input-tips" disabled>Descrivere tutti le tips per la costruzione della query in modo corretto.</textarea>
                        </div>
                        <div class="div-textbox">
                            <textarea class="input-textbox" type="text" name="txt'.$value.'" required></textarea>
                        </div>
                    </div>
                    <button type="submit" name="btn'.$value.'">Add</button>
                </form>
            ';
        }


        function splitAttributes($value) {
            /* primo split per rimuovere l'intestazione della query, CREATE TABLE ,,, */
            $split = explode("(", $value, 2);

            /* rimuovo gli ultimo due caratteri ossia ');' */
            $splitting = substr($split[1], 0, -2);

            /* ultimo split per ricavare attributi e tipo dell'attributo, ad esempio (codice INT, nome VARCHAR) --> (codice INT) - (nome VARCHAR) */
            $splitted = explode(",", $splitting);

            return $splitted;
        }

        /* funzione necessaria per ottenere i token che compongono la chiave primaria della tabella */
        function splitPrimaryKey($value) {
            /* ricava la sezione della chiave primaria, suddividendo in due parti la query, (1 parte - codice sql che precede PRIMARY KEY, quindi gli attributi / 2 - parte codice sql che segue PRIMARY KEY) */
            $split = explode("PRIMARY KEY", $value, 2);

            /* rimuove gli spazi alla fine ed inizio della stringa, quindi ' (numero, codice) );' --> '(numero, codice));' */
            $splitting = trim($split[1]);

            /* rimuove gli ultimo due caratteri del vincolo ossia ');' */
            $splitted = substr($splitting, 0, -2);
            $splitted = explode(",", $splitted);

            return $splitted;
        }

        function insertTableExercise($conn, $nomeTable) {
            $storedProcedure = "CALL Inserimento_Tabella_Esercizio(:nome, :dataCreazione, :numRighe);";

            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":nome", $nomeTable);
                $result -> bindValue(":dataCreazione", date("Y-m-d H:i:s"));
                $result -> bindValue(":numRighe", 0);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }


        function getIdTableExercise($conn, $tokens) {
            $sql = "SELECT ID FROM Tabella_Esercizio WHERE (NOME=:nome);";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":nome", $tokens[2]);
                
                $result -> execute();
                $numRows = $result -> rowCount();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $idTabellaEsercizio = $row['ID'];

            return array($numRows, $idTabellaEsercizio);
        }

        function updateTable($conn, $idTabellaEsercizio, $nome) {
            $storedProcedure = "CALL Aggiornamento_Tabella(:idTabellaEsercizio, :nome);";

            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":idTabellaEsercizio", $idTabellaEsercizio);
                $result -> bindValue(":nome", $nome);

                // $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function insertAttribute($conn, $tokens, $tokensAttribute) {
            /* ricerca dell'id della tabella per sincronizzare l'inserimento nelle tabella Attributo e Tabella_Esercizio */
            [$numRows, $idTabellaEsercizio] = getIdTableExercise($conn, $tokens);
            $primaryKey = false;
            
            /* controllo per inserimento di una sola chiave primaria, dipendente dalla sintassi usata per scrivere la query */
            if(in_array("PRIMARY", $tokensAttribute)) {
                $primaryKey = true;
            }


            if($numRows > 0) {
                $nome = $tokensAttribute[0];

                /* split per ottenere tipo e possibile dimensione dell'attributo*/
                $tokensTypeDimension = explode("(", $tokensAttribute[1]);
                $tipo = $tokensTypeDimension[0];

                /* considerazioni sulla dimensione degli attributi */
                $dimensione = $tokensTypeDimension[1];

                $storedProcedure = "CALL Inserimento_Attributo(:id, :tipo, :nome, :chiavePrimaria);";

                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":id", $idTabellaEsercizio);
                    $stmt -> bindValue(":tipo", $tipo);
                    $stmt -> bindValue(":nome", $nome);
                    $stmt -> bindValue(":chiavePrimaria", $primaryKey);
                    
                    $stmt -> execute();
                } catch (PDOException $e) {
                    echo 'Eccezione '.$e -> getMessage().'<br>';
                }
            }
        }

        function updatePrimaryKey($conn, $tokens, $attributesPrimaryKey) {
            [$numRows, $idTabellaEsercizio] = getIdTableExercise($conn, $tokens);

            if($numRows > 0) {                                
                foreach($attributesPrimaryKey as $value) {
                    $value = trim($value);
                    
                    if(substr($value, 0, 1) == "(") {
                        $value = ltrim($value, "(");
                    } elseif(substr($value, -1) == ")") {
                        $value = substr($value, 0, -1);
                    }
                    
                    $storedProcedure = "CALL Aggiornamento_Chiave(:id, :attributo);";
                    
                    try {
                        $stmt = $conn -> prepare($storedProcedure);
                        $stmt -> bindValue(":id", $idTabellaEsercizio);
                        $stmt -> bindValue(":attributo", $value);
                        
                        $stmt -> execute();
                    } catch(PDOException $e) {
                        echo 'Eccezione '.$e -> getMessage().'<br>';
                    }
                }
            }
        }

        function redirect() {
            header("Location: table_exercise.php");
        }
        
        closeConnection($conn);
    ?>
</html>