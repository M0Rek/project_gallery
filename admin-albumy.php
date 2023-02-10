<?php
require("include/function.php");

if (!isset($_SESSION["user-data"]) || $_SESSION["user-data"]["role"] != "administrator") {
    header("Location: logrej.php");
    exit();
}
$conn = connectToDB();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['T'] == 'change-album') {
        $albumId = $_POST["album-id"];
        $albumTitle = $_POST["album-title"];

        changeAlbumAsAdmin($conn, $albumId, $albumTitle);

    } else if ($_POST['T'] == 'delete-album') {
        $albumId = $_POST["album-id"];

        deleteAlbumAsAdmin($conn, $albumId);
    }
}

echo adminHead("Admin - Albumy", "admin-albumy");


$result = getAlbumsForAdminCount($conn);

$itemsPerPage = 3;
$itemsCount = $result->fetch_assoc()["count"];

$pageCount = ceil($itemsCount / $itemsPerPage);

$currentPage = getCurrentPage($pageCount);

$result = getAlbumsForAdminPaginated($conn, $currentPage, $itemsPerPage);

while ($row = $result->fetch_assoc()) {
    $albums[] = $row;
}
?>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tytuł</th>
            <th scope="col">Twórca</th>
            <th scope="col">Utworzono</th>
            <th scope="col">Do akceptacji</th>
            <th scope="col">Przejdź</th>
            <th scope="col">Usuń</th>
        </tr>
        </thead>
        <tbody>

        <?php

        foreach ($albums as $album) {
            ?>

            <tr>
                <th scope="row"><?php echo $album["id"] ?></th>
                <td>
                    <form class="row" method="post" action="admin-albumy.php">
                        <input type="hidden" name="album-id" value="<?php echo $album["id"] ?>"/>
                        <input type="hidden" name="T" value="change-album"/>
                        <div class="col-9">
                            <label class="form-label" for="album-title-input-<?php echo $album["id"] ?>"
                                   hidden>Tytuł</label><input
                                    type="text" name="album-title"
                                    onchange="document.getElementById('change-<?php echo $album["id"] ?>').disabled = false"
                                    id="album-title-input-<?php echo $album["id"] ?>" class="form-control"
                                    value="<?php echo $album["tytul"] ?>">
                        </div>
                        <div class="col-2">
                            <input type="submit" id="change-<?php echo $album["id"] ?>"
                                   class="btn btn-outline-success" value="Zmień" disabled>
                        </div>
                    </form>
                </td>
                <td><?php echo $album["tworca"] ?></td>
                <td><?php echo $album["krotka_data"] ?></td>
                <td><?php echo $album["niezaakceptowanych"] ?></td>
                <td>
                    <button class="btn btn-outline-dark"
                            onclick="redirectToPage('album.php?id=<?php echo $album["id"] ?>')">Przejdź
                    </button>
                </td>
                <td>
                    <form method="post" action="admin-albumy.php">
                        <input type="hidden" name="T" value="delete-album"/>
                        <input type="hidden" name="album-id" value="<?php echo $album["id"] ?>"/>
                        <input type="submit" class="btn btn-outline-danger" value="Usuń">
                    </form>
                </td>
            </tr>

            <?php
        }

        ?>
        </tbody>
    </table>

    <div class="row m-3">
        <?php echo pagination($currentPage, $pageCount) ?>
    </div>

<?php
echo footer();


?>