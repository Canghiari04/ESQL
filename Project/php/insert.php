<!DOCTYPE html>
<html>
    <?php 
        include 'connectionDB.php';
        $conn = openConnection();

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
        }
        
        closeConnection($conn);
    ?>
</html>