<?php
include "./library/DBClass.php";
include "./library/getid3/getid3.php";
global $rootFolder;
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>LAN Player</title>
        <link rel ="stylesheet" type = "text/css" href = "library/css/bootstrap.min.css">
        <link rel ="stylesheet" type = "text/css" href = "library/css/style.css">
    <script type = "text/javascript" src = "library/js/jQuery.js"></script>
    <script type = "text/javascript" src = "library/js/bootstrap.min.js"></script>
    <script type = "text/javascript" src = "library/js/script.js"></script>
    </head>
    <body class="hero-unit" style="text-align: center;">
        <div class="container">
            <span class="row" id="titlu-melodie">
            </span>
            <div class='container'>
                <span class=' span4 prev-button pull-left' onclick="prev();">
                </span>
                <div class="span6  container-album-art" style="
                     background-image: url('./library/img/default.jpg');
                     ">
                    <!--<img id="FileImage" width="250" src="./library/img/default.jpg" height="250">-->
                </div>
                <span class=' span4 next-button pull-right' onclick="next();">
                </span>
            </div>
            <div id="container-player">
                <audio  id="main-player" class="row" controls="controls">
                    <source src="<?php echo $rootFolder.
                            $rows[0]["path"]?>" 
                            type="audio/mpeg"
                            >
                  Your browser does not support the audio element.
                  </audio>
            </div>
        </div>
    </body>
</html>
