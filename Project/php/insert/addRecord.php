<?php 
    function insertRecord($conn, $sql, $nameTable) {
        /* flag utilizzato per eliminare la possibilità di inserimenti errati all'interno della tabella Attributo */
        $flagPrimaryKey = 0;

        /* rimozione di tutti i token superflui, per risalire alle colonne e ai vincoli di chiave */
        $tokens = explode("(", $sql, 2);
        $tokens = substr($tokens[1], 0, -2);
        $tokensQuery = explode(",", trim($tokens));

        /* metodo che restituisce l'id della tabella esercizio di riferimento */
        [$numRows, $idTableReferential] = getIdTableExercise($conn, $nameTable);

        foreach($tokensQuery as $value) {  
            /* split per ottenere i token che costituiscono la singola riga */
            $token = explode(" ", trim($value));

            if($token[0] == "PRIMARY") {
                $flagPrimaryKey = 1;
                //updatePrimaryKey($conn, $numRows, $idTableReferential, splitPrimaryKey($sql));
            } elseif ($token[0] == "FOREIGN") {
                /* TROVARE MODO PER INSERIMENTI DI PIU' VINCOLI DI CHIAVE ESTERNA */

                [$arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced] = splitForeignKey($value);
                echo ''.$nameTableReferenced.'<br>';
                //insertForeignKey($conn, $numRows, $idTableReferential, $arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced);
                break;
            } elseif($flagPrimaryKey == 0) {
                //insertAttribute($conn, $numRows, $idTableReferential, $token);
            }
        }

        header("Location: insertTable.php");
    }

    function insertAttribute($conn, $numRows, $idTableReferential, $tokensAttribute) {
        $primaryKey = 0;

        /* controllo per inserimento della singola chiave primaria */
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

    /* aggiornamento del campo Chiave_Primaria degli attributi che costituiscono il vincolo di primary key  */
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

    /* funzione restituente tutte le colonne che costituiscano la chiave primaria composta */
    function splitPrimaryKey($sql) {
        $split = explode("(", $sql, 2);
        $splitting = substr($split[1], 0, -2);

        $tokensAttributesKey = explode("PRIMARY KEY", $splitting);

        /* explode mediante la foreign key qualora sia presente, altrimenti restituisce la stessa stringa definita dall'explode per primary key  */
        $tokensPrimaryForeignKey = explode("FOREIGN KEY", $tokensAttributesKey[1]);
        $tokensPrimaryKey = explode(",", $tokensPrimaryForeignKey[0]);

        return $tokensPrimaryKey;
    }

    function insertForeignKey($conn, $numRows, $idTableReferential, $arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced) {
        if($numRows > 0) {
            /* ciclo for basato su un medesimo array dato lo stesso numero di variabili contenuto */
            for($i = 0; $i <= sizeof($arrayForeignKey) - 1; $i++) {
                $nameAttributeReferential = $arrayForeignKey[$i];
                $nameAttributeReferenced = $arrayAttributeReferenced[$i];

                /* individuazione dell'id della tabella referenziata, per la costruzione del vincolo di integrità */
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
                    echo 'Eccezione '.$e -> getMessage().'<br>';
                }

                /* acquisizione dei valori necessari per inserimento di record nella tabella Vincolo_Integrita, prima della collezione referenziante e poi della collezione referenziata */
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

    /* split che restituisce in ordine: colonne della tabella referenziante, nome della tabella referenziata e colonne della tabella referenziata */
    function splitForeignKey($rowQuery) {
        $tokensPrimaryForeignKey = explode("FOREIGN KEY", trim($rowQuery));
        $tokensForeignReferences = explode("REFERENCES", trim($tokensPrimaryForeignKey[1]));
        
        $tokensForeignKey = explode(",", trim($tokensForeignReferences[0]));
        $tokensReferences = explode("(", trim($tokensForeignReferences[1]));
        $nameTableReferenced = trim($tokensReferences[0]);
        $tokensTableReferenced = explode(",", trim($tokensReferences[1]));

        $arrayForeignKey = convertToArray($tokensForeignKey);
        $arrayAttributeReferenced = convertToArray($tokensTableReferenced);

        return array($arrayForeignKey, $nameTableReferenced, $arrayAttributeReferenced);
    }

    /* metodo che permette di convertire i token della foreign key negli attributi necessari per la realizzazione del vincolo di chiave esterna */
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

    /* inserimento della tabella di esercizio, riferita alla collezione di meta-dati */
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

    /* funzione restituente l'id della tabella e il numero di righe della query, per successive condizioni */
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