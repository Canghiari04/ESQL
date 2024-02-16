<?php    
    function openConnection() {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=ESQLDB', 'root', 'password');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $pdo;
        }
        catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
            return null;
        }
    }

    function openConnectionMongoDB() {
        date_default_timezone_set('Europe/Rome');

        $mongoHost = 'localhost';
        $mongoPort = 27017;

        $manager = new MongoDB\Driver\Manager("mongodb://$mongoHost:$mongoPort");

        return $manager;
    }

    function writeLog($manager, $document) {
        $mongoDatabase = 'ESQL';
        $mongoCollection = 'logs';

        $bulkWrite = new MongoDB\Driver\BulkWrite;
        $bulkWrite->insert($document);

        try{
            $manager->executeBulkWrite("$mongoDatabase.$mongoCollection", $bulkWrite);
        } catch (Exception $e){
            //echo($e -> getMessage());
        }
    }
    
    function closeConnection($pdo) {
        $pdo = null;
    }
?> 