<?php
require("include/function.php");

if (!isset($_SESSION["user-data"]) || $_SESSION["user-data"]["role"] != "administrator") {
    header("Location: logrej.php");
    exit();
}

$conn = connectToDB();
print_r($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['T'] == 'change-role') {

        $userId = $_POST["user-id"];
        $userRole = $_POST["user-role"];
        changeUserRole($conn, $userId, $userRole);
    } else if ($_POST['T'] == 'unblock-user') {

        $userId = $_POST["user-id"];
        changeUserBlock($conn, $userId, 1);
    } else if ($_POST['T'] == 'block-user') {

        $userId = $_POST["user-id"];
        changeUserBlock($conn, $userId, 0);
    } else if ($_POST['T'] == 'delete-user') {

        $userId = $_POST["user-id"];
        deleteUserAsAdmin($conn, $userId);
    }
}

$show = "users";
if (isset($_GET["showBy"])) {
    if ($_GET["showBy"] == "administrators") {
        $show = "administrators";
    } else if ($_GET["showBy"] == "moderators") {
        $show = "moderators";
    }
}

echo adminHead("Admin - Użytkownicy", "admin-uzytkownicy");

switch ($show) {
    case "administrators":
        $result = getUsersByRole($conn, "administrator");
        break;
    case "moderators":
        $result = getUsersByRole($conn, "moderator");
        break;
    default:
        $result = getUsersByRole($conn, "użytkownik");
        break;
}

$users = (array)null;

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

?>

    <form class="row" method="get" action="admin-uzytkownicy.php">
        <div class="col-4 col-md-3">
            <input <?php echo(($show == "users") ? "checked" : "") ?> value="users"
                                                                      class="form-check-input me-1"
                                                                      id="users-radio" type="radio"
                                                                      name="showBy">
            <label class="form-check-label" for="users-radio">Użytkownicy</label>
        </div>
        <div class="col-4 d-flex col-md-3">
            <input <?php echo(($show == "moderators") ? "checked" : "") ?> value="moderators" id="moderators-radio"
                                                                           class="form-check-input me-1" type="radio"
                                                                           name="showBy">
            <label class="form-check-label" for="moderators-radio">Moderatorzy</label>
        </div>
        <div class="col-4 d-flex col-md-3">
            <input <?php echo(($show == "administrators") ? "checked" : "") ?> value="administrators"
                                                                               id="administrators-radio"
                                                                               class="form-check-input me-1"
                                                                               type="radio"
                                                                               name="showBy">
            <label class="form-check-label" for="administrators-radio">Administratorzy</label>
        </div>
        <div class="col-4 col-md-2">
            <input type="submit" class="btn btn-outline-dark" value="Pokaż wybranych">
        </div>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Login</th>
            <th scope="col">Email</th>
            <th scope="col">Utworzono</th>
            <th scope="col">Rola</th>
            <th scope="col">Aktywny</th>
            <th scope="col">Usuń</th>
        </tr>
        </thead>
        <tbody>

        <?php

        foreach ($users as $user) {
            ?>

            <tr>
                <th scope="row"><?php echo $user["id"] ?></th>
                <td><?php echo $user["login"] ?></td>
                <td><?php echo $user["email"] ?></td>
                <td><?php echo $user["zarejestrowany"] ?></td>
                <td>
                    <form class="row" method="post" action="admin-uzytkownicy.php">
                        <input type="hidden" name="user-id" value="<?php echo $user["id"] ?>"/>
                        <input type="hidden" name="T" value="change-role"/>
                        <div class="col-8">
                            <label class="form-label" for="user-role-input-<?php echo $user["id"] ?>"
                                   hidden>Tytuł</label>
                            <select class="form-select form-select-sm"
                                    onchange="document.getElementById('change-<?php echo $user["id"] ?>').disabled = false"
                                    name="user-role" id="user-role-input-<?php echo $user["id"] ?>">
                                <option <?php echo(($user["uprawnienia"] == "użytkownik") ? "selected" : "") ?>>
                                    Użytkownik
                                </option>
                                <option <?php echo(($user["uprawnienia"] == "moderator") ? "selected" : "") ?>>Moderator
                                </option>
                                <option <?php echo(($user["uprawnienia"] == "administrator") ? "selected" : "") ?>>
                                    Administrator
                                </option>
                            </select>
                        </div>
                        <div class="col-1">
                            <input type="submit" id="change-<?php echo $user["id"] ?>"
                                   class="btn btn-outline-success" value="Zmień" disabled>
                        </div>
                    </form>
                </td>
                <td><?php if ($user["aktywny"] == 0) { ?>
                        <form method="post" action="admin-uzytkownicy.php">
                            <input type="hidden" name="T" value="unblock-user"/>
                            <input type="hidden" name="user-id" value="<?php echo $user["id"] ?>"/>
                            <input type="submit" class="btn btn-warning" value="Odblokuj">
                        </form>
                        <?php
                    } else { ?>
                        <form method="post" action="admin-uzytkownicy.php">
                            <input type="hidden" name="T" value="block-user"/>
                            <input type="hidden" name="user-id" value="<?php echo $user["id"] ?>"/>
                            <input type="submit" class="btn btn-outline-warning" value="Zablokuj">
                        </form>
                    <?php }
                    ?></td>
                <td>
                    <form method="post" action="admin-uzytkownicy.php">
                        <input type="hidden" name="T" value="delete-user"/>
                        <input type="hidden" name="user-id" value="<?php echo $user["id"] ?>"/>
                        <input type="submit" class="btn btn-outline-danger" value="Usuń">
                    </form>
                </td>
            </tr>
            <?php
        }

        ?>
        </tbody>
    </table>

<?php

echo footer();


?>