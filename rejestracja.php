<?php
session_start();
require("include/function.php");
require("include/database.php");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['T'] == 'register') {
        session_unset(); // Rozważyć zastąpienie unsetem indywidualnych pól sesji

        function returnIfErrors($login,$password,$retyped,$email) {
            if(isset($_SESSION["registration-error"])) {
                $recovery["login"] = $login;
                $recovery["password"] = $password;
                $recovery["retyped"] = $retyped;
                $recovery["email"] = $email;

                $_SESSION["registration-recovery"] = $recovery;
                header("Location: logrej.php");
                exit();
            }
        }

        $login = $_POST["username"];
        $password = $_POST["password"];
        $retyped = $_POST["retyped"];
        $email = $_POST["email"];

        $loginRegex = "/[A-Za-z0-9]{8,16}/";
        $passwordRegex = "/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*]{8,20}/";

        if(!preg_match($loginRegex, $login)) {
            $_SESSION["registration-error"]["invalid-username"] = true;
        }

        if(!preg_match($passwordRegex, $password)) {
            $_SESSION["registration-error"]["invalid-password"] = true;
        }

        if($password !== $retyped) {
            $_SESSION["registration-error"]["invalid-retyped"] = true;
        }

        if($email == "") {
            $_SESSION["registration-error"]["invalid-email"] = true;
        }

        returnIfErrors($login,$password,$retyped,$email);

        $conn = connectToDB();

        if(!$conn) {
            $_SESSION["registration-error"]["database-error"] = true;
        }
        else {

            //Pole login w bazie danych nie jest oznaczone jako unikalne według założeń, więc konieczne jest manualne
            //przeprowadzenie sprawdzenie czy login istnieje. Użyta do tego została tranzakcja by zapewnić ten sam stan bazy danych między sprawdzeniem loginu a insertem.
            $conn->begin_transaction();

            try {
                $stmt = $conn->prepare("SELECT * FROM uzytkownicy where login LIKE ?");
                $stmt->bind_param('s', $login);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows == 0) {
                    $stmt = $conn->prepare(" INSERT INTO uzytkownicy (id,login,haslo,email,zarejestrowany,uprawnienia,aktywny)
                    VALUES (default, ?, md5(?), ?, NOW(), 'użytkownik', 1)");
                    $stmt-> bind_param('sss', $login, $password, $email);
                    if(!$stmt->execute()) {
                        $_SESSION["registration-error"]["database-error"] = true;
                    }
                    else {
                        $stmt = $conn->prepare("SELECT id,login,haslo,email,zarejestrowany,uprawnienia,aktywny FROM uzytkownicy where login LIKE ?");
                        $stmt-> bind_param('s', $login);
                        if(!$stmt->execute()) {
                            $_SESSION["registration-error"]["database-error"] = true;
                        }
                        else {
                            $result = $stmt->get_result();
                            if($row = $result->fetch_assoc()) {
                                $user_data["id"] =  $row["id"];
                                $user_data["login"] =  $row["login"];
                                $user_data["email"] =  $row["email"];
                                $user_data["registered-date"] =  $row["zarejestrowany"];
                                $user_data["role"] =  $row["uprawnienia"];
                                $user_data["is-active"] =  $row["aktywny"];
                                $user_data["password-hash"] =  $row["haslo"];
                            }
                        }
                        $conn->commit();
                    }
                }
                else {
                    $_SESSION["registration-error"]["username-exists"] = true;
                }
            }
            catch (mysqli_sql_exception $e) {
                $_SESSION["registration-error"]["database-error"] = true;
                $conn->rollback();
            }

        }

        returnIfErrors($login,$password,$retyped,$email);

        $_SESSION["user-data"] = $user_data;
        header("Location: rejestracja-ok.php");
        exit();

    }






}






