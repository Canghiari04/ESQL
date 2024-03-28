<?php
    function identifyAttributes($conn) {
        $nameTable = getTableName($conn);
        $attributes = getAttributes($conn);
        $notNullAttributes = getNotNull($conn, $nameTable); // acquisiti i field che abbiano come vincolo NOT NULL 
        $checkAutoInc = getAutoIncrement($conn, $nameTable); // acquisiti i field che abbiano come vincolo AUTO_INCREMENT
 
        $valuesQuery = "INSERT INTO ".$nameTable." ("; // scrittura dinamica della query adattiva rispetto alle caratteristiche della tabella
        
        echo '<table>';

        foreach($attributes as $value){
            if(!in_array($value, $checkAutoInc)) { // controllo che il field appartenga all'insieme dei domini sottoposti ad AUTO_INCREMENT
                $valuesQuery = $valuesQuery.''.$value.'';
                $valuesQuery = $valuesQuery.", ";
                
                echo '
                    <tr>
                        <th><label for="txt'.$value.'">'.$value.'</label></th>
                        <th>'.checkTypeInsert($conn, $value, $notNullAttributes).'</th>
                    </tr>     
                ';    
            }
        }

        echo '</table>';

        $valuesQuery = trim($valuesQuery);
        $valuesQuery = substr($valuesQuery, 0, -1);
        $valuesQuery = $valuesQuery.')';

        $_SESSION["headingInsert"] = $valuesQuery; // inizializzazione del campo della sessione ad intestazione della query in modo tale da garantire l'inserimento dei dati all'interno del database
    }

    function getTableName($conn) {
        $sql= "SELECT NOME FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=:idTabella);";

        try{
            $result = $conn -> prepare($sql);        
            $result -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);

            $result -> execute();
        }catch(PDOException $e){
            echo "Eccezione ".$e -> getMessage()."<br>";
        }  
        
        $row = $result -> fetch(PDO::FETCH_OBJ);
        return $row -> NOME;
    }

    function getAttributes($conn) { 
        $attributes = array();

        $sql = "SELECT * FROM Attributo WHERE (Attributo.ID_TABELLA=:idTabella);";
        
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rows = $result -> fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $attribute = $row["NOME"];
            array_push($attributes,$attribute);
        }

        return $attributes;
    }

    function getNotNull($conn, $nameTable) {
        $columns = array();
        
        $sql = "SHOW COLUMNS FROM ".$nameTable." WHERE `Null` = 'NO';"; // query attuata per estrapolare l'insieme dei domini sottoposti al vincolo NOT NULL

        try {
            $result = $conn -> prepare($sql);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        foreach($rows as $row) {
            $column = $row["Field"];
            array_push($columns, $column);
        }

        return $columns;
    }

    function getAutoIncrement($conn, $nameTable) {
        $columns = array();
        
        $sql = "SHOW COLUMNS FROM ".$nameTable." WHERE (Extra LIKE '%auto_increment%');"; // query attuata per estrapolare l'insieme dei domini sottoposti al vincolo AUTO_INCREMENT

        try {
            $result = $conn -> prepare($sql);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        foreach($rows as $row) {
            $column = $row["Field"];
            array_push($columns, $column);
        }

        return $columns;
    }

    function checkTypeInsert($conn, $nameAttribute, $notNullAttributes) { // controllo definito per accertarsi se l'attributo sia una foreign key o meno
        if(checkReferences($conn, getAttributeId($conn, $nameAttribute))) {
            return getReferencesOptions($conn, getAttributeId($conn, $nameAttribute), $nameAttribute);           
        } else if(checkEnums(getAttributeType($conn, $nameAttribute))) { // controllo definito per accertarsi se l'attributo sia un enum o meno
            return getEnumOptions($nameAttribute, getAttributeType($conn, $nameAttribute));    
        } else {
            return '<input class="input" type="'.setTypeInput(getAttributeType($conn, $nameAttribute)).'"  name="txt'.$nameAttribute.'" '.checkNotNull($nameAttribute, $notNullAttributes).' >';
        }
    }

    function checkReferences($conn, $idAttributeReferencing) {
        $sql = "SELECT * FROM Vincolo_Integrita WHERE (Vincolo_Integrita.REFERENTE=:idAttributo);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idAttributo", $idAttributeReferencing);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();
        if($numRows > 0) {
            return true;
        }
    }

    function getAttributeId($conn, $attributeName) {
        $sql = "SELECT * FROM Attributo WHERE (Attributo.NOME=:nomeTabella) AND (Attributo.ID_TABELLA=:idTabella);"; 

        try{
            $result = $conn -> prepare($sql);
            $result -> bindValue(":nomeTabella", $attributeName);
            $result -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $row = $result -> fetch(PDO::FETCH_OBJ);
        return $row -> ID;
    }

    function getReferencesOptions($conn, $idAttributeReferencing, $nameAttribute) { // funzione capace di stabilire l'insieme dei values che contraddistinguono una foreign key
        $sql = "SELECT * FROM Vincolo_Integrita WHERE (Vincolo_Integrita.REFERENTE=:idAttributo);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idAttributo", $idAttributeReferencing);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();
        if($numRows > 0) {
            $row = $result -> fetch(PDO::FETCH_OBJ);
            $idAttributeReferenced = $row -> REFERENZIATO;

            $sql = "SELECT NOME, ID_TABELLA FROM Attributo WHERE (Attributo.ID=:idAttributoReferenziato);"; // query in grado di estrapolare i meta-dati dell'attributo 

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":idAttributoReferenziato", $idAttributeReferenced);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $row = $result -> fetch(PDO::FETCH_OBJ);
            $nameAttributeReferenced = $row -> NOME;
            $idTableReferenced = $row -> ID_TABELLA;

            $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (Tabella_Esercizio.ID=:idTabellaReferenziata)"; // query attuata per individuare la tabella contenente l'attributo in evidenza

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":idTabellaReferenziata", $idTableReferenced);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $row = $result -> fetch(PDO::FETCH_OBJ);
            $nameTableReferenced = $row -> NOME;

            $sql = "SELECT DISTINCT ".$nameAttributeReferenced." FROM ".$nameTableReferenced.""; // query adottata per estrapolare tutti i valori che contiene l'attributo

            try {
                $result = $conn -> prepare($sql);
                
                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $string = '<select name="txt'.$nameAttribute.'" required>'; // costruzione della select dinamicamente, adattiva rispetto alle caratteristiche del'attributo

            $numRows = $result -> rowCount();
            if($numRows > 0) {
                while($row = $result -> fetch(PDO::FETCH_OBJ)){
                    $string = $string. "<option value=\"" . $row -> $nameAttributeReferenced . "\">" . $row -> $nameAttributeReferenced . "</option><br>";
                }
            }

            return $string."</select>";
        } 
    }

    function checkEnums($attributeType){
        if(substr_count($attributeType, "ENUM")){   
            return true;
        } else {
            return false;
        }

    }

    function getEnumOptions($nameAttribute, $attributeType){ // funzione capace di stabilire l'insieme dei values che contraddistinguono l'enum in question
        $attributeType = substr($attributeType, 0, -1);
        $tokens = explode('(', $attributeType); //explode che permettono di separare i valori tra di loro        
        $tokensOptions = explode(',',$tokens[1]);

        $string = '<select name="txt'.$nameAttribute.'" required>'; // costruzione della select dinamicamente, adattiva rispetto alle caratteristiche del'attributo

        foreach($tokensOptions as $value){
            $value = trim($value);//rimozione di spazi e degli apici che contraddistinguono ogni carattere
            $value = substr($value, 1);
            $value = substr($value, 0, -1);
        
            $string = $string. "<option value=\"" . $value . "\">" . $value . "</option><br>";
        }

        return $string."</select>";
    }

    function setTypeInput($type){
        switch ($type) {
            case "DATE":
                return "date";
            break;
            case "DATETIME":
                return "datetime-local";
            break;
            case "INT":
                return "number";
            break;
            case "DOUBLE":
                return "number";
            break;
            default:
                return "text";
            break;
        }
    }

    function getAttributeType($conn, $attributeName) {
        $sql = "SELECT * FROM Attributo WHERE (Attributo.NOME=:nomeTabella) AND (Attributo.ID_TABELLA=:idTabella);"; 

        try{
            $result = $conn -> prepare($sql);
            $result -> bindValue(":nomeTabella", $attributeName);
            $result -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
        
        $row = $result -> fetch(PDO::FETCH_OBJ);
        return $row -> TIPO;
    }

    function checkNotNull($nameAttribute, $notNullAttributes) { // controllo che l'attributo appartenga all'insieme dei domini sottoposti al vincolo NOT_NULL
        if(in_array($nameAttribute, $notNullAttributes)) {
            return "required";
        }
    }

    function insertData($conn) {
        $attributesText = array();

        $nameTable = getTableName($conn);
        $attributesInsert = getAttributes($conn);
        $attributesAutoIncrement = getAutoIncrement($conn, $nameTable);

        foreach($attributesInsert as $value) {
            if(!in_array($value, $attributesAutoIncrement)) {                     
                $str = "txt".$value."";
                array_push($attributesText, $str);
            }
        }

        if(strstr($_SESSION["headingInsert"], '(')) { // controllo attuato per accertarsi che la query abbia almeno un valore
            $stringDatas = ''.$_SESSION["headingInsert"]." VALUES (";

            foreach($attributesText as $value) { // ciclo definito per ricreare l'inserimento di dati ovviando a dipendency injection
                $stringDatas = $stringDatas. '?' ;
                $stringDatas = $stringDatas. ", " ;
            }
               
            $stringDatas = trim($stringDatas);
            $stringDatas = substr($stringDatas, 0, -1);

            $stringDatas = $stringDatas.");";
            $index = 0;
        } 
        else { // ramo del costrutto attuato qualora tutti gli attributi della tabella siano sottoposti al vincolo AUTO_INCREMENT
            $stringDatas = "INSERT INTO ".getTableName($conn)." () VALUES ()";
        }

        try {
            $result = $conn -> prepare($stringDatas);

            foreach($attributesText as $value) {
                $index++;
                $result -> bindValue($index, $_POST[$value]);
            }
        
            $result -> execute(); 

            echo "<script>document.querySelector('.input-tips').value='INSERIMENTO DATI AVVENUTO CON SUCCESSO';</script>";

            $storedProcedure = "CALL Inserimento_Manipolazione_Riga(:idTabella);"; // modifica del numero di righe della tabella solamente se l'inserimento va a buon fine
        
            try {
                $storedProcedure = $conn -> prepare($storedProcedure);
                $storedProcedure -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
                                        
                $storedProcedure -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        } catch(PDOException $e) {
            echo "<script>document.querySelector('.input-tips').value=".json_encode($e -> getMessage(), JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS).";</script>";
        }
    }
?>