<?php
require("include/function.php");

if (!isset($_SESSION["user-data"])) {
    header("Location: logrej.php");
    exit();
}

$conn = connectToDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['T'] == 'add-photo') {
    savePhoto($conn);
}

echo head("Dodaj zdjęcia", "dodaj-foto");

$result = getAlbumsByUser($conn, $_SESSION["user-data"]["id"]);

if ($result->num_rows == 0) {
    ?>

    <h3 class="text-warning">Musisz założyć album, żeby móc dodać zdjęcia!</h3>
    <h4>Przekierowywanie...</h4>
    <caption>
        Nie działa? Spróbuj ten <a href="dodaj-album.php">link</a>
    </caption>
    <?php

    $delay = 2000;
    echo redirectToPage("dodaj-album.php", $delay);
} else if (!isset($_GET["album"])) {
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        header("Location: dodaj-foto.php?album=" . $row["id"]);
        exit();
    }

    ?>
    <div class="row">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th>Tytuł</th>
                <th>Data utworzenia</th>
                <th>Ilość zdjęć</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) {

                echo
                    '<tr class="cursor-pointer" onclick="redirectToPage(\'dodaj-foto.php?album=' . $row["id"] . '\')">
            <td>' . $row["tytul"] . '</td>
            <td>' . $row["data"] . '</td>
            <td>' . $row["zdj_count"] . '</td>
            </tr>';
            } ?>
            </tbody>
        </table>
    </div>
    <?php
} else {

    $albumId = intval($_GET["album"]);
    $hasAccess = IsUserAlbum($conn, $albumId);;

    if (!$hasAccess) { ?>
        <div class="alert alert-danger" role="alert">
            Nie masz dostępu do tego albumu!
        </div>
        <?php
    } else {
        $result = getPhotosByAlbum($conn, $albumId);

        while ($row = $result->fetch_assoc()) {
            $photos[] = $row;
        }

        ?>
        <form enctype="multipart/form-data" class="row needs-validation" method="post" id="add-photo-form" novalidate>
            <input type="hidden" name="T" value="add-photo"/>
            <input type="hidden" name="album" value="<?php echo $albumId; ?>"/>
            <div class="row">
                <div class="col-12 mt-4">
                    <label for="add-photo-file" class="form-label">Dodaj zdjęcie</label>
                    <input name="photo-file" class="form-control" accept="image/*" type="file" id="add-photo-file"
                           required>
                </div>
                <div class="col-12 mt-4">
                    <label for="add-photo-input" class="form-label">Opis zdjęcia (opcjonalnie)</label>
                    <input name="desc" type="text" maxlength="255" class="form-control" id="add-photo-input">
                    <div id="add-photo-hint" class="form-text">Do 255 znaków. (Pozostało: <span
                                id="chars-left-hint">255</span>)
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary">Dodaj zdjęcie</button>
                </div>
            </div>
        </form>

        <?php
        if (isset($_SESSION["add-photo-error"])) {

            $errors = $_SESSION["add-photo-error"];

            $html = '<div class="pt-5">';
            $errDiv = '<div class="alert alert-danger" role="alert">';

            if (isset($errors["upload-error"])) {
                $html .= $errDiv . 'Nie udało się zapisać zdjęcia</div>';
            }

            if (isset($errors["invalid-format"])) {
                $html .= $errDiv . 'Niepoprawny format. Akceptowalne rozszerzenia: jpg, jpeg, png, gif, webp.</div>';
            }

            if (isset($errors["not-image"])) {
                $html .= $errDiv . 'Załączony plik nie jest zdjęciem! </div>';
            }

            if (isset($errors["not-users-album"])) {
                $html .= $errDiv . 'Brak dostępu do wybranego albumu!</div>';
            }

            if (isset($errors["no-file"])) {
                $html .= $errDiv . 'Nie załączono zdjęcia!</div>';
            }

            if (isset($errors["file-too-large"])) {
                $html .= $errDiv . 'Zdjęcie za duże! Max rozmiar: 1 MB</div>';
            }

            $html .= '</div>';

            echo $html;
        }
        ?>

        <div class="row mt-3 d-flex justify-content-center g-3">
            <?php
            if (isset($photos)) {
                foreach ($photos as $photo) {
                    echo '<div class="d-flex w-auto justify-content-center">
            <img alt="' . $photo["opis"] . '" 
            class="album-thumbnail" 
            onclick="window.location.href=\'photo.php?id=' . $photo["id"] . '\'" 
            data-bs-toggle="tooltip" 
            data-bs-html="true" 
            data-bs-placement="bottom" 
            data-bs-title="' . ($photo["opis"] != "" ? 'Opis: ' . $photo["opis"] . '<br>' : "") . '
            Utworzono: ' . $photo["data"] . '
            <br> Zaakceptowano: ' . ($photo["zaakceptowane"] == "1" ? "Tak" : "Nie") . '" 
            src="photo/' . $photo["id_albumu"] . '/' . $photo["id"] . '-min.jpg"></div>';
                }
            }
            ?>
        </div>
        <?php
    }
}
?>


<?php
echo footer("add-photo-validation.js");

?>