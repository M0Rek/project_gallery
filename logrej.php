<?php

require("include/function.php");


echo head("Logowanie/Rejestracja", "logrej");
?>
    <div class="row justify-content-around">
        <div class="col-lg-5 mt-5">
            <h2 class="text-center">Rejestracja</h2>
            <form id="registration-form" action="rejestracja.php" method="post" class="row g-3 needs-validation"
                  novalidate>
                <input type="hidden" name="T" value="register"/>
                <div class="col-12">
                    <label for="register-username" class="form-label">Login</label>
                    <input pattern="[A-Za-z0-9]{8,16}" name="username" type="text" class="form-control"
                           id="register-username" required>
                    <div class="invalid-feedback" id="register-username-validation">
                        Login musi mieć od 8 do 16 znaków, tylko litery i cyfry.
                    </div>
                </div>
                <div class="col-12">
                    <label for="register-password" class="form-label">Hasło</label>
                    <input pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,20}" name="password"
                           type="password"
                           class="form-control" id="register-password" required>
                    <div class="invalid-feedback" id="register-password-validation">
                        Hasło musi mieć od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra.
                    </div>
                </div>
                <div class="col-12">
                    <label for="register-password-retyped" class="form-label">Powtórz hasło</label>
                    <input name="retyped" type="password" class="form-control" id="register-password-retyped"
                           required>
                    <div class="invalid-feedback" id="register-password-retyped-validation">
                        Upewnij się, że hasła są takie same.
                    </div>
                </div>
                <div class="col-12">
                    <label for="register-email" class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" id="register-email" required>
                    <div class="invalid-feedback" id="register-email-validation">
                        Email nie jest poprawny.
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Zarejestruj się</button>
                </div>
            </form>

            <?php

            if (isset($_SESSION["registration-error"])) {
                $errors = $_SESSION["registration-error"];

                $html = '<div class="pt-5">';
                $errDiv = '<div class="alert alert-danger" role="alert">';

                if (isset($errors["invalid-username"])) {
                    $html .= $errDiv . 'Login musi mieć od 8 do 16 znaków, tylko litery i cyfry.</div>';
                }

                if (isset($errors["invalid-password"])) {
                    $html .= $errDiv . 'Hasło musi mieć od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra.</div>';
                }

                if (isset($errors["invalid-retyped"])) {
                    $html .= $errDiv . 'Upewnij się, że hasła są takie same.</div>';
                }

                if (isset($errors["invalid-email"])) {
                    $html .= $errDiv . 'Email nie jest poprawny.</div>';
                }

                if (isset($errors["database-error"])) {
                    $html .= $errDiv . 'Błąd połączenia z bazą danych.</div>';
                }

                if (isset($errors["username-exists"])) {
                    $html .= $errDiv . 'Ten login jest już zajęty!</div>';
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
        <div class="col-lg-5 mt-5">
            <h2 class="text-center">Logowanie</h2>
            <form id="login-form" action="logowania.php" method="post" class="row g-3 needs-validation" novalidate>
                <input type="hidden" name="T" value="login"/>
                <div class="col-12">
                    <label for="login-username" class="form-label">Login</label>
                    <input name="username" type="text" class="form-control" id="login-username" required>
                    <div class="invalid-feedback" id="login-username-validation">
                        Pole login nie może być puste!
                    </div>
                </div>
                <div class="col-12">
                    <label for="login-password" class="form-label">Hasło</label>
                    <input name="password" type="password" class="form-control" id="login-password" required>
                    <div class="invalid-feedback" id="login-password-validation">
                        Pole hasło nie może być puste!
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Zaloguj się</button>
                </div>
            </form>
            <?php
            if (isset($_SESSION["login-error"])) {
                $errors = $_SESSION["login-error"];

                $html = '<div class="pt-5">';
                $errDiv = '<div class="alert alert-danger" role="alert">';

                if (isset($errors["invalid-credentials"])) {
                    $html .= $errDiv . 'Nie ma takiego użytkownika.</div>';
                }

                if (isset($errors["database-error"])) {
                    $html .= $errDiv . 'Błąd połączenia z bazą danych.</div>';
                }

                if (isset($errors["user-blocked"])) {
                    $html .= $errDiv . 'Konto zostało zablokowane.</div>';
                }

                $html .= '</div>';

                echo $html;
            }
            ?>
        </div>
    </div>

<?php
echo footer("logrej-validation.js");

?>