<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
    <style>
        html, body {
            font-family: 'Public Sans';
            height: 100%;
        }

        .navbar {
            height: 100%;
            width: 10%;
            padding-bottom: 20px;
            position: fixed;
            z-index: 1;
            background-color: white;
            transition: 0.5s;
            text-align: left;
        }

        .a-href {
            display: block; /* block permette che non vi sia intermittenza nell'elenco */
            text-decoration: none;
            font-size: 18px;
            padding-top: 20px;
            padding-left: 25px;
            color: darkgray;
            transition: 0.3s;
        }

        .a-href:hover {
            color: #ff3131;
        }

        .zoom-on-img {
            transition: transform .4s;
        }

        .zoom-on-img:hover {
            transform: scale(1.099);
        }

        .ESQL {
            margin-top: 10px;
            margin-left: 15px;
            margin-bottom: 10px;
        }

        .undo {
            margin-top: 250px;
            margin-left: 50px;
        }

        .background {
            height: 100%;
            background-image: url("img/background.png");
            background-repeat: no-repeat;
            background-size: cover;
        }

        .center {
            position: relative;
            background-color: rgba(255, 255, 255, 0.70);
            top: 200px;
            padding-top: 10px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;
            width: 45%;
            text-align: center;
            border-radius: 20px;
            transition: transform .4s;
        }

        .center:hover {
            transform: scale(1.03);
        }

        .center-center {
            width: 60%;
            display: block;
            position: relative;
            padding-top: 5px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="index.php?"><img class="zoom-on-img ESQL" width="112" height="48" src="img/ESQL.png"></a>
        <a class="a-href" href="#">Messaggi</a>
        <a class="a-href" href="#">Risposte</a>
        <a class="a-href" href="#">Test</a>
        <a href="login.php?"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
    </div>

    <div class="background">
    </div>
</body>
</html>

<?php 

?>