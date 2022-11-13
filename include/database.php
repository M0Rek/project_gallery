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

function disconnectDB()
{
    $conn = $GLOBALS["conn"];
    if ($conn) {
        mysqli_close($conn);

    }
    return null;
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