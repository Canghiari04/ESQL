<?php
    function OpenConnection() {
        $dbhost = "localhost";
        $dbuser = "root";
        $dbpassword = "password";
        $db = "ESQLDB";
        try {
            $conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $db);
            return $conn;
        } catch(mysqli_sql_exception $e) {
            echo 'Eccezione individuata: '. $e -> getMessage();
        }
    }

    function CloseConnection($conn) {
        mysqli_close($conn);
    }
?> 