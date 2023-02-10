<?php
require("include/function.php");

echo head("Najlepiej oceniane", "top-foto");


$items = 20;

$conn = connectToDB();
$result = getTopPhotos($conn, $items);

while ($row = $result->fetch_assoc()) {
    $photos[] = $row;

}

?>
    <h3>Najlepiej oceniane</h3>


<?php
        if (isset($photos)) {
            echo photos($photos);
        }
        ?>


<?php
echo footer();
?>