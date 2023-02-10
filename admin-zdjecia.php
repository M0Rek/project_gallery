<?php
require("include/function.php");

if (!isset($_SESSION["user-data"]) || ($_SESSION["user-data"]["role"] != "administrator" && $_SESSION["user-data"]["role"] != "moderator")) {
    header("Location: logrej.php");
    exit();
}

$conn = connectToDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['T'] == 'change-photo') {
        $photoId = $_POST["photo-id"];
        $photoDesc = $_POST["photo-desc"];
        changePhotoAsAdmin($conn, $photoId, $photoDesc);

    } else if ($_POST['T'] == 'accept-photo') {
        $photoId = $_POST["photo-id"];

        acceptPhotoAsAdmin($conn, $photoId);
    } else if ($_POST['T'] == 'delete-photo') {
        $photoId = $_POST["photo-id"];
        $albumId = $_POST["album-id"];

        deletePhotoAsAdmin($conn, $albumId, $photoId);
    }
}

$show = "unaccepted";
$albumId = -1;

if (isset($_GET["showBy"]) && isset($_GET["albumId"]) && $_GET["showBy"] == "album") {
    $show = "album";
    $albumId = intval($_GET["albumId"]);
}

echo adminHead("Admin - Zdjęcia", "admin-zdjecia");

if ($show == "album") {
    $result = getPhotosByAlbum($conn, $albumId);
} else {
    $result = getUnacceptedPhotos($conn);
}

$photos = (array)null;

while ($row = $result->fetch_assoc()) {
    $photos[] = $row;
}

?>

    <form class="row" method="get" action="admin-zdjecia.php">
        <div class="col-4 col-md-3">
            <input <?php echo(($show == "unaccepted") ? "checked" : "") ?> value="unaccepted"
                                                                           class="form-check-input me-1"
                                                                           id="accepted-radio" type="radio"
                                                                           name="showBy">
            <label class="form-check-label" for="accepted-radio">Pokaż niezaakceptowane</label>
        </div>
        <div class="col-4 d-flex col-md-3">
            <input <?php echo(($show == "album") ? "checked" : "") ?> value="album" id="album-radio"
                                                                      class="form-check-input me-1" type="radio"
                                                                      name="showBy">
            <label class="form-check-label" for="album-radio">Pokaż z albumu</label>
            <label class="form-check-label" for="album-id-input" hidden>Id albumu</label>
            <input value="<?php echo(($show == "album") ? $albumId : "") ?>" id="album-id-input" style="width: 150px"
                   class="ms-3 form-control" type="number" placeholder="ID albumu" min="0" name="albumId">
        </div>
        <div class="col-4 col-md-2">
            <input type="submit" class="btn btn-outline-dark" value="Pokaż wybrane">
        </div>
    </form>


    <table class="table">
        <thead>
        <tr>
            <th>Zdjęcie</th>
            <th>Opis</th>
            <th>Data utworzenia</th>
            <th>Zaakceptuj</th>
            <th>Usuń</th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($photos as $photo) {
            ?>
            <tr>
                <td>
                    <?php echo '<div class="d-flex w-auto justify-content-center">
                                                <img alt="' . $photo["opis"] . '"  onclick="window.location.href=\'foto.php?id=' . $photo["id"] . '\'"  src="' . minPhotoPath($photo["id_albumu"], $photo["id"]) . '"/>
                                            </div>'; ?>
                </td>
                <td><?php echo $photo["opis"] ?></td>
                <td><?php echo $photo["data"] ?></td>
                <td><?php if ($photo["zaakceptowane"] == 0) { ?>
                        <form method="post" action="admin-zdjecia.php">
                            <input type="hidden" name="T" value="accept-photo"/>
                            <input type="hidden" name="photo-id" value="<?php echo $photo["id"] ?>"/>
                            <input type="submit" class="btn btn-outline-success" value="Zaakceptuj">
                        </form>
                        <?php
                    } else {
                        echo "Zaakceptowano";
                    }
                    ?></td>
                <td>
                    <form method="post" action="admin-zdjecia.php">
                        <input type="hidden" name="T" value="delete-photo"/>
                        <input type="hidden" name="photo-id" value="<?php echo $photo["id"] ?>"/>
                        <input type="hidden" name="album-id" value="<?php echo $photo["id_albumu"] ?>"/>
                        <input type="submit" class="btn btn-outline-danger" value="Usuń">
                    </form>
                </td>
            </tr>

            <?php
        } ?>

        </tbody>
    </table>
<?php
echo footer();
?>