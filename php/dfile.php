<?php
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Length: ". filesize("output.csv").";");
    header("Content-Disposition: attachment; filename=output.csv");
    header("Content-Type: application/octet-stream; "); 
    header("Content-Transfer-Encoding: binary");

    readfile("output.csv");
?>
