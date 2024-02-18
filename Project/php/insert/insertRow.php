<?php 
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            <?php 
                include '../connectionDB.php';
            ?>
        </style>
    </head>
    <body>
        <?php             
            $conn = openConnection();
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if (isset($_GET["btnInsertRow"])) {
                    $att=$_GET["btnInsertRow"];
                    $_SESSION["IdTable"]=$att;
                    IdentifyAttributes($conn,$att);
                    

                }

            }


            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                if (isset($_POST["btnInsertData"])) {
                    $attributesTxt=array();
                    $attributesInsert = getAttributes($conn);
                    [$attributesAutoIncrement,$gay,$attributesNotNull]=CheckAutoIncrement($conn);

                    foreach($attributesInsert as $value){
                        if(!in_array($value,$attributesAutoIncrement)){                    
                            $s= 'Txt'.$value.'';
                            //echo ''.$s.'<br>';
                            array_push($attributesTxt,$s);
                        }
                    }
                    $StringDatas=''.$_SESSION["IntestazioneInsert"].' VALUES (';

                    foreach($attributesTxt as $value){
                        $s=$_POST[$value];
                        $StringDatas=$StringDatas. '"'.$s.'"' ;
                        $StringDatas=$StringDatas. ', ' ;
                    }
                    
                    $StringDatas=trim($StringDatas);
                    $StringDatas=substr($StringDatas, 0, -1);
                    $StringDatas=$StringDatas.');';

                    echo $StringDatas.'<br>';
                    try{
                        $result = $conn -> prepare($StringDatas);
                        $result -> execute();

                    }catch(PDOException $e) {
                        echo 'RISPETTA LA SINTASSI DELLA TABELLA UTILIZZATA<br>';
                    }


                    

                }


            }

            function getAttributes($conn){                
                $sql= 'SELECT * FROM ATTRIBUTO WHERE ID_TABELLA ='.$_SESSION["IdTable"].' ';
                $result = $conn -> prepare($sql);
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
                    $sql='SELECT * FROM ATTRIBUTO WHERE NOME = "'.$attributeName.'" AND ID_TABELLA = '.$_SESSION["IdTable"].''; 
                    $result = $conn -> prepare($sql);
                    $result -> execute();

                    $row = $result -> fetch(PDO::FETCH_OBJ);
                    $TipoAttributo = $row->TIPO;
                    return $row->TIPO;


                }catch(PDOException $e){
                    echo 'Eccezione '.$e -> getMessage().'<br>';
                }

            }





            function CheckAutoIncrement($conn){
                $sql= 'SELECT NOME FROM TABELLA_ESERCIZIO WHERE ID ='.$_SESSION["IdTable"].' ';
                $result = $conn -> prepare($sql);
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

                $sql = "SHOW COLUMNS FROM ".$nometab." WHERE Null = 'NO'";
                $result = $conn->prepare($sql);
                $result->execute();

                $rows = $result->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows

                $columnsNull = array();
                foreach ($rows as $row) {
                    $columnNull = $row['Field'];
                    $columnNull= ''. $columnNull.'Txt';
                    array_push($columnsNull,$columnNull);
                }




                return [$columns, $nometab,$columnsNull];

            }

            function IdentifyAttributes($conn){
                [$check,$nomeTabella,$attributesNotNull] = CheckAutoIncrement($conn);
                $valuesQuery='INSERT INTO '.$nomeTabella.' (';
                echo '
                <form action="" method="POST">     
                <div class="">
                    <table class="">
                ';

                $attributes=getAttributes($conn);

                foreach($attributes as $value){
                    if(!in_array($value,$check)){
                        $valuesQuery=$valuesQuery. ''.$value.'' ;
                        $valuesQuery=$valuesQuery. ', ' ;
                        echo '
                            <tr>
                                <th><label for="Txt'.$value.'">'.$value.':</label></th>
                                <th><input type="'.setTypeInput(getAttributeType($value,$conn)).'"  name="Txt'.$value.'"></th>   
                            </tr>         
                        ';
                        }
                }
                echo '
                    </table>
                    <button class="" type="submit" name="btnInsertData" value="">Insert Row</button>  
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
                        return "datetime";
                        break;
                    case "INT":
                        return "number";
                        break;
                    default:
                        return "text";
                        break;
                }


            }


        ?>
    </body>
</html>