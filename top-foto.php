<?php
require("include/function.php");

echo head("Najlepiej oceniane");


$items = 20;

$conn = connectToDB();
$result = getTopPhotos($conn, $items);

while ($row = $result->fetch_assoc()) {
    $photos[] = $row;

}

?>
    <h3>Najlepiej oceniane</h3>
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