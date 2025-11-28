<?php
session_start();
require "logic/class.php";
$page = "login";
$message = "";

// Kode untuk login
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $user_auth = new User($_POST["username"], $_POST["password"]);
    $user_auth->userCredential();
    $message = $user_auth->getMessage();
}

// Kode untuk Register
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $user_auth = new User($_POST["username"], $_POST["password"]);
    $user_auth->addUser();
    $message = $user_auth->getMessage();
}

// Kode untuk ganti halaman
if(isset($_GET["page"])){
    $page = $_GET["page"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LostTrace : Aplikasi laporan barang hilang</title>

    <style>
        /* style/styles.css */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(45deg, #87BAC3, #D6F4ED);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .frame {
            background: rgba(234, 234, 234, 1);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .container {
            width: 100%;
        }

        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Label styles */
        label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        /* Input styles */
        input[type="text"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
        }

        /* Submit button */
        input[type="submit"] {
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background: #0056b3;
        }

        /* Link container */
        .container > form > div:not(.input-container) {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .container > form > div:not(.input-container) p {
            color: #666;
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Link styles */
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert-error {
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 1.5em;
        }

        .header {
            background: radial-gradient( #D6F4ED, #afd9e0ff);
            padding: 1.5em;
            text-align: center;
            margin-bottom: 1.5em;
            border-radius: 8px;

            p {
                font-weight: 700;
                font-size: 2em;
                color: #4A70A9;
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .frame {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="container">
            <div class="header">
                <p>FindTrace</p>
            </div>

            <?php if(!empty($message)) : ?>
                <div class="alert-error"
                    style="background: <?= $message == 'Registrasi berhasil' ? '#c6ffa3ff' : '#fee2e2' ?>; 
                           color: <?= $message == 'Registrasi berhasil' ? '#205200ff' : '#9a0404ff' ?>;">
                    <p><?= $message ?></p>
                </div>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <?php if($page === "login") : ?>
                <form action="" method="POST">
                    <div class="input-container">
                        <label for="name">Nama Pengguna</label>
                        <input type="text" name="username">
                    </div>
                    <div class="input-container">
                        <label for="name">Password</label>
                        <input type="text" name="password">
                    </div>
                    <div class="">
                        <p>Belum punya akun?</p>
                        <a href="?page=register">Daftar disini</a>
                    </div>
                    <input type="submit" value="Login" name="login">
                </form>   
            <?php else : ?>
                <!-- REGISTER FORM -->
                <form action="" method="POST">
                    <div class="input-container">
                        <label for="name">Nama Pengguna</label>
                        <input type="text" name="username">
                    </div>
                    <div class="input-container">
                        <label for="name">Password</label>
                        <input type="text" name="password">
                    </div>
                    
                    <input type="submit" value="Register" name="register">
        
                    <div class="">
                        <p>Kembali ke</p>
                        <a href="login-register.php">Login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>