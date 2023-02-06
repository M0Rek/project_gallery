<?php
require("include/function.php");

echo head("Najnowsze");


$items = 20;

$conn = connectToDB();
$result = getNewestPhotos($conn, $items);

while ($row = $result->fetch_assoc()) {
    $photos[] = $row;
}

?>
    <h3>Najnowsze</h3>
    <div class="row my-3 d-flex justify-content-center g-3">

        <?php
        if (isset($photos)) {
            echo photos($photos);
        }
        ?>
    </div>


<?php
echo footer();
?>