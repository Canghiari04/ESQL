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
            } elseif(isset($_POST["btnInsertDomandaChiusa"])) {
                buildNavbar("question");
                buildFormQuestion($conn, "AddDomandaChiusa");

                /* */
            } elseif(isset($_POST["btnInsertDomandaCodice"])) {
                buildNavbar("question");
                buildFormQuestion($conn, "AddDomandaCodice");

                /* */
            } else {
                if(isset($_POST["btnAddTable"])) {
                    $sql = $_POST["txtAddTable"];

                    /* la query presa da textbox, viene suddivisa in tutti i token che la compongono per controllare se si tratti di una query CREATE, da cui si ricava il nome della tabella */
                    $tokens = explode(" ", $sql);

                    if(in_array("CREATE", $tokens)) {
                        try {
                            /* prima query per la creazione della tabella effettiva */
                            $result = $conn -> prepare($sql);

                            $result -> execute();

                            /* inserimento della tabella esercizio */
                            insertTableExercise($conn, $tokens[2]);
                        } catch(PDOException $e) {
                            echo 'Eccezione '.$e -> getMessage().'<br>';
                        }

                        /* inserimento dei record all'interno delle tabelle meta-dati */
                        insertRecord($conn, $sql, $tokens[2]);

                        /* DA RICONSIDERARE IL REDIRECT DATO CHE IN CASO DI ERRORI NON SAREBBERO VISUALIZZABILI  */
                        //redirectToTable();
                    } else{
                        echo 'Sono valide solo query CREATE';
                    }
                } elseif(isset($_POST["btnAddQuestion"])) {

                    //redirectToQuestion();
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

        /* creazione dinamica del form, data la necessità di ulteriori campi di inserimento rispetto al metodo buildForm() generale */
        function buildFormQuestion($conn, $value) {
            echo '
                <form action="" method="POST">
                    <div class="container">
                        <div class="div-select">
                            <select name="sltDifficolta" required>
                                <option value="BASSO">BASSO</option>
                                <option value="MEDIO">MEDIO</option>
                                <option value="ALTO">ALTO</option>
                            </select>
                            '.getNameTests($conn).'  
                            <input type="number" name="txtNumeroRisposte" min="1">
                        </div>
                        <div class="div-textbox">
                            <textarea class="input-textbox-question" type="text" name="txt'.$value.'" required></textarea>
                        </div>
                    </div>
                    <button type="submit" name="btn'.$value.'">Add</button>
                </form>
            ';
        }

        /* restituisce tutti i titoli dei test esistenti, in modo tale da associare il quesito rispetto al test voluto */
        function getNameTests($conn) {
            $sql = "SELECT TITOLO FROM Test";

            try {
                $result = $conn -> prepare($sql); 

                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            if($result) {
                echo '
                    <div class="">
                        <select name="sltNomeTest">
                ';

                while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                    echo '
                        <option value="'.$row -> TITOLO.'">'.$row -> TITOLO.'</option>
                    ';
                }

                echo '
                        </select>
                    </div>
                ';
            }
        }

        function insertRecord($conn, $sql, $nomeTabella) {
            /* flag che consente il corretto inserimento dei record all'interno della tabella Attributo */
            $flagPrimaryKey = 0;

            /* rimozione dell'intestazione della query e dei due caratteri finali ");" */
            $tokens = explode("(", $sql, 2);
            $tokens = substr($tokens[1], 0, -2);

            /* split per ogni riga della query "...,", successiva alla rimozione di spazi iniziali e finali per ogni riga */
            $tokensQuery = explode(",", trim($tokens));

            /* metodo che restituisce l'id della tabella esercizio di riferimento */
            [$numRows, $idTabellaEsercizio] = getIdTableExercise($conn, $nomeTabella);

            foreach($tokensQuery as $value) {  
                /* split inerente allo spazio compreso tra nome e tipo della colonna */
                $token = explode(" ", trim($value));

                /* condizione che tratta vincoli di chiave primaria oppure esterna */
                if($token[0] == "PRIMARY") {
                    /* set del flag a 1 per evitare si inserire colonne già presenti, a causa della sintassi del vincolo di chiave primaria */
                    $flagPrimaryKey = 1;
                    updatePrimaryKey($conn, $numRows, $idTabellaEsercizio, splitPrimaryKey($sql));
                } elseif ($token[0] == "FOREIGN") {
                    [$tokensForeignKey, $nameTableReferenced, $tokensTableReferenced] = splitForeignKey($sql);
                    insertForeignKey($conn, $numRows, $idTabellaEsercizio, $tokensForeignKey, $nameTableReferenced, $tokensTableReferenced);
                    
                    break;
                } elseif($flagPrimaryKey == 0) {
                    insertAttribute($conn, $numRows, $idTabellaEsercizio, $token);
                }
            }
        }

        function insertAttribute($conn, $numRows, $idTabellaEsercizio, $tokensAttribute) {
            $primaryKey = 0;

            /* controllo per inserimento di una singola chiave primaria */
            if(in_array("PRIMARY", $tokensAttribute)) {
                $primaryKey = 1;
            }
            
            if($numRows > 0) { 
                $nome = $tokensAttribute[0];
                
                /* split per ottenere tipo e dimensione dell'attributo */
                $tokensTypeDimension = explode("(", $tokensAttribute[1]);
                $tipo = $tokensTypeDimension[0];
                $dimensione = substr($tokensTypeDimension[1], 0, -1);
                
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

        function updatePrimaryKey($conn, $numRows, $idTabellaEsercizio, $tokensPrimaryKey) {
            if($numRows > 0) {                                
                foreach($tokensPrimaryKey as $value) {
                    $attribute = trim($value);
                    
                    /* condizioni per verificare se si tratti della prima oppure dell'ultima colonna che compone la chiave composta */
                    if(substr($attribute, 0, 1) == "(") {
                        $attribute = ltrim($attribute, "(");
                    } elseif(substr($attribute, -1) == ")") {
                        $attribute = substr($attribute, 0, -1);
                    }
                    
                    $storedProcedure = "CALL Aggiornamento_Chiave(:id, :attributo);";
                    
                    try {
                        $stmt = $conn -> prepare($storedProcedure);
                        $stmt -> bindValue(":id", $idTabellaEsercizio);
                        $stmt -> bindValue(":attributo", $attribute);
                        
                        $stmt -> execute();
                    } catch(PDOException $e) {
                        echo 'Eccezione '.$e -> getMessage().'<br>';
                    }
                }
            }
        }

        function splitPrimaryKey($sql) {
            $split = explode("(", $sql, 2);
            $splitting = substr($split[1], 0, -2);

            $tokensAttributesKey = explode("PRIMARY KEY", $splitting);
            $tokensPrimaryForeignKey = explode("FOREIGN KEY", $tokensAttributesKey[1]);
            $tokensPrimaryKey = explode(",", $tokensPrimaryForeignKey[0]);

            return $tokensPrimaryKey;
        }

        function insertForeignKey($conn, $numRows, $idTabellaEsercizio, $tokensForeignKey, $nameTableReferenced, $tokensTableReferenced) {
            //Ricordarsi di usare sempre trim quando si opera su valori dell'array
            if($numRows > 0) {
                foreach($tokensForeignKey as $value) {
                    $value = trim($value);
                    /* */
                }

                foreach($tokensTableReferenced as $value) {
                    $value = trim($value);
                    /* */
                }
            }
        }

        /* split che restituisce in ordine --> colonne della tabella referenziante, nome della tabella referenziata e colonne della tabella referenziata */
        function splitForeignKey($sql) {
            $split = explode("(", $sql, 2);
            $splitting = substr($split[1], 0, -2);

            $tokensAttributes = explode("PRIMARY KEY", $splitting);
            $tokensPrimaryForeignKey = explode("FOREIGN KEY", trim($tokensAttributes[1]));
            $tokensForeignKeyReferences = explode("REFERENCES", trim($tokensPrimaryForeignKey[1]));

            $tokensForeignKey = explode(",", trim($tokensForeignKeyReferences[0]));
            $tokensReferences = explode("(", trim($tokensForeignKeyReferences[1]));
            
            $nameTableReferenced = trim($tokensReferences[0]);
            $tokensTableReferenced = explode(",", trim($tokensReferences[1]));

            return array($tokensForeignKey, $nameTableReferenced, $tokensTableReferenced);
        }

        function insertTableExercise($conn, $nomeTabella) {
            $storedProcedure = "CALL Inserimento_Tabella_Esercizio(:nome, :dataCreazione, :numRighe);";

            try {
                $result = $conn -> prepare($storedProcedure);
                $result -> bindValue(":nome", $nomeTabella);
                $result -> bindValue(":dataCreazione", date("Y-m-d H:i:s"));
                $result -> bindValue(":numRighe", 0);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }

        function getIdTableExercise($conn, $nomeTabella) {
            $sql = "SELECT ID FROM Tabella_Esercizio WHERE (NOME=:nome);";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":nome", $nomeTabella);
                
                $result -> execute();
                $numRows = $result -> rowCount();
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }

            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $idTabellaEsercizio = $row['ID'];

            return array($numRows, $idTabellaEsercizio);
        }

        function redirectToTable() {
            header("Location: table_exercise.php");
        }

        function redirectToQuestion() {
            header("Location: question.php");
        }
        
        closeConnection($conn);
    ?>
</html>