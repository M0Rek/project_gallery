<?php
require("include/function.php");


$conn = connectToDB();

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$albumId = intval($_GET["id"]);

$result = getAlbumById($conn, $albumId);
$exists = false;
$title = "";


if ($row = $result->fetch_assoc()) {
    $exists = true;
    $title = $row['tytul'];
}

echo head("Album " . $title);

if (!$exists) {
    ?>
    <h3 class="text-warning">Wybrany album nie istnieje.</h3>
    <?php
    echo backToAlbumsButton();
} else {

    echo backToAlbumsButton();
    ?>
    <div class="card">
        <div class="card-header display-6">
            Album <?php echo $row["tytul"] ?>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Stworzono przez: <span class="fw-bold"><?php echo $row["tworca"] ?></span></li>
            <li class="list-group-item">Data utworzenia: <span class="fw-bold"><?php echo $row["data"] ?></span></li>
            <li class="list-group-item">Ilość zdjęć: <span class="fw-bold"><?php echo $row["zdj_count"] ?></span></li>
        </ul>
    </div>
    <?php

    $itemsPerPage = 20;
    $itemsCount = intval($row["zdj_count"]);
    $pageCount = ceil($itemsCount / $itemsPerPage);

    $currentPage = getCurrentPage($pageCount);

    $result = getPhotosByAlbumPaginated($conn, $albumId, 1, $currentPage, $itemsPerPage);


    while ($row = $result->fetch_assoc()) {
        $photos[] = $row;
    }

    ?>

    <div class="row my-3 d-flex justify-content-center g-3">

        <?php
        if (isset($photos)) {
            echo photos($photos);
        }
        ?>
    </div>
    <?php
    echo pagination($currentPage, $pageCount);
    echo backToAlbumsButton();
}


echo footer();

?>

