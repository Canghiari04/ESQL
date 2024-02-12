<?php 
    function insertRecord($conn, $sql, $nameTable) {
        /* flag che consente il corretto inserimento dei record all'interno della tabella Attributo */
        $flagPrimaryKey = 0;

        /* rimozione dell'intestazione della query e dei due caratteri finali ");" */
        $tokens = explode("(", $sql, 2);
        $tokens = substr($tokens[1], 0, -2);

        /* split per ogni riga della query "...,", successiva alla rimozione di spazi iniziali e finali per ogni riga */
        $tokensQuery = explode(",", trim($tokens));

        /* metodo che restituisce l'id della tabella esercizio di riferimento */
        [$numRows, $idTableReferential] = getIdTableExercise($conn, $nameTable);

        foreach($tokensQuery as $value) {  
            /* split inerente allo spazio compreso tra nome e tipo della colonna */
            $token = explode(" ", trim($value));

            /* condizione che tratta vincoli di chiave primaria oppure esterna */
            if($token[0] == "PRIMARY") {
                /* set del flag a 1 per evitare si inserire colonne già presenti, a causa della sintassi del vincolo di chiave primaria */
                $flagPrimaryKey = 1;
                updatePrimaryKey($conn, $numRows, $idTableReferential, splitPrimaryKey($sql));
            } elseif ($token[0] == "FOREIGN") {
                /* trovare modo che gestisca FOREIGN KEY su più righe */
                [$arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced] = splitForeignKey($sql);
                insertForeignKey($conn, $numRows, $idTableReferential, $arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced);
                break;
            } elseif($flagPrimaryKey == 0) {
                insertAttribute($conn, $numRows, $idTableReferential, $token);
            }
        }

        header("Location: insertTable.php");
    }

    function insertAttribute($conn, $numRows, $idTableReferential, $tokensAttribute) {
        $primaryKey = 0;

        /* controllo per inserimento di una singola chiave primaria */
        if(in_array("PRIMARY", $tokensAttribute)) {
            $primaryKey = 1;
        }
        
        if($numRows > 0) { 
            $name = $tokensAttribute[0];
            
            /* split per ottenere tipo e dimensione dell'attributo */
            $tokensTypeDimension = explode("(", $tokensAttribute[1]);
            $type = $tokensTypeDimension[0];
            $dimension = substr($tokensTypeDimension[1], 0, -1);
            
            $storedProcedure = "CALL Inserimento_Attributo(:id, :tipo, :nome, :chiavePrimaria);";
            
            try {
                $stmt = $conn -> prepare($storedProcedure);

                $stmt -> bindValue(":id", $idTableReferential);
                $stmt -> bindValue(":tipo", $type);
                $stmt -> bindValue(":nome", $name);
                $stmt -> bindValue(":chiavePrimaria", $primaryKey);

                $stmt -> execute();
            } catch (PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        }
    }

    function updatePrimaryKey($conn, $numRows, $idTableReferential, $tokensPrimaryKey) {
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
                    $stmt -> bindValue(":id", $idTableReferential);
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

    function insertForeignKey($conn, $numRows, $idTableReferential, $arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced) {
        if($numRows > 0) {
            for($i = 0; $i <= sizeof($arrayForeignKey) - 1; $i++) {
                $nameAttributeReferential = $arrayForeignKey[$i];
                $nameAttributeReferenced = $arrayAttributeReferenced[$i];

                //echo ''.$nameAttributeReferential.'<br>'.$nameAttributeReferenced.'';

                [$numRows, $idTableReferenced] = getIdTableExercise($conn, $nameTableReferenced);
                
                $sqlReferential = "SELECT Attributo.ID FROM Attributo JOIN Tabella_Esercizio ON (Attributo.ID_TABELLA=Tabella_Esercizio.ID) WHERE (Attributo.ID_TABELLA=:idTabellaReferenziante) AND (Attributo.NOME=:nomeAttributoReferenziante)";
                $sqlReferenced = "SELECT Attributo.ID FROM Attributo JOIN Tabella_Esercizio ON (Attributo.ID_TABELLA=Tabella_Esercizio.ID) WHERE (Attributo.ID_TABELLA=:idTabellaReferenziata) AND (Attributo.NOME=:nomeAttributoReferenziato)";
                
                try {
                    $resultReferential = $conn -> prepare($sqlReferential);
                    $resultReferenced = $conn -> prepare($sqlReferenced);
                    
                    /* */
                    $resultReferential -> bindValue(":idTabellaReferenziante", $idTableReferential);
                    $resultReferential -> bindValue(":nomeAttributoReferenziante", $nameAttributeReferential);
                    
                    /* */
                    $resultReferenced -> bindValue(":idTabellaReferenziata", $idTableReferenced);
                    $resultReferenced -> bindValue(":nomeAttributoReferenziato", $nameAttributeReferenced);
                    
                    $resultReferential -> execute();
                    $resultReferenced -> execute();
                } catch(PDOException $e) {
                    echo 'Eccezione '.$e -> getMessage().'<br>';
                }

                $rowReferential = $resultReferential -> fetch(PDO::FETCH_ASSOC);
                $rowReferenced = $resultReferenced -> fetch(PDO::FETCH_ASSOC);
                
                $idAttributeReferential = $rowReferential['ID'];
                $idAttributeReferenced = $rowReferenced['ID'];

                $storedProcedure = "CALL Inserimento_Vincolo_Integrita(:idAttributoReferenziante, :idAttributoReferenziato)";

                try {
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue("idAttributoReferenziante", $idAttributeReferential);
                    $stmt -> bindValue("idAttributoReferenziato", $idAttributeReferenced);

                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo 'Eccezione '.$e -> getMessage().'<br>';
                }
            }
        }
    }

    /* split che restituisce in ordine --> colonne della tabella referenziante, nome della tabella referenziata e colonne della tabella referenziata */
    function splitForeignKey($sql) {
        $split = explode("(", $sql, 2);
        $splitting = substr($split[1], 0, -2);
        
        $tokensPrimaryForeignKey = explode("FOREIGN KEY", trim($splitting));
        $tokensForeignKeyReferences = explode("REFERENCES", trim($tokensPrimaryForeignKey[1]));
        
        $tokensForeignKey = explode(",", trim($tokensForeignKeyReferences[0]));
        
        $tokensReferences = explode("(", trim($tokensForeignKeyReferences[1]));
        $nameTableReferenced = trim($tokensReferences[0]);
        $tokensTableReferenced = explode(",", trim($tokensReferences[1]));

        $arrayForeignKey = convertToArray($tokensForeignKey);
        $arrayAttributeReferenced = convertToArray($tokensTableReferenced);


        return array($arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced);
    }

    function convertToArray($tokensForeignKey) {
        $array = array();

        foreach($tokensForeignKey as $value) {
            $attribute = trim($value);
            
            if(substr($attribute, 0, 1) == "(") {
                $attribute = ltrim($attribute, "(");
            } 
            if(substr($attribute, -1) == ")") {
                $attribute = substr($attribute, 0, -1);
            }
            
            array_push($array, $attribute);
        }

        return $array;
    }

    function insertTableExercise($conn, $nameTable) {
        $storedProcedure = "CALL Inserimento_Tabella_Esercizio(:nome, :dataCreazione, :numRighe);";

        try {
            $result = $conn -> prepare($storedProcedure);
            $result -> bindValue(":nome", $nameTable);
            $result -> bindValue(":dataCreazione", date("Y-m-d H:i:s"));
            $result -> bindValue(":numRighe", 0);
            
            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }

    function getIdTableExercise($conn, $nameTable) {
        $sql = "SELECT ID FROM Tabella_Esercizio WHERE (NOME=:nome);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":nome", $nameTable);
            
            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        $row = $result -> fetch(PDO::FETCH_ASSOC);
        $idTableReferential = $row['ID'];

        return array($numRows, $idTableReferential);
    }
?>