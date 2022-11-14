<?php

require("include/function.php");


echo head("Logowanie/Rejestracja");
?>


    <div class="container-lg mt-5">
        <div class="row justify-content-around">
            <div class="col-lg-5">
                <h2 class="text-center">Rejestracja</h2>
                <form id="registration-form" action="rejestracja.php" method="post" class="row g-3 needs-validation"
                      novalidate>
                    <input type="hidden" name="T" value="register"/>
                    <label for="register-username" class="form-label">Login</label>
                    <input pattern="[A-Za-z0-9]{8,16}" name="username" type="text" class="form-control"
                           id="register-username"
                           placeholder="Login" required>
                    <div class="invalid-feedback" id="register-username-validation">
                        Login musi mieć od 8 do 16 znaków, tylko litery i cyfry.
                    </div>
                    <label for="register-password" class="form-label">Hasło</label>
                    <input pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*]{8,20}" name="password"
                           type="password"
                           class="form-control" id="register-password" required>
                    <div class="invalid-feedback" id="register-password-validation">
                        Hasło musi mieć od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra.
                    </div>
                    <label for="register-password-retyped" class="form-label">Powtórz hasło</label>
                    <input name="retyped" type="password" class="form-control" id="register-password-retyped"
                           required>
                    <div class="invalid-feedback" id="register-password-retyped-validation">
                        Upewnij się, że hasła są takie same.
                    </div>
                    <label for="register-email" class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" id="register-email" required>
                    <div class="invalid-feedback" id="register-email-validation">
                        Email nie jest poprawny.
                    </div>
                    <button class="btn btn-primary" type="submit">Zarejestruj się</button>
                </form>

                <?php

                if (isset($_SESSION["registration-error"])) {
                    $errors = $_SESSION["registration-error"];

                    $html = '<div class="pt-5">';
                    $errDiv = '<div class="alert alert-danger" role="alert">';

                    if ($errors["invalid-username"]) {
                        $html .= $errDiv . '
           Login musi mieć od 8 do 16 znaków, tylko litery i cyfry.</div>';
                    }

                    if ($errors["invalid-password"]) {
                        $html .= $errDiv . '
            Hasło musi mieć od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra.</div>';
                    }

                    if ($errors["invalid-retyped"]) {
                        $html .= $errDiv . '
            Upewnij się, że hasła są takie same.</div>';
                    }

                    if ($errors["invalid-email"]) {
                        $html .= $errDiv . '
            Email nie jest poprawny.</div>';
                    }

                    if ($errors["database-error"]) {
                        $html .= $errDiv . '
            Błąd połączenia z bazą danych.</div>';
                    }

                    if ($errors["username-exists"]) {
                        $html .= $errDiv . '
            Ten login jest już zajęty!</div>';
                    }

                    $html .= '</div>';

                    echo $html;
                }

                if (isset($_SESSION["registration-recovery"])) {
                    $recovery = $_SESSION["registration-recovery"];
                    echo '
                    <script>
                      document.getElementById("register-username").value ="' . $recovery["login"] . '"
                      document.getElementById("register-password").value ="' . $recovery["password"] . '"
                      document.getElementById("register-password-retyped").value = "' . $recovery["retyped"] . '"
                      document.getElementById("register-email").value = "' . $recovery["email"] . '"
                    </script>';
                } ?>
            </div>
            <div class="col-lg-5">
                <h2 class="text-center">Logowanie</h2>
                <form id="login-form" action="logowania.php" method="post" class="row g-3 needs-validation" novalidate>
                    <input type="hidden" name="T" value="login"/>
                    <label for="login-username" class="form-label">Login</label>
                    <input name="username" type="text" class="form-control" id="login-username" placeholder="Login"
                           required>
                    <div class="invalid-feedback" id="login-username-validation">
                        Pole login nie może być puste!
                    </div>
                    <label for="login-password" class="form-label">Hasło</label>
                    <input name="password" type="password" class="form-control" id="login-password" required>
                    <div class="invalid-feedback" id="login-password-validation">
                        Pole hasło nie może być puste!
                    </div>
                    <button class="btn btn-primary" type="submit">Zaloguj się</button>
                </form>
                <?php
                if (isset($_SESSION["login-error"])) {
                    $errors = $_SESSION["login-error"];

                    $html = '<div class="pt-5">';
                    $errDiv = '<div class="alert alert-danger" role="alert">';

                    if ($errors["invalid-credentials"]) {
                        $html .= $errDiv . '
           Nie ma takiego użytkownika.</div>';
                    }

                    if ($errors["database-error"]) {
                        $html .= $errDiv . '
            Błąd połączenia z bazą danych.</div>';
                    }

                    if ($errors["user-blocked"]) {
                        $html .= $errDiv . '
            Konto zostało zablokowane.</div>';
                    }

                    $html .= '</div>';

                    echo $html;
                }
                ?>
            </div>
        </div>
    </div>

<?php
echo footer("validation.js");

?>