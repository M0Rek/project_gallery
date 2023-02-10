<?php
require("include/function.php");

echo head("Moje konto", "konto");

if (!isset($_SESSION["user-data"])) {
    header("Location: logrej.php");
    exit();
}

$conn = connectToDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['T'] == 'change-data') {
        $currentPwd = $_POST["password"];
        $userLogin = ($_SESSION["user-data"]["login"]);


        if (verifyUser($conn, $userLogin, $currentPwd)->num_rows == 1) {
            if (isset($_POST["pwd-change"])) {
                $newPwd = $_POST["new-pwd"];
                $success = changePassword($conn, $userLogin, $newPwd);
                if (!$success) {
                    $dataError["invalid-password"] = true;
                }
            }

            if (isset($_POST["email-change"])) {
                $newEmail = $_POST["new-email"];
                $success = changeEmail($conn, $userLogin, $newEmail);
                if (!$success) {
                    $dataError["invalid-email"] = true;
                }
            }
        } else {
            $dataError["invalid-credentials"] = true;
        }

        if (!isset($dataError)) {
            $dataChanged = true;
        }

    } else if ($_POST['T'] == 'change-album') {
        $albumId = $_POST["album-id"];
        $albumTitle = $_POST["album-title"];
        $userId = $_SESSION["user-data"]["id"];

        changeAlbum($conn, $userId, $albumId, $albumTitle);

    } else if ($_POST['T'] == 'delete-album') {
        $albumId = $_POST["album-id"];
        $userId = $_SESSION["user-data"]["id"];

        deleteAlbum($conn, $userId, $albumId);

    } else if ($_POST['T'] == 'change-photo') {
        $photoId = $_POST["photo-id"];
        $photoDesc = $_POST["photo-desc"];
        $userId = $_SESSION["user-data"]["id"];

        changePhoto($conn, $userId, $photoId, $photoDesc);

    } else if ($_POST['T'] == 'delete-photo') {
        $photoId = $_POST["photo-id"];
        $albumId = $_POST["album-id"];
        $userId = $_SESSION["user-data"]["id"];

        deletePhoto($conn, $userId, $albumId, $photoId);


    } else if ($_POST['T'] == 'delete-account') {
        $userId = $_SESSION["user-data"]["id"];

        deleteUser($conn, $userId);
        header("Location: wyloguj.php");
        exit();
    }
}

?>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="my-data-tab" data-bs-toggle="pill" data-bs-target="#my-data"
                    type="button" role="tab" aria-controls="my-data" aria-selected="true">Moje dane
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="my-albums-tab" data-bs-toggle="pill" data-bs-target="#my-albums"
                    type="button" role="tab" aria-controls="my-albums" aria-selected="false">Moje albumy
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="my-photos-tab" data-bs-toggle="pill" data-bs-target="#my-photos"
                    type="button" role="tab" aria-controls="my-photos" aria-selected="false">Moje zdjęcia
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="delete-account-tab" data-bs-toggle="pill" data-bs-target="#delete-account"
                    type="button" role="tab" aria-controls="delete-account" aria-selected="false">Usuń moje konto
            </button>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade" id="my-data" role="tabpanel" aria-labelledby="my-data-tab"
             tabindex="0">

            <form method="post" id="redo-form" action="konto.php" class="row g-3">
                <input type="hidden" name="T" value="change-data"/>
                <div class="col-12">
                    <div>Email: <?php echo htmlspecialchars($_SESSION["user-data"]["email"]) ?></div>
                    <div>Hasło: ********</div>

                    <div class="col-md-4 col-12">
                        <div class="alert alert-info" role="alert">
                            Podaj aktualne hasło przed zmianą danych.
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <label for="current-password" class="form-label">Aktualne hasło</label>
                    <input name="password" type="password" class="form-control" id="current-password" required>
                </div>

                <div class="col-md-4 col-12">
                    <h4><input onclick="toggleEmailModification()" id="email-checkbox" name="email-change"
                               type="checkbox"><label class="form-label" for="email-checkbox">Zmień email</label></h4>
                    <label class="form-label" for="new-email-input">Nowy email: </label><input class="form-control"
                                                                                               name="new-email" disabled
                                                                                               id="new-email-input"
                                                                                               value="<?php echo $_SESSION["user-data"]["email"] ?>"
                                                                                               type="text">
                </div>
                <div class="col-md-4 col-12">
                    <h4><input onclick="togglePwdModification()" id="pwd-checkbox" name="pwd-change" type="checkbox">
                        <label class="form-label" for="pwd-checkbox">Zmień hasło</label></h4>
                    <label class="form-label" for="new-pwd-input">Nowe hasło: </label><input class="form-control"
                                                                                             name="new-pwd" disabled
                                                                                             id="new-pwd-input"
                                                                                             type="password">
                </div>
                <div class="col-4">
                    <input class="btn btn-primary" id="change-submit" type="submit" disabled value="Zmień dane">
                </div>
                <div class="alert alert-danger" <?php if (!isset($dataError["invalid-email"])) echo "hidden" ?>
                     id="new-email-validation" role="alert">
                    Pole email niepoprawne!
                </div>
                <div class="alert alert-danger" <?php if (!isset($dataError["invalid-credentials"])) echo "hidden" ?>
                     id="current-password-validation" role="alert">
                    Pole aktualne hasło niepoprawne!
                </div>
                <div class="alert alert-danger" <?php if (!isset($dataError["invalid-password"])) echo "hidden" ?>
                     id="new-pwd-validation" role="alert">
                    Hasło musi mieć od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra.
                </div>
                <div class="alert alert-success" <?php if (!isset($dataChanged)) echo "hidden" ?>
                     id="new-pwd-validation" role="alert">
                    Zmiana danych ukończona pomyślnie. Zmiany będą widoczne po zalogowaniu ponownie.
                </div>

            </form>
        </div>
        <div class="tab-pane fade" id="my-albums" role="tabpanel" aria-labelledby="my-albums-tab" tabindex="0">
            <?php

            $result = getAlbumsByUser($conn, $_SESSION["user-data"]["id"]);


            while ($row = $result->fetch_assoc()) {
                $albums[] = $row;
            }
            ?>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tytuł</th>
                    <th scope="col">Data utworzenia</th>
                    <th scope="col">Ilość zdjęć</th>
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
                            <form class="row" method="post" action="konto.php">
                                <input type="hidden" name="album-id" value="<?php echo $album["id"] ?>"/>
                                <input type="hidden" name="T" value="change-album"/>
                                <div class="col-10">
                                    <label class="form-label" for="album-title-input-<?php echo $album["id"] ?>" hidden>Tytuł</label><input
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
                        <td><?php echo $album["data"] ?></td>
                        <td><?php echo $album["zdj_count"] ?></td>
                        <td>
                            <button class="btn btn-outline-dark"
                                    onclick="redirectToPage('album.php?id=<?php echo $album["id"] ?>')">Przejdź
                            </button>
                        </td>
                        <td>
                            <form method="post" action="konto.php">
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
        </div>
        <div class="tab-pane fade show active" id="my-photos" role="tabpanel" aria-labelledby="my-photos-tab"
             tabindex="0">
            <?php
            if (isset($_GET["album"])) {
                ?>
                <button class="btn btn-outline-primary" onclick="redirectToPage('konto.php')">Powrót</button>
                <?php

                $result = getPhotosByAlbumAndUser($conn, intval($_GET["album"]), $_SESSION["user-data"]["id"]);

                while ($row = $result->fetch_assoc()) {
                    $photos[] = $row;
                }

                ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Zdjęcie</th>
                        <th>Opis</th>
                        <th>Data utworzenia</th>
                        <th>Ocena</th>
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
                                                <img alt="' . $photo["opis"] . '" class="album-thumbnail"  onclick="window.location.href=\'foto.php?id=' . $photo["id"] . '\'"  src="' . minPhotoPath($photo["id_albumu"], $photo["id"]) . '"/>
                                            </div>'; ?>
                            </td>
                            <td>
                                <form class="row" method="post" action="konto.php">
                                    <input type="hidden" name="photo-id" value="<?php echo $photo["id"] ?>"/>
                                    <input type="hidden" name="T" value="change-photo"/>
                                    <div class="col-10">
                                        <label for="photo-title-input-<?php echo $photo["id"] ?>" class="form-label"
                                               hidden>Opis</label> <input type="text" name="photo-desc"
                                                                          onchange="document.getElementById('change-<?php echo $photo["id"] ?>').disabled = false"
                                                                          id="photo-title-input-<?php echo $photo["id"] ?>"
                                                                          class="form-control"
                                                                          value="<?php echo $photo["opis"] ?>">
                                    </div>
                                    <div class="col-2">
                                        <input type="submit" id="change-<?php echo $photo["id"] ?>"
                                               class="btn btn-outline-success" value="Zmień" disabled>
                                    </div>
                                </form>
                            </td>
                            <td><?php echo $photo["data"] ?></td>
                            <td><?php echo $photo["ocena"] ?></td>
                            <td>
                                <form method="post" action="konto.php">
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
            } else {
                ?>

                <div class="row">
                    <div class="text-center col-12">
                        <h3>Wybierz album</h3>
                    </div>
                </div>
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
                        <?php

                        $result = getAlbumsByUser($conn, $_SESSION["user-data"]["id"]);

                        while ($row = $result->fetch_assoc()) {
                            echo
                                '<tr class="cursor-pointer" onclick="redirectToPage(\'konto.php?album=' . $row["id"] . '\')">
            <td>' . $row["tytul"] . '</td>
            <td>' . $row["data"] . '</td>
            <td>' . $row["zdj_count"] . '</td>
            </tr>';
                        } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="delete-account" role="tabpanel" aria-labelledby="delete-account-tab"
             tabindex="0">
            <form method="post" action="konto.php">
                <input type="hidden" name="T" value="delete-account"/>
                <input type="submit" value="Usuń konto" class="btn btn-danger">
                <div class="col-md-4 mt-5 col-12">
                    <div class="alert alert-danger" role="alert">
                        Usunięcie konta spowoduje bezpowrotne usunięcie wszystkich albumów, zdjęć, komentarzy i ocen
                        użytkownika!
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php
echo footer("account.js");
?>