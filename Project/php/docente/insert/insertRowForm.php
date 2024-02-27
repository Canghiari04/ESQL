<!DOCTYPE html>
<html>
    <head>           
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/insertRow.css">
        <?php 
            include 'addRow.php';
            include '../../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../table_exercise.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <form action="" method="POST">
            <div class="container">
                <div class="div-tips"> 
                    <textarea class="input-tips" disabled></textarea>
                </div>
                <div class="div-textbox-generative">
                    <?php             
                        $conn = openConnection();
 
                        if($_SERVER['REQUEST_METHOD'] == 'POST') {
                            if(isset($_POST['btnInsertForm'])) {
                                identifyAttributes($conn);
                            } elseif(isset($_POST['btnInsertData'])) {
                                insertData($conn);  
                                identifyAttributes($conn);
                            }
                        }
                    ?>
                </div>  
            </div>
            <button class="button-data" name="btnInsertData">Add</button>  
        </form>
    </body>
</html>