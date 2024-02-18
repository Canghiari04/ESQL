<?php
    function getAttributes($conn){                
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

    function getAttributeType($attributeName,$conn){

        try{
            $sql='SELECT * FROM ATTRIBUTO WHERE NOME = :nameTable AND ID_TABELLA = :idTable'; 
            $result = $conn -> prepare($sql);
            $result -> bindValue(':nameTable', $attributeName);
            $result -> bindValue(':idTable', $_SESSION["IdTable"]);
            $result -> execute();

            $row = $result -> fetch(PDO::FETCH_OBJ);
            $TipoAttributo = $row->TIPO;
            return $row->TIPO;


        }catch(PDOException $e){
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

    }

    function CheckAutoIncrement($conn){
        $sql= 'SELECT NOME FROM TABELLA_ESERCIZIO WHERE ID = :idTable ';
        $result = $conn -> prepare($sql);
        
        $result -> bindValue(':idTable', $_SESSION["IdTable"]);
        $result -> execute();

        $row = $result -> fetch(PDO::FETCH_OBJ);
        $nometab= $row -> NOME; 

        $sql = "SHOW COLUMNS FROM  ".$nometab." WHERE Extra LIKE '%auto_increment%'";
        $result = $conn->prepare($sql);
        $result->execute();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows

        $columns = array();
        foreach ($rows as $row) {
            $column = $row['Field'];
            array_push($columns,$column);
        }

        return [$columns, $nometab];

    }

    function IdentifyAttributes($conn){
        [$check,$nomeTabella] = CheckAutoIncrement($conn);
        $valuesQuery='INSERT INTO '.$nomeTabella.' (';
        echo '
        <form action="" method="POST">     
            <div class="center"> 
                <table class="">
        ';

        $attributes=getAttributes($conn);

        foreach($attributes as $value){
            if(!in_array($value,$check)){
                $valuesQuery=$valuesQuery. ''.$value.'' ;
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
                    <button class="button-insert" type="submit" name="btnInsertData" value="">Insert Row</button>  
                </div>
            </form>
        ';

        $valuesQuery=trim($valuesQuery);
        $valuesQuery=substr($valuesQuery, 0, -1);
        $valuesQuery=$valuesQuery.')';

        $_SESSION["IntestazioneInsert"] = $valuesQuery;
    }

    function setTypeInput($i){
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

    function insertData($conn){
        $attributesTxt=array();
        $attributesInsert = getAttributes($conn);
        [$attributesAutoIncrement,$nomeTabella]=CheckAutoIncrement($conn);

        foreach($attributesInsert as $value){
            if(!in_array($value,$attributesAutoIncrement)){                    
                $s= 'txt'.$value.'';
                array_push($attributesTxt,$s);
            }
        }
        $StringDatas=''.$_SESSION["IntestazioneInsert"].' VALUES (';

        foreach($attributesTxt as $value){
            $StringDatas=$StringDatas. '?' ;
            $StringDatas=$StringDatas. ', ' ;
        }
           
        $StringDatas=trim($StringDatas);
        $StringDatas=substr($StringDatas, 0, -1);
        $StringDatas=$StringDatas.');';
        $index = 0;
        try{
            $result = $conn -> prepare($StringDatas);
            foreach($attributesTxt as $value){
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