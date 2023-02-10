<?php

//Use head() and footer() functions together and in that order on each site. Otherwise, the body tag won't get closed correctly.
function head($title = "", $active = "index")
{
    return '
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galeria - ' . $title . '</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="style/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href="style/style.css" rel="stylesheet">
</head>
<body>
<div id="wrap">
<div id="main">
' . navbar($active) . '
<div class="container-md mt-5">';
}

function footer($script = "")
{
    $txt = '<script src="javascript/bootstrap.bundle.min.js"></script>';
    $txt .= '<script src="javascript/script.js"></script>';

    if ($script != "")
        $txt .= '<script src="javascript/' . $script . '"></script>';

    $txt .= '
</div>
</div>
</div>
<div class="footer">
<h5 class="text-end text-secondary me-3">Wykonano przez: Michał Morawski</h5>
</div>
</body>
</html>';

    return $txt;
}

function isActive($active, $nav)
{
    return ($active == $nav) ? " active" : "";
}

function navbar($active = "index")
{
    return '
<nav class="navbar sticky-top navbar-dark bg-primary navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" id="index" href="index.php">Galeria</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      ' . navbarItems($active) . '
    </div>
  </div>
</nav>';
}

function navbarItems($active)
{
    $userData = (isset($_SESSION["user-data"])) ? $_SESSION["user-data"] : null;


    $txt = '<ul class="navbar-nav mr-auto">';
    $txt .= '<li class="nav-item"><a id="dodaj-album" class="nav-link' . isActive($active, "dodaj-album") . '" href="' . (isset($userData) ? "dodaj-album.php" : "logrej.php") . '">Załóż album</a></li>';
    $txt .= '<li class="nav-item"><a id="dodaj-foto" class="nav-link' . isActive($active, "dodaj-foto") . '" href="' . (isset($userData) ? "dodaj-foto.php" : "logrej.php") . '">Dodaj zdjęcie</a></li>';
    $txt .= '<li class="nav-item"><a id="top-foto" class="nav-link' . isActive($active, "top-foto") . '" href="top-foto.php">Najlepiej oceniane</a></li>';
    $txt .= '<li class="nav-item" ><a id="nowe-foto" class="nav-link' . isActive($active, "nowe-foto") . '" href = "nowe-foto.php" >Najnowsze</a ></li >';
    if (isset($userData)) {
        $txt .= '<li class="nav-item" ><a id="konto" class="nav-link' . isActive($active, "konto") . '" href = "konto.php" >Moje konto</a ></li >';
        $txt .= '<li class="nav-item" ><a id="wyloguj" class="nav-link' . isActive($active, "wyloguj") . '" href = "wyloguj.php" >Wyloguj się</a ></li >';

        if ($userData["role"] == "moderator" || $userData["role"] == "administrator") {
            $txt .= '<li class="nav-item" ><a id="admin" class="nav-link' . isActive($active, "admin") . '" href = "admin/index.php" >Panel administracyjny</a ></li >';
        }
    } else {
        $txt .= '<li class="nav-item" ><a id="log" class="nav-link' . isActive($active, "logrej") . '" href = "logrej.php" >Zaloguj się</a ></li >';
        $txt .= '<li class="nav-item" ><a id="rej" class="nav-link' . isActive($active, "logrej") . '" href = "logrej.php" >Rejestracja</a ></li >';
    }
    $txt .= '</ul>';
    return $txt;
}

function pagination($currentPage, $pageCount)
{
    if (isset($_GET)) {
        $get = getParamsToUrl(array("page"));
    } else $get = "";

    if ($pageCount == 1)
        return "";

    $txt = '<ul class="col-12 pagination justify-content-center">
                <li class="page-item ' . (($currentPage <= 1) ? "disabled" : "") . '">
                    <a class="page-link" href="?page=' . ($currentPage - 1) . $get . '" aria-label="Previous">
                        <span>&laquo;</span>
                    </a>
                </li>';


    for ($i = 1; $i <= $pageCount; $i++) {
        $txt .= '<li class="page-item ' . (($currentPage == $i) ? "disabled" : "") . '"><a class="page-link" href="?page=' . $i . $get . '">' . $i . '</a></li>';
    }

    $txt .= '<li class="page-item ' . (($currentPage >= $pageCount) ? "disabled" : "") . '">
                    <a class="page-link" href="?page=' . ($currentPage + 1) . $get . '" aria-label="Next">
                        <span>&raquo;</span>
                    </a>
                </li>
             </ul>';
    return $txt;
}

/**
 * @param $excludeKeys
 * @return string End part of url containing not excluded get values
 */
function getParamsToUrl($excludeKeys)
{

    $url = "";
    if (isset($_GET)) {
        foreach ($_GET as $key => $value) {
            if (!in_array($key, $excludeKeys))
                $url .= "&" . $key . "=" . $value;
        }
    }
    return $url;
}

function backToAlbumsButton()
{
    return '<div class="my-2 d-flex">
        <button type="button" onclick="redirectToPage(\'index.php\')" class="btn btn-secondary btn-sm">Powrót do albumów</button>
    </div>';
}

function backToAlbumButton($albumId)
{
    return '<div class="my-2 d-flex">
        <button type="button" onclick="redirectToPage(\'album.php?id=' . $albumId . '\')" class="btn btn-primary btn-sm">Powrót do albumu</button>
    </div>';
}

function photos($photos)
{
    $txt = '<div class="row my-3 d-flex justify-content-center g-3">';
    foreach ($photos as $photo) {
        $txt .= '<div class="d-flex w-auto justify-content-center">
            <img alt="' . $photo["opis"] . '" 
            class="album-thumbnail" 
            onclick="window.location.href=\'foto.php?id=' . $photo["id"] . '\'" 
            data-bs-toggle="tooltip" 
            data-bs-html="true" 
            data-bs-placement="bottom" 
            data-bs-title="' . ($photo["opis"] != "" ? '<b>Opis:</b> ' . $photo["opis"] . '<br>' : "") . '
            <b>Utworzono:</b> ' . $photo["data"] . '<br>  
            <b>Autor:</b> ' . $photo["tworca"] . '<br> 
            <b>Album:</b> ' . $photo["tytul_albumu"] . '<br>
            <b>Ocena:</b> ' . $photo["ocena"] . '"  
            src="' . minPhotoPath($photo["id_albumu"], $photo["id"]) . '"></div>';
    }
    return $txt . "</div>";
}
