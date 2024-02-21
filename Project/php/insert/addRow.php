<?php
    function getAttributes($conn){    //permette di ottenere gli attributi relativi alla tabella interessata            
        $sql= 'SELECT * FROM ATTRIBUTO WHERE ID_TABELLA =:idTable ';
        $result = $conn -> prepare($sql);
        $result -> bindValue(':idTable', $_SESSION["IdTable"]);
        $result -> execute();
        $attributes = array();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $attribute = $row['NOME'];
            array_push($attributes,$attribute);
        }

        return $attributes;
    }

    function getAttributeType($attributeName,$conn){//permette di ottenere in tipo dell'attributo interessato

        try{
            $sql='SELECT * FROM ATTRIBUTO WHERE NOME = :nomeTabella AND ID_TABELLA = :idTabella'; 
            $result = $conn -> prepare($sql);
            $result -> bindValue(':nomeTabella', $attributeName);
            $result -> bindValue(':idTabella', $_SESSION["IdTable"]);
            $result -> execute();

            $row = $result -> fetch(PDO::FETCH_OBJ);
            $TipoAttributo = $row->TIPO;
            return $row->TIPO;


        }catch(PDOException $e){
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

    }

    function getTableName($conn){
        $sql= 'SELECT NOME FROM TABELLA_ESERCIZIO WHERE ID = :idTable ';
        try{
            $result = $conn -> prepare($sql);           
            $result -> bindValue(':idTable', $_SESSION['IdTable']);
            $result -> execute();

            $row = $result -> fetch(PDO::FETCH_OBJ);
            $nameTable= $row -> NOME;
            return $nameTable;

        }catch(PDOException $e){
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
        
       
    }

    function CheckAutoIncrement($conn){//restituisce un array contenente i nomi degli attributi settati AUTO INCREMENT 
        $nameTable = getTableName($conn);
        $sql = "SHOW COLUMNS FROM  ".$nameTable." WHERE Extra LIKE '%auto_increment%'";
        $result = $conn->prepare($sql);
        $result->execute();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC); 

        $columns = array();
        foreach ($rows as $row) {
            $column = $row['Field'];
            array_push($columns,$column);
        }

        return $columns;

    }

    function IdentifyAttributes($conn){//mettodo che permette di stampare le textbox per l'inserimento di dati nella tabella interessata
        $checkAutoInc = CheckAutoIncrement($conn);
        $nameTable =getTableName($conn);
        $valuesQuery='INSERT INTO '.$nameTable.' (';//scrittura intestazione query
        echo '
            <table class="">
        ';

        $attributes=getAttributes($conn);

        foreach($attributes as $value){
            if(!in_array($value,$checkAutoInc)){//vengono evitati gli attributi auto increment
                $valuesQuery=$valuesQuery. ''.$value.'' ;//vengono inseriti gli attributi interessati all'inserimento, concatenando all'intestazione scritta prima
                $valuesQuery=$valuesQuery. ', ' ;
                echo '
                    <tr>
                        <th><label for="txt'.$value.'">'.$value.':</label></th>
                        <th><input class="input" type="'.setTypeInput(getAttributeType($value,$conn)).'"  name="txt'.$value.'"></th>   
                    </tr>         
                ';
                }
        }
        echo '
            </table>
        ';
        //echo $valuesQuery;
        $valuesQuery=trim($valuesQuery);
        $valuesQuery=substr($valuesQuery, 0, -1);
        $valuesQuery=$valuesQuery.')';

        $_SESSION['HeadingInsert'] = $valuesQuery;//Settaggio variabile sessione in modo da poter salvare la intestazione della query per il momento in cui verranno inseriti i dati
    }

    function setTypeInput($i){//permette di settare l'input type in base al tipo di attributo
        switch ($i) {
            case "DATE":
                return "date";
                break;
            case "DATETIME":
                return "datetime-local";
                break;
            case "INT":
                return "number";
                break;
            default:
                return "text";
                break;
        }
    }

    function insertData($conn){//permette l'inserimento dei dati inseriti dal professore nel database
        $attributesText=array();
        $attributesInsert = getAttributes($conn);
        $attributesAutoIncrement=CheckAutoIncrement($conn);

        foreach($attributesInsert as $value){
            if(!in_array($value,$attributesAutoIncrement)){                    
                $s= 'txt'.$value.'';//vengono realizzati i nomi delle texbox evitando i nomi degli attributi auto increment, usati successivamente come indice nel metodo POST
                array_push($attributesText,$s);
            }
        }
        if(strstr($_SESSION['HeadingInsert'],'(')){//controllo per assicurarsi che la query abbia almeno un valore
            $StringDatas=''.$_SESSION["HeadingInsert"].' VALUES (';//sovrascrizione della stringa di intestazione

            foreach($attributesText as $value){//realizzazione completa con protezione da sql injection dinamica
                $StringDatas=$StringDatas. '?' ;
                $StringDatas=$StringDatas. ', ' ;
            }
               
            $StringDatas=trim($StringDatas);
            $StringDatas=substr($StringDatas, 0, -1);
            $StringDatas=$StringDatas.');';
            $index = 0;

        } else {//caso in cui la query non abbia nessun valore oltre agli attributi auto_increment
            $StringDatas= 'INSERT INTO '.getTableName($conn).' () VALUES ()';
        }

        try{
            $result = $conn -> prepare($StringDatas);
            foreach($attributesText as $value){
                $index = $index + 1;
                $result->bindValue($index, $_POST[$value]);
            }
        
            $result -> execute();
            echo "<script>document.querySelector('.input-tips').value='Inserimento eseguito correttamente!';</script>";

        }catch(PDOException $e) {
            echo "<script>document.querySelector('.input-tips').value='Rispettare la sintassi delle tabelle interessate!';</script>";
        }



    }
?>