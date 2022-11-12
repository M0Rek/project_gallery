<?php

require("include/layout.php");

echo head("Logowanie/Rejestracja");

?>

    <form id="registration-form" action="rejestracja.php" method="post" class="row g-3 needs-validation" novalidate>
        <div class="col-md-4">
            <label for="register-username" class="form-label">Login</label>
            <input pattern="[A-Za-z0-9]{8,16}" name="username" type="text" class="form-control" id="register-username" placeholder="Login" required>
            <div class="invalid-feedback" id="register-username-validation">
               Login musi mieć od 8 do 16 znaków, tylko litery i cyfry.
            </div>
        </div>
        <div class="col-md-4">
            <label for="register-password" class="form-label">Hasło</label>
            <input pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*]{8,20}" name="password" type="password" class="form-control" id="register-password" required>
            <div class="invalid-feedback" id="register-password-validation">
                Hasło musi mieć od 8 do 20 znaków, minimum 1 duża litera, 1 mała litera i 1 cyfra.
            </div>
        </div>
        <div class="col-md-4">
            <label for="register-password-retyped" class="form-label">Powtórz hasło</label>
            <input name="retyped" type="password" class="form-control" id="register-password-retyped" required>
            <div class="invalid-feedback" id="register-password-retyped-validation" >
                Upewnij się, że hasła są takie same.
            </div>
        </div>
        <div class="col-md-4">
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


    <form id="login-form" method="post" class="row g-3 needs-validation" novalidate>
        <div class="col-md-4">
            <label for="login-username" class="form-label">Login</label>
            <input type="text" class="form-control" id="login-username" placeholder="Login" required>
            <div class="invalid-feedback" id="login-username-validation">
                Pole login nie może być puste!
            </div>
        </div>
        <div class="col-md-4">
            <label for="login-password" class="form-label">Hasło</label>
            <input type="password" class="form-control" id="login-password" required>
            <div class="invalid-feedback" id="login-password-validation">
                Pole hasło nie może być puste!
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" onsubmit="return validateLogin()" type="submit">Zaloguj się</button>
        </div>
    </form>


<?php

echo footer("validation.js");




?>