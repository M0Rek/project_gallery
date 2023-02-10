<?php
require("include/function.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['T'] == 'login') {
        session_unset(); // Rozważyć zastąpienie unsetem indywidualnych pól sesji

        $login = trim($_POST["username"]);
        $password = $_POST["password"];

        $conn = connectToDB();

        if (!$conn) {
            $_SESSION["login-error"]["database-error"] = true;
        } else {
            try {
                if (($result = verifyUser($conn, $login, $password))->num_rows == 1) {
                    $userData = getUserData($result);
                    if ($userData["is-active"] != 1) {

                        $_SESSION["login-error"]["user-blocked"] = true;
                    }
                } else {
                    $_SESSION["login-error"]["invalid-credentials"] = true;
                }
            } catch (mysqli_sql_exception $e) {
                $_SESSION["login-error"]["database-error"] = true;
            }

        }

        if (isset($_SESSION["login-error"])) {
            header("Location: logrej.php");
            exit();
        }

        $_SESSION["user-data"] = $userData;
        header("Location: index.php");
        exit();

    }
}

header("Location: index.php");
exit();






