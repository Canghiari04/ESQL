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
                buildForm('AddTable');
                /* inserimento di una nuova tabella esercizio da parte del docente 
                    - guardare file addRecord.php
                */
            } elseif(isset($_POST["btnInsertQuestion"])) {
                buildNavbar("question");
                buildForm("AddQuestion");

                /* permettere l'inserimento mediante query scritta dal docente, con accorgimenti relativi alla sintassi oppure fornire lo scheletro della query 
                    - controlli che siano rispettati i vari constraint
                    - controlli che indicano se si tratta di una domanda codice oppure di una domanda chiusa
                    - controlli che stabiliscano che i dati selezionati siano esistenti 
                */
            } elseif(isset($_POST["btnAddTable"])) {
            try{
                $sql=strtoupper($_POST["txtAddTable"]);
                $pieces = explode(" ", $sql);
                if ($pieces[0] == "CREATE"){
                    $result = $conn -> prepare($sql);
                    $result -> execute();
                    $splitPk= (explode("PRIMARY KEY", $sql,2));
                    $splittedPk = trim($splitPk[1]);
                    $splittedPk = substr($splittedPk, 0, -2); 

                    $sql2 = "INSERT INTO TABELLA_ESERCIZIO VALUES (NULL,'$pieces[2]', '" . date("Y-m-d H:i:s") . "', 0)";                      
                    $result = $conn -> prepare($sql2);
                    $result -> execute();

                    $attrs = explode("(", $sql, 2);//SPLIT PER RIMUOVERE intestazione query
                    $s = substr($attrs[1], 0, -2);   //tolti ultimi due caratteri dalla stringa trimmata  
                         
                    $attrs = explode(",", $s);//split per ottenere gli attributi e tipo
                    foreach($attrs as $value){//for each per ogni attributo
                        $s=trim($value);
                        //echo ''.$s.'0000'; 
                        $stz = explode(" ", $s);//split tra nome e tipo
                        
                        if(count($stz)==2){
                            $sql="SELECT ID FROM TABELLA_ESERCIZIO WHERE NOME='$pieces[2]'" ;
                            $result = $conn -> prepare($sql);
                            $result -> execute();
                            $numRows = $result -> rowCount();
                            if($numRows==1){
                                $row = $result->fetch(PDO::FETCH_ASSOC);
                                $id = $row['ID'];                            
                                $nome=$stz[0];
                                
                                $tipodim = explode("(", $stz[1],2);//split tra tipo e dimensione
                                
                                $tipo=$tipodim[0];
                                $sql="INSERT INTO ATTRIBUTO VALUES (NULL,'$id','$tipo','$nome', 0)";
                                $result = $conn -> prepare($sql);
                                $result -> execute();
                            }
                        }else if($stz[0]=="PRIMARY"){

                                $pk=explode("(",$s,2); 
                                     
                                $splittenPk= explode(",",$splittedPk);
                                
                                foreach($splittenPk as $value){
                                    $value=trim($value);
                                    if(substr($value, -1)==")"){
                                        
                                        $value = substr($value, 0, -1);
                                        //echo ''.$value.'';
                                        
                                    } else if(substr($value, 0, 1)=="("){
                                        $value=ltrim($value, '(');
                                    }
                                    echo ''.$value.'';
                                    $sql = "UPDATE ATTRIBUTO SET CHIAVE_PRIMARIA = 1 WHERE NOME= '$value'";
                                    $result = $conn -> prepare($sql);
                                    $result -> execute();
                                }
                                
                            }
                        }
                    }



                    
                 else{
                    echo 'Sono valide solo query CREATE';
                }
            }catch (PDOException $e){
                echo 'Eccezione: '. $e -> getMessage(); 

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

        /* funzione per rendere adattivo il form in base a quale record debba essere inserito nelle tabelle del database */
        function buildForm($value) {
            echo '
                <form action="" method="POST">
                    <div class="container">
                        <div class="div-tips">
                            <input class="input-tips">
                        </div>
                        <div class="div-textbox">
                            <input class="input-textbox" type="text" name="txt'.$value.'" required>
                        </div>
                    </div>
                    <button type="submit" name="btn'.$value.'">Add</button>
                </form>
            ';
        }
        
        closeConnection($conn);
    ?>
</html>