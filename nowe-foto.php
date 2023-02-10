<?php
require("include/function.php");

echo head("Najnowsze", "nowe-foto");


$items = 20;

$conn = connectToDB();
$result = getNewestPhotos($conn, $items);

$photos = (array)null;

while ($row = $result->fetch_assoc()) {
    $photos[] = $row;
}

?>
    <h3>Najnowsze</h3>


<?php
        if (isset($photos)) {
            echo photos($photos);
        }
        ?>


<?php
echo footer();
?>