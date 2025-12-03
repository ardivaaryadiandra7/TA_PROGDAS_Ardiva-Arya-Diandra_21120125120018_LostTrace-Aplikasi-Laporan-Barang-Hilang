<?php
session_start();
require "logic/class.php";

$page = isset($_GET["page"]) ? $_GET["page"] : "login";
$message = "";

// LOGIN
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $user_auth = new User($_POST["username"], $_POST["password"]);
    $user_auth->userCredential();
    $message = $user_auth->getMessage();
}

// REGISTER
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $user_auth = new User($_POST["username"], $_POST["password"]);
    $user_auth->addUser();
    $message = $user_auth->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LostTrace : Aplikasi laporan barang hilang</title>
    <link rel="stylesheet" href="style/log-reg.css">
    
</head>
<body>

<div class="frame">
    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <p>LostTrace</p>
        </div>

        <!-- MESSAGE -->
        <?php if (!empty($message)) : ?>
            <div class="alert-error"
                style="background: <?= $message === 'Registrasi berhasil' ? '#c6ffa3' : '#fee2e2' ?>;
                       color: <?= $message === 'Registrasi berhasil' ? '#205200' : '#9a0404' ?>;">
                <p><?= $message ?></p>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <?php if ($page === "login") : ?>

            <form action="" method="POST">
                <div class="input-container">
                    <label>Nama Pengguna</label>
                    <input type="text" name="username">
                </div>

                <div class="input-container">
                    <label>Password</label>
                    <input type="text" name="password">
                </div>

                <div class="form-footer">
                    <p>Belum punya akun?</p>
                    <a href="?page=register">Daftar disini</a>
                </div>

                <input type="submit" value="Login" name="login">
            </form>

        <?php else: ?>

            <!-- REGISTER FORM -->
            <form action="" method="POST">
                <div class="input-container">
                    <label>Nama Pengguna</label>
                    <input type="text" name="username">
                </div>

                <div class="input-container">
                    <label>Password</label>
                    <input type="text" name="password">
                </div>

                <input type="submit" value="Register" name="register">

                <div class="form-footer">
                    <p>Kembali ke</p>
                    <a href="login-register.php">Login</a>
                </div>
            </form>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
