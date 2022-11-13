<?php
require("include/function.php");
session_start();

echo head("Pomyślna rejestracja");


print_r($_SESSION["user-data"]);

?>


<?php

echo footer();


?>