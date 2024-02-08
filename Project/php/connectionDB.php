<?php
    function openConnection() {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=ESQLDB','root', '');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $pdo;
        }
        catch(PDOException $e) {
            echo("Eccezione ". $e -> getMessage());
            return null;
        }
    }

    function closeConnection($pdo) {
        $pdo = null;
    }
?> 