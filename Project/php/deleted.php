<!DOCTYPE html>
<html>
<?php 
    include 'connectionDB.php';
    $conn = openConnection();

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET["btnDropTable"])) {
            deleteTable($conn, $idTable = $_GET["btnDropTable"]);    
        }
    }
    else {
        echo '<script> alert("No Record / Data Found")</script>';
    }
                    
    /* mettere l'eliminazione nelle stored procedure*/
    function deleteTable($conn, $idTable){
        /* fare drop anche della tabella reale */
        $sql = "DELETE FROM TABELLA_ESERCIZIO WHERE ID = $idTable";
        $stmt = $conn -> prepare($sql);        
        $stmt -> execute();
                       
        closeConnection($conn);
        header("Location: table_exercise.php");
    }
?>