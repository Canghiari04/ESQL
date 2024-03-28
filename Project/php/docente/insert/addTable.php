<?php 
    function insertTableExercise($conn, $nameTable, $email) {
        $storedProcedure = "CALL Inserimento_Tabella_Esercizio(:nome, :dataCreazione, :numRighe, :emailDocente);";

        try {
            $result = $conn -> prepare($storedProcedure);
            $result -> bindValue(":nome", $nameTable);
            $result -> bindValue(":dataCreazione", date("Y-m-d H:i:s"));
            $result -> bindValue(":numRighe", 0);
            $result -> bindValue(":emailDocente", $email);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    function insertRecord($conn, $sql, $nameTable) {
        $flagPrimaryKey = false; // flag utilizzato per ovviare ad inserimenti errati nelle tabelle contenenti meta-dati

        [$sql, $enums] = checkEnum($conn, $sql, $nameTable);

        $tokens = explode('(', $sql, 2); // explode necessari per estrapolare tutti i token utili per l'inserimento all'interno delle tabell
        $tokens = substr($tokens[1], 0, -2);
        $tokensQuery = explode(',', trim($tokens));

        [$numRows, $idTableReferential] = getIdTableExercise($conn, $nameTable); // funzione attuata per restituire l'id della tabella esercizio da poco inserita nella collezione Tabella_Esercizio

        foreach($tokensQuery as $value) {  
            $token = explode(' ', trim($value)); // explode necessario per acquisire i token di ogni riga della query

            if($token[0] == "PRIMARY") {
                $flagPrimaryKey = true;

                updatePrimaryKey($conn, $numRows, $idTableReferential, splitPrimaryKey($sql)); // metodo attuato per modificare il vincolo di chiave primaria degli attributi già inseriti
            } elseif ($token[0] == "FOREIGN") {
                insertForeignKey($conn, $sql, $numRows, $idTableReferential);
                break; // break necessario per interrompere il ciclo
            } elseif($flagPrimaryKey == false) { // controllo ideato per inserire gli attributi precedenti al vincolo PRIMARY KEY
                $deleteTableBool = insertAttribute($conn, $numRows, $idTableReferential, $token);

                if($deleteTableBool == 0){
                    break;
                }
            }
        }     
        
        if(sizeof($enums) > 0){
            insertEnums($conn, $enums, $nameTable);//inserimento dei valori di tipo enum, se presenti  
        }
    }

    function checkEnum($conn, $sql, $nameTable){//funzione che permette di controllare la presenza di enum alll'interno della queri
        $numberEnum = substr_count($sql, "ENUM");
        $enums = array();//array dove verranno inseriti i dati relativi agli enum

        for($i=0;$i<$numberEnum; $i++){
            $positionEnum = strpos($sql, "ENUM");//individuando la presenza dell'enum, vengono calcolate le posizioni dei due caratteri che delimitano il nome dell'attributo
            $spaceBeforeEnum = strrpos(substr($sql, 0, $positionEnum), ' ');
            $spaceBeforeAttribute = strrpos(substr($sql, 0, $spaceBeforeEnum - 1), ',');  

            if($spaceBeforeAttribute == ""){//nel caso in cui il nome non sia preceduto da una virgola, esso sarà il primo attributo della lista
                $spaceBeforeAttribute = strpos($sql, '(');
            }
            
            $startPosition = strpos($sql, "(", $positionEnum);//individuazione della prima parentesi aperta  in seguito ad enum
            $endPosition = strpos($sql, ")", $startPosition);//individuazione della prima parentesi chiusa in seguito ad enum
            $finalPosition = strpos($sql, ",", $endPosition);//individuazione della virgola in seguito alla parentesi di chiusura, necessaria nel caso ci siano attributi in seguito

            if($finalPosition==null){
                $finalPosition = $endPosition;//sovrascrizione del carattere finale nel caso l'attributo contenente l'enum sia l'ultimo dell'elenco
                $commaBeforeAttribute = strrpos(substr($sql, 0, $spaceBeforeAttribute + 1), ',');
                $sql = substr_replace($sql, " ", $commaBeforeAttribute, 1);//rimozione della virgola precedente all'ultimo attributo 
            }

            $enumData = substr($sql, $spaceBeforeAttribute + 1, $finalPosition - ($spaceBeforeAttribute + 1) + 1);//i dati relativi all'enum vengono inseriti in una stringa
            $sql = substr_replace($sql, "", $spaceBeforeAttribute + 1, $finalPosition - ($spaceBeforeAttribute + 1) + 1);//i dati relativi all'enum vengono rimossi dalla query originale

            array_push($enums, $enumData);
        }

        return [$sql, $enums];//viene restituita la query originale senza gli enum e un array contenente questi ultimi
    }

    function insertEnums($conn, $enums, $nameTable){//metodo che permette di inserire gli enum
        [$numRows, $idTable] = getIdTableExercise($conn, $nameTable);

        foreach($enums as $value){
            $value = trim($value);

            if(substr($value, -1) == ',') { 
                $value = substr($value, 0, -1);//rimozione della virgola finale nel caso sia presente 
            }  

            $tokens = explode(' ',$value, 2);// separazione tra nome attributo e tipologia contente i possibili valori
            $storedProcedure = "CALL Inserimento_Attributo(:id, :tipo, :nome, :chiavePrimaria);";
            
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(":id", $idTable);
                $stmt -> bindValue(":tipo", $tokens[1]);
                $stmt -> bindValue(":nome", $tokens[0]);
                $stmt -> bindValue(":chiavePrimaria", 0);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

        }
    }

    function getIdTableExercise($conn, $nameTable) { 
        $sql = "SELECT ID FROM Tabella_Esercizio WHERE NOME=:nomeTabella;";
    
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":nomeTabella", $nameTable);
            
            $result -> execute();

            $numRows = $result -> rowCount();
            if ($numRows > 0) {
                $row = $result -> fetch(PDO::FETCH_OBJ);
                $idTableReferential = $row -> ID;
            } else {
                return [0, null];
            }
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
            return [0, null];
        }
    
        return [$numRows, $idTableReferential];
    }

    function updatePrimaryKey($conn, $numRows, $idTableReferential, $tokensPrimaryKey) { // metodo definito per aggiornare il vincolo di chiave primaria sugli attributi inseriti
        if($numRows > 0) {                                
            foreach($tokensPrimaryKey as $value) {
                $attribute = trim($value);
                
                if(substr($attribute, 0, 1) == '(') { // rimozione di caratteri superflui
                    $attribute = ltrim($attribute, '(');
                } elseif(substr($attribute, -1) == ')') {
                    $attribute = substr($attribute, 0, -1);
                    if(substr($attribute, -1) == ')') { // costrutto condizionale necessario per mantenere la corretta sintassi della query in caso di spazi aggiuntivi
                        $attribute = substr($attribute, 0, -1);
                    }
                }
                
                $storedProcedure = "CALL Aggiornamento_Chiave(:id, :attributo);";
                
                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":id", $idTableReferential);
                    $stmt -> bindValue(":attributo", $attribute);
                    
                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
            }
        }
    }

    function splitPrimaryKey($sql) { // funzione definita per estrapolare tutti i domini che costituiscano la chiave primaria
        $split = explode('(', $sql, 2);
        $splitting = substr($split[1], 0, -2);

        $tokensAttributesKey = explode("PRIMARY KEY", $splitting);
        $tokensPrimaryForeignKey = explode("FOREIGN KEY", $tokensAttributesKey[1]);
        $tokensPrimaryKey = explode(',', $tokensPrimaryForeignKey[0]);

        return $tokensPrimaryKey;
    }

    function insertForeignKey($conn, $sql, $numRows, $idTableReferential) {
        $tokensQuery = explode("FOREIGN KEY", substr($sql, 0, -2));

        for($i = 1; $i < sizeof($tokensQuery); $i++) {
            [$arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced] = splitForeignKey(trim($tokensQuery[$i]));
            updateForeignKey($conn, $numRows, $idTableReferential, $arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced);
        }
    }

    function splitForeignKey($tokensQuery) { // funzione attuata per risalire alle colonne della tabella referenziante nome della tabella referenziata e colonne della tabella referenziata
        $tokensForeignReferences = explode("REFERENCES", trim($tokensQuery));
        $tokensForeignKey = explode(',', trim($tokensForeignReferences[0]));

        $tokensReferences = explode('(', trim($tokensForeignReferences[1]));
        $nameTableReferenced = trim($tokensReferences[0]);
        $tokensTableReferenced = explode(',', trim($tokensReferences[1]));
        
        $arrayForeignKey = convertToArray($tokensForeignKey);
        $arrayAttributeReferenced = convertToArray($tokensTableReferenced);

        return [$arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced];
    }

    function convertToArray($tokensForeignKey) { // funzione ideata per convertire i token della foreign key negli attributi necessari per la realizzazione della chiave esterna
        $array = array();

        foreach($tokensForeignKey as $value) {
            $attribute = trim($value);
            
            if(substr($attribute, 0, 1) == '(') {
                $attribute = ltrim($attribute, '(');
            } 

            if(substr($attribute, -1) == ')') {
                $attribute = substr($attribute, 0, -1);
                if(substr($attribute, -1) == ')') { // costrutto condizionale necessario per mantenere la corretta sintassi della query in caso di spazi aggiuntive
                    $attribute = substr($attribute, 0, -1);
                }
            }

            if(!is_null($attribute)) {
                array_push($array, $attribute);
            }
        }

        return $array;
    }

    function updateForeignKey($conn, $numRows, $idTableReferential, $arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced) { // metodo definito per aggiornare il vincolo di chiave esterna sugli attributi inseriti
        if($numRows > 0) {
            for($i = 0; $i <= sizeof($arrayForeignKey) - 1; $i++) {
                $nameAttributeReferential = $arrayForeignKey[$i];
                $nameAttributeReferenced = $arrayAttributeReferenced[$i];

                [$numRows, $idTableReferenced] = getIdTableExercise($conn, $nameTableReferenced);

                $sqlReferential = "SELECT Attributo.ID FROM Attributo JOIN Tabella_Esercizio ON (Attributo.ID_TABELLA=Tabella_Esercizio.ID) WHERE (Attributo.ID_TABELLA=:idTabellaReferenziante) AND (Attributo.NOME=:nomeAttributoReferenziante)";
                $sqlReferenced = "SELECT Attributo.ID FROM Attributo JOIN Tabella_Esercizio ON (Attributo.ID_TABELLA=Tabella_Esercizio.ID) WHERE (Attributo.ID_TABELLA=:idTabellaReferenziata) AND (Attributo.NOME=:nomeAttributoReferenziato)";
                
                try {
                    $resultReferential = $conn -> prepare($sqlReferential);
                    $resultReferenced = $conn -> prepare($sqlReferenced);
                    
                    $resultReferential -> bindValue(":idTabellaReferenziante", $idTableReferential);
                    $resultReferential -> bindValue(":nomeAttributoReferenziante", $nameAttributeReferential);
                    
                    $resultReferenced -> bindValue(":idTabellaReferenziata", $idTableReferenced);
                    $resultReferenced -> bindValue(":nomeAttributoReferenziato", $nameAttributeReferenced);
                    
                    $resultReferential -> execute();
                    $resultReferenced -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                $rowReferential = $resultReferential -> fetch(PDO::FETCH_OBJ);
                $rowReferenced = $resultReferenced -> fetch(PDO::FETCH_OBJ);
                
                $idAttributeReferential = $rowReferential -> ID;
                $idAttributeReferenced = $rowReferenced -> ID;
                
                $storedProcedure = "CALL Inserimento_Vincolo_Integrita(:idAttributoReferenziante, :idAttributoReferenziato)";

                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue("idAttributoReferenziante", $idAttributeReferential);
                    $stmt -> bindValue("idAttributoReferenziato", $idAttributeReferenced);

                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
            }
        }
    }

    function insertAttribute($conn, $numRows, $idTableReferential, $tokensAttribute) {
        $primaryKey = 0;

        if(in_array("PRIMARY", $tokensAttribute)) { // controllo stabilito per accertarsi che un solo attributo componga la chiave primaria
            $primaryKey = 1;
        }
        
        if($numRows > 0) { 
            $name = $tokensAttribute[0];
            
            $tokensTypeDimension = explode('(', $tokensAttribute[1]);
            $type = $tokensTypeDimension[0];

            if(sizeof($tokensTypeDimension) > 1){
                $dimension = substr($tokensTypeDimension[1], 0, -1);
            }
            
            $storedProcedure = "CALL Inserimento_Attributo(:id, :tipo, :nome, :chiavePrimaria);";
            
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(":id", $idTableReferential);
                $stmt -> bindValue(":tipo", $type);
                $stmt -> bindValue(":nome", $name);
                $stmt -> bindValue(":chiavePrimaria", $primaryKey);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        }
        
        $validity = 1; // variabile utilizzata come riscontro dell'inserimento

        if(in_array("REFERENCES", $tokensAttribute)) { // controllo foreign key in linea, visualizzata durante la dichiarazione dell'attributo 
            $sql = "SELECT ID FROM Attributo WHERE (Attributo.ID_TABELLA=:idTabellaReferenziante) AND (Attributo.NOME=:nomeAttributoReferenziante);";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":idTabellaReferenziante", $idTableReferential);
                $result -> bindValue(":nomeAttributoReferenziante", $name);

                $result -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $idAttributeReferential = $row["ID"];
        
        
            $validity = insertReference($conn, $idAttributeReferential, $idTableReferential, $tokensAttribute[3]);
        }

        return $validity;
    }

    function insertReference($conn, $idAttributeReferential, $idTableReferential, $tokensAttributeReferenced) { // funzione attuata per inserire il vincolo di chiave esterna qualora sia composto da un solo attributo
        $tokensTableReferenced = explode('(', $tokensAttributeReferenced);

        [$num, $idTableReferenced] = getIdTableExercise($conn, $tokensTableReferenced[0]);

        $attributeReferenced = "";

        if(sizeof($tokensTableReferenced) > 1){
            $attributeReferenced = rtrim($tokensTableReferenced[1], ')');
        }

        if(($idTableReferenced == "" || $attributeReferenced == "")) { // controllo stabilito per accertarsi della validità dei dati inseriti rispetto a quelli esistenti
            echo "<script>document.querySelector('.input-tips').value='VINCOLO INTEGRITA NON ESISTENTE. RICREARE LA TABELLA CON DEI VALORI PRESENTI NEL DATABASE';</script>";

            deleteTable($conn, $idTableReferential);
            deleteTableExercise($conn, $idTableReferential);
            return 0;
        } else {
            $sql = "SELECT ID FROM Attributo WHERE (Attributo.ID_TABELLA=:idTabellaReferenziata) AND (Attributo.NOME=:nomeAttributoReferenziato);";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":idTabellaReferenziata", $idTableReferenced);
                $result -> bindValue(":nomeAttributoReferenziato", $attributeReferenced);
    
                $result -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
            
            $numRows = $result -> rowCount();
            if($numRows > 0) {
                $row = $result -> fetch(PDO::FETCH_OBJ);
                $idAttributeReferenced = $row -> ID;
                
                $storedProcedure = "CALL Inserimento_Vincolo_Integrita(:idAttributoReferenziante, :idAttributoReferenziato)";
                
                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue("idAttributoReferenziante", $idAttributeReferential);
                    $stmt -> bindValue("idAttributoReferenziato", $idAttributeReferenced);
                        
                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                return 1;
            }       
        }

        function deleteTable($conn, $id) { // metodo definito affinchè venga cancellata la tabella nel caso di riscontro negativo da inserimento
            $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:id);";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":id", $id);
    
                $result -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
    
            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $nome = $row["NOME"];
    
            $sql = "DROP TABLE ".$nome.";";
    
            try {
                $result = $conn -> prepare($sql);
    
                $result -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        }
    
        function deleteTableExercise($conn, $id) { // metodo definito affinchè venga cancellato il record all'intero della collezione Tabella_Esercizio nel caso di riscontro negativo da inserimento
            $storedProcedure = "CALL Eliminazione_Tabella_Esercizio(:id);";
                
            try {
                $stmt = $conn -> prepare($storedProcedure);
                $stmt -> bindValue(":id", $id);
    
                $stmt -> execute();
            } catch (PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        }
    }
?>