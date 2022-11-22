<?php

require("include/function.php");

$conn = connectToDB();
$result = getNotEmptyAlbumsCount($conn);

$itemsPerPage = 20;
$itemsCount = $result->fetch_assoc()["count"];
$pageCount = ceil($itemsCount / $itemsPerPage);

$currentPage = getCurrentPage($pageCount);

//Wartości nie są bezpośrednio wprowadzone do kwerendy, co zapobiega sql injection
if (isset($_GET["sort"])) {
    switch ($_GET["sort"]) {
        case "data":
            $sort = "data";
            break;
        case "tworca":
            $sort = "tworca";
            break;
        default:
            $sort = "tytul";
            break;
    }
} else {
    $sort = "tytul";
}

if (isset($_GET["asc"]) && $_GET["asc"] == "false")
    $asc = "DESC";
else $asc = "ASC";

$result = getNotEmptyAlbumsPaginated($conn, $sort, $asc, $currentPage, $itemsPerPage);

while ($row = $result->fetch_assoc()) {
    $albums[] = $row;
}

echo head("Strona główna", "index");

?>


<div class="row m-3">
    <div class="col-md-4">
        <label for="album-sort">
            Sortowanie:
        </label>
        <select onchange="sortAlbums()" id="album-sort" class="form-select"
                aria-label="Sortowanie">
            <option value="tytul">Nazwa</option>
            <option value="data">Data</option>
            <option value="tworca">Twórca</option>
        </select>
        <div class="form-check form-switch pt-2">
            <input class="form-check-input" <?php echo (isset($_GET["asc"]) && $_GET["asc"] == "false") ? "" : "checked" ?>
                   onchange="sortAlbums()" type="checkbox" id="album-asc">
            <label class="form-check-label" for="album-asc">Sortuj rosnąco</label>
        </div>
    </div>
    <script>

        <?php
        if (isset($_GET["sort"])) {
            echo 'document.getElementById("album-sort").value = "' . $_GET["sort"] . '";';
        }


        ?>

        function sortAlbums() {
            const sort = document.getElementById("album-sort").value;
            const desc = document.getElementById("album-asc").checked;

            document.location.href = "index.php?sort=" + sort + "&asc=" + desc;
        }
    </script>
</div>


<div class="row d-flex justify-content-center g-3">

    <?php
    if (isset($albums)) {
        foreach ($albums as $album) {
            echo
                '<div class="d-flex w-auto justify-content-center">
            <img 
            alt="' . $album["tytul"] . '" 
            class="album-thumbnail" 
            onclick="window.location.href=\'album.php?id=' . $album["id"] . '\'" 
            data-bs-toggle="tooltip" 
            data-bs-html="true" 
            data-bs-placement="bottom" 
            data-bs-title="Tytuł: ' . $album["tytul"] . '
            <br> Utworzono: ' . $album["krotka_data"] . '
            <br> Twórca: ' . $album["tworca"] . '" 
            src="' . photoPath($album["id"], $album["zdjecie"]) . '"/>
            
            </div>';
        }
    }
    ?>
</div>
<div class="row m-3">
    <?php echo pagination($currentPage, $pageCount) ?>
</div>


<?php

echo footer();
?>
