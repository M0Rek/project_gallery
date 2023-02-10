<?php
require("include/function.php");

$conn = connectToDB();

if (isset($_SESSION["user-data"])) {
    $loggedIn = true;
} else $loggedIn = false;

if ($loggedIn && $_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['T'] == 'add-rating') {
        $photoId = intval($_POST["id"]);
        $userId = intval($_SESSION["user-data"]["id"]);
        $rating = intval($_POST["rating"]);

        $conn = connectToDB();

        ratePhoto($conn, $photoId, $userId, $rating);
    } else if ($_POST['T'] == 'add-comment') {
        $photoId = intval($_POST["id"]);
        $userId = intval($_SESSION["user-data"]["id"]);
        $comment = htmlspecialchars($_POST["comment"]);

        $conn = connectToDB();

        commentPhoto($conn, $photoId, $userId, $comment);
    }
}

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$photoId = intval($_GET["id"]);

$result = getAcceptedPhotoById($conn, $photoId);
$exists = false;
$id = "";


if ($row = $result->fetch_assoc()) {
    $exists = true;
    $id = $row['id'];
    $albumId = $row["id_albumu"];
}

echo head("Zdjęcie " . $id);

if (!$exists) {
    ?>
    <h3 class="text-warning">Wybrane zdjęcie nie istnieje lub nie jest zaakceptowane.</h3>
    <?php
    echo backToAlbumsButton();
} else {
    echo backToAlbumButton($albumId);
    ?>
    <div class="card">
        <div class="card-header">
            <h5>Album <?php echo $row["tytul"] ?></h5>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Dodano przez: <span class="fw-bold"><?php echo $row["tworca"] ?></span></li>
            <li class="list-group-item">Data dodania zdjęcia: <span class="fw-bold"><?php echo $row["data"] ?></span>
            </li>
            <?php if ($row["opis"] != "") {
                ?>
                <li class="list-group-item">Opis: <span class="fw-bold"><?php echo $row["opis"] ?></span></li>
                <?php
            } ?>

        </ul>
    </div>
    <div class="row">
        <div class="col-12 text-center justify-content-center d-flex my-4">
            <div class="position-relative">
                <div onclick="redirectToPage('foto.php?id=<?php echo previousPhoto($conn, $id, $row["id_albumu"]); ?>')"
                     class="position-absolute cursor-pointer top-50 img-btn previous-img-btn">
                    <img alt="left-arrow" src="images/chevron-double-left.svg">
                </div>
                <div onclick="redirectToPage('foto.php?id=<?php echo nextPhoto($conn, $id, $row["id_albumu"]); ?>')"
                     class="position-absolute cursor-pointer top-50 img-btn next-img-btn">
                    <img alt="right-arrow" src="images/chevron-double-right.svg">
                </div>
                <img class="img-fluid rounded mw-100 h-auto bord big-image"
                     alt="<?php echo $row["opis"]; ?>"
                     src="<?php echo photoPath($row["id_albumu"], $id) ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-1 d-flex align-items-center justify-content-end col-sm-2 text-center mb-2 mb-sm-0 col-12">
            Ocena
        </div>
        <?php

        $result = getRating($conn, $id);

        if ($row = $result->fetch_assoc() and $row["count"] > 0) {
            $rating = round($row["ocena"], 2);
            ?>
            <div class="col-lg-4 col-sm-10 col-12">
                <div style="height: 2.5rem; font-size: 1.1rem" class="progress bg-secondary">
                    <div style="width:<?php echo($rating * 10) ?>%;"
                         class="progress-bar overflow-visible fw-bold bg-warning"><?php echo $rating ?> na 10
                        (<?php echo $row["count"] ?> ocen)
                    </div>
                </div>
            </div>
            <?php
        } else {

            ?>
            <div class="col-lg-4 d-flex align-items-center justify-content-center text-center col-sm-10 col-12">Brak
            </div> <?php
        }
        if (isset($_SESSION["user-data"])) {
            $rating = getPhotoRatingByUser($conn, $id, $_SESSION["user-data"]["id"]);
        } else $rating = "Nie zalogowano";
        ?>
        <div class="col-sm-2 text-center mb-2 mb-sm-0 d-flex justify-content-end align-middle col-12">
            <label for="add-rating-input" class="m-0 d-flex align-items-center form-label">Twoja ocena </span></label>
        </div>
        <div class="col-lg-4 mt-lg-0 mt-3 col-10">
            <form method="post" id="add-rating-form" class="needs-validation" novalidate>
                <input type="hidden" name="T" value="add-rating"/>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <fieldset <?php echo $rating ? "disabled" : "" ?>>
                    <div class="row">
                        <div class="col-10">
                            <input name="rating" required type="number" value="<?php echo $rating ?: "" ?>" max="10"
                                   min="1" class=" form-control"
                                   id="add-rating-input">
                            <div class="invalid-feedback" id="add-rating-input-validation">
                                Ocena musi leżeć w zakresie 1-10
                            </div>
                        </div>
                        <div class="col-2 text-end">
                            <button type="submit" class=" w-auto btn btn-primary">Oceń</button>
                        </div>
                    </div>
                </fieldset>
            </form>

        </div>
    </div> <?php
    if (isset($_SESSION["add-rating-error"])) {

        $errors = $_SESSION["add-rating-error"];

        $html = '<div class="pt-5">';
        $errDiv = '<div class="alert alert-danger" role="alert">';

        if (isset($errors["already-rated"])) {
            $html .= $errDiv . '
           Oceniłeś już to zdjęcie.</div>';
        }

        if (isset($errors["database-error"])) {
            $html .= $errDiv . '
           Błąd bazy danych. Nie oceniono. </div>';
        }

        if (isset($errors["invalid-rating"])) {
            $html .= $errDiv . '
           Ocena musi być w skali od 1 do 10. </div>';
        }

        $html .= '</div>';

        echo $html;

        unset($_SESSION["add-rating-error"]);
    }
    ?>
    <hr class="my-5 text-primary"/>
    <div id="comment-section">
        <h5>Komentarze</h5>
        <?php
        $result = getPhotoComments($conn, $id);

        $comments = (array)null;

        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        if (isset($comments)) {
            foreach ($comments as $comment) {
                ?>
                <div class="card mt-2">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $comment["tworca"] ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo $comment["data"] ?></h6>
                        <p class="card-text"><?php echo $comment["komentarz"] ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="alert alert-info" role="alert">
                Brak komentarzy.
            </div>
            <?php
        }
        ?>
        <div class="card mt-2">
            <div class="card-body">
                <form method="post" id="add-comment-form" class="needs-validation" novalidate>
                    <input type="hidden" name="T" value="add-comment"/>
                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <label class="form-label" for="add-comment-input">Napisz komentarz:</label>
                    <fieldset <?php echo $loggedIn ? "" : "disabled" ?>>
                        <div class="row">
                            <div class="col-12">
                                <textarea name="comment" required rows="2"
                                          class="form-control"
                                          id="add-comment-input"><?php echo $loggedIn ? "" : "Zaloguj się aby komentować." ?></textarea>
                                <div class="invalid-feedback" id="add-comment-input-validation">
                                    Komentarz nie może być pusty.
                                </div>
                            </div>
                            <div class="col-12 mt-2 text-end">
                                <button type="submit" class="btn btn-primary">Wyślij</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <?php
            if (isset($_SESSION["add-comment-error"])) {
                $errors = $_SESSION["add-comment-error"];

                $html = '<div class="pt-5">';
                $errDiv = '<div class="alert alert-danger" role="alert">';

                if (isset($errors["invalid-comment"])) {
                    $html .= $errDiv . 'Komentarz nie może być pusty.</div>';
                }

                if (isset($errors["database-error"])) {
                    $html .= $errDiv . 'Błąd bazy danych. Nie skomentowano. </div>';
                }

                $html .= '</div>';

                echo $html;

                unset($_SESSION["add-rating-error"]);
            }
            ?>
        </div>
    </div>
    <?php
    echo backToAlbumButton($albumId);


}

echo footer("photo-validation.js");
?>