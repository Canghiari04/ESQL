<?php
    session_start();
    
    /* metodo che permette di creare i tag input necessari per l'inserimento di dati all'interno della collezione */
    function identifyAttributes($conn){
        $nameTable = getTableName($conn);

        $checkAutoInc = checkAutoIncrement($conn, $nameTable);

        /* scrittura intestazione della query */
        $valuesQuery = 'INSERT INTO '.$nameTable.' (';
        
        $attributes = getAttributes($conn);
        
        echo '
            <table class="">
        ';

        foreach($attributes as $value){
            /* tramite la condizione non sono stampati tutti i domini della collezione che risultino auto increment */
            if(!in_array($value, $checkAutoInc)) {
                /* sono creati i tag input necessari per l'inserimento di dati all'interno della collezione, attraverso la concatenazione dell'intestazione scritta prima */
                $valuesQuery = $valuesQuery.''.$value.'';
                $valuesQuery = $valuesQuery.', ';

                echo '
                    <tr>
                        <th><label for="txt'.$value.'">'.$value.':</label></th>
                        <th><input class="input" type="'.setTypeInput(getAttributeType($conn, $value)).'"  name="txt'.$value.'"></th>   
                    </tr>     
                ';    
            }
        }

        echo '
            </table>
        ';
        
        $valuesQuery = trim($valuesQuery);

        /* si elimina la virgola finale per concatenare la parentesi tonda chiusa, come da sintassi di mysql ')' */
        $valuesQuery = substr($valuesQuery, 0, -1);
        $valuesQuery = $valuesQuery.')';

        /* salvataggio tramite la sessione dell'intestazione della query, da cui verranno inseriti i dati */
        $_SESSION['headingInsert'] = $valuesQuery;
    }

    /* funzione che restituisce il nome della tabella in base al suo id contenuto nella collezione Tabella_Esercizio */
    function getTableName($conn) {
        $sql= 'SELECT NOME FROM Tabella_Esercizio WHERE ID = :idTabella;';

        try{
            $result = $conn -> prepare($sql);        

            $result -> bindValue(':idTabella', $_SESSION['idCurrentTable']);

            $result -> execute();
            $row = $result -> fetch(PDO::FETCH_OBJ);
        }catch(PDOException $e){
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }  

        $nameTable= $row -> NOME;
        return $nameTable;
    }

    function checkAutoIncrement($conn, $nameTable){
        /* vettore contenitivo di tutte le colonne legate al vincolo auto increment */
        $columns = array();
        
        /* query che restituisce tutte le colonne della tabella in questione che siano auto increment */
        $sql = "SHOW COLUMNS FROM ".$nameTable." WHERE Extra LIKE '%auto_increment%';";

        try {
            $result = $conn -> prepare($sql);
            
            $result -> execute();
            $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        foreach($rows as $row) {
            $column = $row['Field'];
            array_push($columns, $column);
        }

        return $columns;
    }
    
    /* metodo che garantisce l'acquisizione degli attributi della tabella interessata */
    function getAttributes($conn){ 
        $sql= 'SELECT * FROM Attributo WHERE ID_TABELLA = :idTabella;';
        
        $attributes = array();
        
        try {
            $result = $conn -> prepare($sql);

            $result -> bindValue(':idTabella', $_SESSION['idCurrentTable']);
            
            $result -> execute();
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        foreach ($rows as $row) {
            $attribute = $row['NOME'];
            array_push($attributes,$attribute);
        }

        return $attributes;
    }

    /* permette di settare l'input type in base al tipo di attributo */
    function setTypeInput($type){
        switch ($type) {
            case 'DATE':
                return 'date';
            case 'DATETIME':
                return 'datetime-local';
            case 'INT':
                return 'number';
            default:
                return 'text';
        }
    }

    function getAttributeType($conn, $attributeName){
        $sql = 'SELECT * FROM Attributo WHERE NOME = :nomeTabella AND ID_TABELLA = :idTabella;'; 

        try{
            $result = $conn -> prepare($sql);
            
            $result -> bindValue(':nomeTabella', $attributeName);
            $result -> bindValue(':idTabella', $_SESSION['idCurrentTable']);
            
            $result -> execute();

            $row = $result -> fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        return $row -> TIPO;
    }

    /* funzione che permette l'inserimento dei dati acquisiti da input all'interno della collezione di riferimento */
    function insertData($conn){
        $attributesText = array();
        $nameTable = getTableName($conn);
        
        $attributesInsert = getAttributes($conn);
        $attributesAutoIncrement = checkAutoIncrement($conn, $nameTable);

        foreach($attributesInsert as $value){
            /* tramite la condizione non sono stampati tutti i domini della collezione che risultino auto increment */
            if(!in_array($value, $attributesAutoIncrement)){                    
                $str = 'txt'.$value.'';
                array_push($attributesText, $str);
            }
        }

        /* controllo per assicurarsi che la query abbia almeno un valore,  */
        if(strstr($_SESSION['headingInsert'],'(')){
            /* sovrascrizione della stringa di intestazione  */
            $stringDatas=''.$_SESSION['headingInsert'].' VALUES (';

            /* realizzazione completa con protezione da sql injection dinamica */
            foreach($attributesText as $value){
                $stringDatas = $stringDatas. '?' ;
                $stringDatas = $stringDatas. ', ' ;
            }
               
            $stringDatas = trim($stringDatas);
            $stringDatas = substr($stringDatas, 0, -1);

            $stringDatas = $stringDatas.');';
            $index = 0;
        } 
        /* caso in cui la query non abbia nessun valore oltre agli attributi auto_increment */
        else {
            $stringDatas= 'INSERT INTO '.getTableName($conn).' () VALUES ()';
        }

        try{
            $result = $conn -> prepare($stringDatas);

            foreach($attributesText as $value){
                $index++;
                $result -> bindValue($index, $_POST[$value]);
            }
        
            $result -> execute();
            echo "<script>document.querySelector('.input-tips').value='INSERIMENTO AVVENUTO';</script>";
        }catch(PDOException $e) {
            echo "<script>document.querySelector('.input-tips').value='RISPETTARE LA SINTASSI DELLA TABELLA';</script>";
        }
    }
?>