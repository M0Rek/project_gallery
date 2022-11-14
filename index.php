<?php

require("include/function.php");
require("include/database.php");

//Wartości nie są bezpośrednio wprowadzone do kwerendy, co zapobiega sql injection
if(isset($_GET["sort"])) {
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
}
else {
    $sort = "tytul";
}

if (isset($_GET["asc"]) && $_GET["asc"] == "false")
    $asc = "DESC";
else $asc = "ASC";

$conn = connectToDB();
$stmt = $conn->prepare("SELECT count(*) as count FROM albumy INNER JOIN 
    (SELECT id,opis,id_albumu,min(data),zaakceptowane FROM zdjecia WHERE zaakceptowane = 1 GROUP BY id_albumu) as zaakceptowane on zaakceptowane.id_albumu = albumy.id
        LEFT JOIN uzytkownicy ON id_uzytkownika = uzytkownicy.id");
$stmt->execute();
$result = $stmt->get_result();

$itemsPerPage = 20;
$itemsCount = $result->fetch_assoc()["count"];
$pageCount = ceil($itemsCount / $itemsPerPage);

if(isset($_GET["page"])) {
    $getPage = intval($_GET["page"]);

    if($getPage > $pageCount)
        $currentPage = $pageCount;

    else if($getPage < 1)
        $currentPage = 1;

    else $currentPage = $getPage;
}
else $currentPage = 1;


$stmt = $conn->prepare("SELECT albumy.id as id, zaakceptowane.id as zdjecie,tytul, date(data) as data ,uzytkownicy.login as tworca FROM albumy INNER JOIN 
    (SELECT id,opis,id_albumu,min(data),zaakceptowane FROM zdjecia WHERE zaakceptowane = 1 GROUP BY id_albumu) as zaakceptowane on zaakceptowane.id_albumu = albumy.id
        LEFT JOIN uzytkownicy ON id_uzytkownika = uzytkownicy.id ORDER BY " . $sort . " " . $asc . " LIMIT ".(($currentPage - 1) * $itemsPerPage).", ".$itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $albums[] = $row;
}

echo head("Strona główna");

?>


<div class="container-md mt-5">

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
                <input class="form-check-input" checked onchange="sortAlbums()" type="checkbox" id="album-asc">
                <label class="form-check-label" for="album-asc">Sortuj rosnąco</label>
            </div>
        </div>
        <script>

            <?php
            if (isset($_GET["sort"])) {
                echo 'document.getElementById("album-sort").value = "' . $_GET["sort"] . '";';
            }

            if (isset($_GET["asc"])) {
                echo 'document.getElementById("album-asc").checked = ("' . $_GET["asc"] . '" == "false") ? false : true ;';
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
        foreach ($albums as $album) {
            echo '<div class="d-flex w-auto justify-content-center">
            <img alt="'.$album["tytul"].'" class="album-thumbnail" onclick="window.location.href=\'album.php?id=' . $album["id"] . '\'" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" 
    data-bs-title="Tytuł: ' . $album["tytul"] . '<br> Utworzono: ' . $album["data"] . '<br> Twórca: ' . $album["tworca"] . '" 
    src="photo/' . $album["id"] . '/' . $album["zdjecie"] . '.jpg"></div>';
        }
        ?>
    </div>
    <div class="row m-3">
        <?php echo pagination($currentPage,$pageCount) ?>
    </div>
</div>


<?php

echo footer();
?>
