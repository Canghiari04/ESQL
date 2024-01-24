<?php
    include 'connectionDB.php';
    $conn = OpenConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["txtEmailLogin"])) {
            $email = $_POST["txtEmailLogin"];
            $password = $_POST["txtPasswordLogin"];

            $sql = "SELECT COUNT(*) FROM Utente WHERE (EMAIL = '$email') AND (PSWD = '$password')";
            
        try {
            $result = mysqli_query($conn, $sql);
            $row = mysqli_num_rows($result);
            
            if(isset($row)){
                echo $row;
            }
        } catch(Exception $e) {
            echo 'Eccezione individuata: '. $e -> getMessage();
        }   
        } elseif (isset($_POST["txtEmailSignupDocente"])) {
            $email = $_POST["txtEmailSignup"];
            $password = $_POST["txtPasswordSignup"];
            $nome = $_POST["txtNomeSignup"];
            $cognome = $_POST["txtCognomeSignup"];
            $telefono = $_POST["txtTelefonoSignup"];
            $corso = $_POST["txtCorso"];
            $dipartimento = $_POST["txtDipartimento"];
            
            /* controllo mancante per la presenza o meno dell'account */
        } elseif (isset($_POST["txtEmailSignupStudente"])) {
            $email = $_POST["txtEmailSignup"];
            $password = $_POST["txtPasswordSignup"];
            $nome = $_POST["txtNomeSignup"];
            $cognome = $_POST["txtCognomeSignup"];
            $telefono = $_POST["txtTelefonoSignup"];
            $annoImmatricolazione = $_POST["txtAnnoImmatricolazione"];
            $codice = $_POST["txtCodice"];

            /* controllo mancante per la presenza o meno dell'account */
        }
    }
?>
