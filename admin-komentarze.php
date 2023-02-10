<?php
require("include/function.php");

if (!isset($_SESSION["user-data"]) || ($_SESSION["user-data"]["role"] != "administrator" && $_SESSION["user-data"]["role"] != "moderator")) {
    header("Location: logrej.php");
    exit();
}

$conn = connectToDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['T'] == 'change-comment' && $_SESSION["user-data"]["role"] == "administrator") {
        $commentId = $_POST["comment-id"];
        $commentContent = $_POST["comment-content"];
        changeCommentAsAdmin($conn, $commentId, $commentContent);

    } else if ($_POST['T'] == 'accept-comment') {
        $commentId = $_POST["comment-id"];

        acceptCommentAsAdmin($conn, $commentId);
    } else if ($_POST['T'] == 'delete-comment') {
        $commentId = $_POST["comment-id"];

        deleteCommentAsAdmin($conn, $commentId);
    }
}

$show = "all";

if (isset($_GET["showBy"]) && $_GET["showBy"] == "unaccepted") {
    $show = "unaccepted";
}

echo adminHead("Admin - Komentarze", "admin-komentarze");

if ($show == "unaccepted") {
    $result = getUnacceptedComments($conn);
} else {
    $result = getAllComments($conn);
}

$comments = (array)null;

while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

?>
    <form class="row" method="get" action="admin-komentarze.php">
        <div class="col-4 col-md-3">
            <input <?php echo(($show == "all") ? "checked" : "") ?> value="all"
                                                                    class="form-check-input me-1"
                                                                    id="all-radio" type="radio"
                                                                    name="showBy">
            <label class="form-check-label" for="all-radio">Pokaż wszystkie</label>
        </div>
        <div class="col-4 d-flex col-md-3">
            <input <?php echo(($show == "unaccepted") ? "checked" : "") ?> value="unaccepted" id="unaccepted-radio"
                                                                           class="form-check-input me-1" type="radio"
                                                                           name="showBy">
            <label class="form-check-label" for="unaccepted-radio">Pokaż niezaakceptowane</label>
        </div>
        <div class="col-4 col-md-2">
            <input type="submit" class="btn btn-outline-dark" value="Pokaż wybrane">
        </div>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Komentarz</th>
            <th scope="col">Twórca</th>
            <th scope="col">Utworzono</th>
            <th scope="col">Zaakceptuj</th>
            <th scope="col">Usuń</th>
        </tr>
        </thead>
        <tbody>

        <?php

        foreach ($comments as $comment) {
            ?>

            <tr>
                <th scope="row"><?php echo $comment["id"] ?></th>
                <td>
                    <form class="row" method="post" action="admin-komentarze.php">
                        <input type="hidden" name="comment-id" value="<?php echo $comment["id"] ?>"/>
                        <input type="hidden" name="T" value="change-comment"/>
                        <div class="col-9">
                            <label class="form-label" for="comment-title-input-<?php echo $comment["id"] ?>"
                                   hidden>Tytuł</label>
                            <textarea <?php echo(($_SESSION["user-data"]["role"] == "administrator") ? "" : "disabled") ?>
                                name="comment-content"
                                onchange="document.getElementById('change-<?php echo $comment["id"] ?>').disabled = false"
                                id="comment-title-input-<?php echo $comment["id"] ?>" class="form-control"
                                rows="5"><?php echo $comment["komentarz"] ?></textarea>
                        </div>
                        <div class="col-2">
                            <input type="submit" id="change-<?php echo $comment["id"] ?>"
                                   class="btn btn-outline-success" value="Zmień" disabled>
                        </div>
                    </form>
                </td>
                <td><?php echo $comment["tworca"] ?></td>
                <td><?php echo $comment["data"] ?></td>
                <td><?php if ($comment["zaakceptowany"] == 0) { ?>
                        <form method="post" action="admin-komentarze.php">
                            <input type="hidden" name="T" value="accept-comment"/>
                            <input type="hidden" name="comment-id" value="<?php echo $comment["id"] ?>"/>
                            <input type="submit" class="btn btn-outline-success" value="Zaakceptuj">
                        </form>
                        <?php
                    } else {
                        echo "Zaakceptowano";
                    }
                    ?></td>
                <td>
                    <form method="post" action="admin-komentarze.php">
                        <input type="hidden" name="T" value="delete-comment"/>
                        <input type="hidden" name="comment-id" value="<?php echo $comment["id"] ?>"/>
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