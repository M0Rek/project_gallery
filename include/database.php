<?php

function connectToDB()
{
    $conn = mysqli_connect(DB_HOST, DB_LOGIN, DB_PWD, DB);

    if (mysqli_connect_errno()) {
        echo "Błąd połączenia nr: " . mysqli_connect_errno() . "<br>";
        echo "Opis błędu: " . mysqli_connect_error();
        return false;
    }

    mysqli_query($conn, 'SET NAMES utf8');
    mysqli_query($conn, 'SET CHARACTER SET utf8');
    mysqli_query($conn, "SET collation_connection = utf8_polish_ci");
    return $conn;
}

/**
 * @param mysqli_result $result
 * @return array
 */
function getUserData(mysqli_result $result)
{
    $row = $result->fetch_assoc();
    $user_data["id"] = $row["id"];
    $user_data["login"] = $row["login"];
    $user_data["email"] = $row["email"];
    $user_data["registered-date"] = $row["zarejestrowany"];
    $user_data["role"] = $row["uprawnienia"];
    $user_data["is-active"] = $row["aktywny"];
    $user_data["password-hash"] = $row["haslo"];

    return $user_data;
}

function insertAlbum($conn, $title)
{
    unset($_SESSION["add-album-error"]);

    $title = trim($title);

    if ($title == "") {
        $_SESSION["add-album-error"]["invalid-title"] = true;
        return false;
    }

    if (!$conn) {
        $_SESSION["add-album-error"]["database-error"] = true;
        return false;
    }

    if (!isset($_SESSION["user-data"])) {
        $_SESSION["add-album-error"]["not-logged-in"] = true;
        return false;
    }

    $title = htmlspecialchars($title);

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT * FROM albumy where tytul LIKE ?");
        $stmt->bind_param('s', $title);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION["add-album-error"]["title-exists"] = true;
            $conn->rollback();
            return false;
        }

        $stmt = $conn->prepare(" INSERT INTO albumy (id,tytul,data,id_uzytkownika) VALUES (default, ?, NOW(), ?)");
        $stmt->bind_param('si', $title, $_SESSION["user-data"]["id"]);

        if (!$stmt->execute()) {
            $_SESSION["add-album-error"]["database-error"] = true;
            $conn->rollback();
            return false;
        }

        $id = $conn->insert_id;
        mkdir("photo/" . $id);
        $conn->commit();
        header("Location: dodaj-foto.php");
        exit();


    } catch (mysqli_sql_exception $e) {
        $_SESSION["add-album-error"]["database-error"] = true;
        $conn->rollback();
    }

    return true;
}


function getNotEmptyAlbumsCount($conn)
{
    $stmt = $conn->prepare("SELECT count(*) as count FROM albumy INNER JOIN 
    (SELECT id,opis,id_albumu,min(data),zaakceptowane FROM zdjecia WHERE zaakceptowane = 1 GROUP BY id_albumu) as zaakceptowane on zaakceptowane.id_albumu = albumy.id
        LEFT JOIN uzytkownicy ON id_uzytkownika = uzytkownicy.id");
    $stmt->execute();

    return $stmt->get_result();
}

function getNotEmptyAlbumsPaginated($conn, $sortBy, $asc, $currentPage, $itemsPerPage)
{
    $stmt = $conn->prepare("SELECT albumy.id as id, zaakceptowane.id as zdjecie,tytul, data, date(data) as krotka_data,uzytkownicy.login as tworca FROM albumy INNER JOIN 
    (SELECT id,opis,id_albumu,min(data),zaakceptowane FROM zdjecia WHERE zaakceptowane = 1 GROUP BY id_albumu) as zaakceptowane on zaakceptowane.id_albumu = albumy.id
        LEFT JOIN uzytkownicy ON id_uzytkownika = uzytkownicy.id ORDER BY " . $sortBy . " " . $asc . " LIMIT " . (($currentPage - 1) * $itemsPerPage) . ", " . $itemsPerPage);
    $stmt->execute();
    return $stmt->get_result();
}

function getAlbumsByUser($conn, $userId)
{
    $stmt = $conn->prepare("SELECT albumy.id as id,tytul, albumy.data as data, count(zdjecia.id) as zdj_count FROM albumy 
        INNER JOIN uzytkownicy ON id_uzytkownika = uzytkownicy.id
        LEFT JOIN zdjecia ON albumy.id = id_albumu 
        WHERE id_uzytkownika = ? GROUP BY albumy.id, albumy.data ORDER BY data DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result();
}

function getPhotosByAlbum($conn, $albumId)
{
    $stmt = $conn->prepare("SELECT id,opis,id_albumu,data,zaakceptowane FROM zdjecia WHERE id_albumu = ?");
    $stmt->bind_param("i", $albumId);
    $stmt->execute();
    return $stmt->get_result();
}

function getPhotosByAlbumPaginated($conn, $albumId, $accepted, $currentPage, $itemsPerPage)
{
    $stmt = $conn->prepare("SELECT id,opis,id_albumu,data,zaakceptowane FROM zdjecia WHERE id_albumu = ? AND zaakceptowane = ? LIMIT " . (($currentPage - 1) * $itemsPerPage) . ", " . $itemsPerPage);
    $stmt->bind_param("ii", $albumId, $accepted);
    $stmt->execute();
    return $stmt->get_result();
}

function insertPhoto($conn, $desc, $albumId)
{

    if (!isset($_SESSION["user-data"])) {
        $_SESSION["add-photo-error"]["not-logged-in"] = true;
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO `zdjecia` (`id`, `opis`, `id_albumu`, `data`, `zaakceptowane`) VALUES (DEFAULT, ?, ?, now(), 0);");
    $stmt->bind_param("si", $desc, $albumId);
    $stmt->execute();
    return $conn->insert_id;
}

function IsUserAlbum($conn, $albumId)
{
    $result = getAlbumsByUser($conn, $_SESSION["user-data"]["id"]);
    while ($row = $result->fetch_assoc()) {
        if ($albumId == $row["id"]) {
            return true;
        }
    }
    return false;
}

function savePhoto($conn)
{
    unset($_SESSION["add-photo-error"]);

    if (!file_exists($_FILES['photo-file']['tmp_name'])) {
        $_SESSION["add-photo-error"]["no-file"] = true;
        return false;
    }

    $albumId = $_POST["album"];
    $hasAccess = IsUserAlbum($conn, $albumId);

    if (!$hasAccess) {
        $_SESSION["add-photo-error"]["not-users-album"] = true;
        return false;
    }

    if ($_FILES["photo-file"]["size"] > 1000000) {
        $_SESSION["add-photo-error"]["file-too-large"] = true;
        return false;
    }

    $desc = htmlspecialchars($_POST["desc"]);

    $targetDir = "photo/" . $albumId . '/';
    $fileName = basename($_FILES["photo-file"]["name"]);
    $tmp = $_FILES["photo-file"]["tmp_name"];

    $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    //Sprawdzenie, czy plik to zdjęcie
    $check = getimagesize($tmp);
    if ($check === false) {
        $_SESSION["add-photo-error"]["not-image"] = true;
        return false;
    }

    if ($imageFileType != "jpg" && $imageFileType != "webp" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $_SESSION["add-photo-error"]["invalid-format"] = true;
        return false;
    }

    if (!$photoId = strval(insertPhoto($conn, $desc, $albumId))) {
        $_SESSION["add-photo-error"]["database-error"] = true;
        return false;
    }

    $photoId = strval(insertPhoto($conn, $desc, $albumId));
    $path = $targetDir . basename($photoId . '.jpg');
    $minPath = $targetDir . basename($photoId . '-min.jpg');

    if (!move_uploaded_file($_FILES["photo-file"]["tmp_name"], $path)) {
        $_SESSION["add-photo-error"]["upload-error"] = true;
        return false;
    }
    $binary = imagecreatefromstring(file_get_contents($path));
    imageJpeg($binary, $path, 100);

    $size = getimagesize($path);
    $width = $size[0];
    $height = $size[1];

    if ($width > $height) {
        $resizeResult = imageJpeg(resizeImage($path, ($width > 1200) ? 1200 : $width, 0, true), $path, 100);
        $minResizeResult = imageJpeg(resizeImage($path, 0, 180, false), $minPath, 100);
    } else {
        $resizeResult = imageJpeg(resizeImage($path, 0, ($height > 1200) ? 1200 : $height, false), $path, 100);
        $minResizeResult = imageJpeg(resizeImage($path, 180, 0, true), $minPath, 100);
    }


    if (!$minResizeResult || !$resizeResult) {
        $_SESSION["add-photo-error"]["upload-error"] = true;
        return false;
    }

    return true;
}