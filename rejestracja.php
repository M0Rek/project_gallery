<?php
/** @noinspection PhpUndefinedVariableInspection */
require("include/function.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['T'] == 'register') {
    session_unset(); // Rozważyć zastąpienie unsetem indywidualnych pól sesji

    function returnIfErrors($login, $password, $retyped, $email)
    {
        if (isset($_SESSION["registration-error"])) {
            $recovery["login"] = $login;
            $recovery["password"] = $password;
            $recovery["retyped"] = $retyped;
            $recovery["email"] = $email;

            $_SESSION["registration-recovery"] = $recovery;
            header("Location: logrej.php");
            exit();
        }
    }

    $login = trim($_POST["username"]);
    $password = $_POST["password"];
    $retyped = $_POST["retyped"];
    $email = trim($_POST["email"]);

    if (!preg_match(LOGIN_REGEX, $login)) {
        $_SESSION["registration-error"]["invalid-username"] = true;
    }

    if (!preg_match(PWD_REGEX, $password)) {
        $_SESSION["registration-error"]["invalid-password"] = true;
    }

    if ($password !== $retyped) {
        $_SESSION["registration-error"]["invalid-retyped"] = true;
    }

    if ($email == "") {
        $_SESSION["registration-error"]["invalid-email"] = true;
    }

    returnIfErrors($login, $password, $retyped, $email);

    $conn = connectToDB();

    if (!$conn) {
        $_SESSION["registration-error"]["database-error"] = true;
    } else {

        //Pole login w bazie danych nie jest oznaczone jako unikalne według założeń, więc konieczne jest manualne
        //przeprowadzenie sprawdzenie czy login istnieje. Użyta do tego została tranzakcja by zapewnić ten sam stan bazy danych między sprawdzeniem loginu a insertem.
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("SELECT * FROM uzytkownicy where login LIKE ?");
            $stmt->bind_param('s', $login);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $stmt = $conn->prepare(" INSERT INTO uzytkownicy (id,login,haslo,email,zarejestrowany,uprawnienia,aktywny)
                    VALUES (default, ?, md5(?), ?, NOW(), 'użytkownik', 1)");
                $stmt->bind_param('sss', $login, $password, $email);
                if (!$stmt->execute()) {
                    $_SESSION["registration-error"]["database-error"] = true;
                } else {
                    $stmt = $conn->prepare("SELECT id,login,haslo,email,zarejestrowany,uprawnienia,aktywny FROM uzytkownicy where login LIKE ?");
                    $stmt->bind_param('s', $login);
                    if (!$stmt->execute()) {
                        $_SESSION["registration-error"]["database-error"] = true;
                    } else {
                        $result = $stmt->get_result();
                        if ($result->num_rows == 1) {
                            $userData = getUserData($result);
                        } else {
                            throw new mysqli_sql_exception;
                        }
                    }
                    $conn->commit();
                }
            } else {
                $_SESSION["registration-error"]["username-exists"] = true;
            }
        } catch (mysqli_sql_exception $e) {
            $_SESSION["registration-error"]["database-error"] = true;
            $conn->rollback();
        }

    }

    returnIfErrors($login, $password, $retyped, $email);

    $_SESSION["user-data"] = $userData;
    header("Location: rejestracja-ok.php");
    exit();
}
header("Location: index.php");
exit();







