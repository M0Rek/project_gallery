<?php
include("include/function.php");

if (!isset($_SESSION["user-data"])) {
    header("Location: logrej.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['T'] == 'add-album') {

    // Nie ma potrzeby na sprawdzenie tytułu, czy ma niebezpieczne znaki;
    // Przygotowane kwerendy zawsze traktują parametry jako dane i ignorują znaki specjalne.
    $title = $_POST["title"];
    $conn = connectToDB();

    insertAlbum($conn, $title);
}

echo head("Dodaj album", "dodaj-album");
?>
<form method="post" id="add-album-form" class="needs-validation" novalidate>
    <input type="hidden" name="T" value="add-album"/>
    <label for="add-album-input" class="form-label">Tytuł albumu</label>
    <div class="row">
        <div class="col-12 col-md-10">
            <input name="title" required type="text" maxlength="100" class="form-control" id="add-album-input">
            <div class="invalid-feedback" id="add-album-input-validation">
                Tytuł albumu nie może być pusty!
            </div>
            <div id="add-album-hint" class="form-text">Do 100 znaków. (Pozostało: <span id="chars-left-hint">100</span>)
            </div>
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-primary">Stwórz album</button>
        </div>
    </div>
</form>
<?php
if (isset($_SESSION["add-album-error"])) {

    $errors = $_SESSION["add-album-error"];

    $html = '<div class="pt-5">';
    $errDiv = '<div class="alert alert-danger" role="alert">';

    if (isset($errors["title-exists"])) {
        $html .= $errDiv . '
           Taki tytuł już istnieje.</div>';
    }

    if (isset($errors["invalid-title"])) {
        $html .= $errDiv . '
           Tytuł albumu nie może być pusty!</div>';
    }

    if (isset($errors["database-error"])) {
        $html .= $errDiv . '
            Błąd połączenia z bazą danych.</div>';
    }

    $html .= '</div>';

    echo $html;
}
?>


<?php

echo footer("add-album-validation.js");


?>



