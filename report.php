<?php

session_start();
require 'logic/class.php';
$data = new Data();
$message = "";

if(empty($_SESSION["credential"])) {
    header("Location: login-register.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["report"])) {
    $report = new Report($_POST["deskripsi_barang"], $_POST["lokasi"], $_POST["kontak"], $_POST["status"]);
    $report->setFoto($_FILES["foto"]);
    $report->addReport();
    $message = $report->getMessage();
}

if(isset($_GET["id"])){
    $data->setState($_GET["id"]);
    header('Location: report.php');
}

if(isset($_GET["logout"])){
    unset($_SESSION["credential"]);
    header('Location: login-register.php');
}

$data->getReportDataBy($_SESSION["credential"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LostTrace : Aplikasi laporan barang hilang</title>
    
    <style>
        /* style/style.css */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(45deg, #87BAC3, #D6F4ED);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: rgba(234, 234, 234, 1);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Navigation Links */
        a {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s ease;
            margin-right: 10px;
        }

        a:hover {
            background: #0056b3;
            text-decoration: none;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        form > div:not(.df) {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Label Styles */
        label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Input Styles */
        input[type="text"],
        input[type="file"] {
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

        /* Radio Button Styles */
        .df {
            display: flex;
            gap: 1em;
            align-items: center;
        }

        .df input[type="radio"] {
            margin-right: 5px;
        }

        .df span {
            color: #333;
            font-weight: 500;
        }

        /* Submit Button */
        input[type="submit"] {
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: 600;
        }

        input[type="submit"]:hover {
            background: #0056b3;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        thead {
            background: #007bff;
            color: white;
        }

        thead tr td {
            font-weight: 600;
            padding: 15px;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        tbody td {
            padding: 15px;
            color: #333;
        }

        /* Image Styles */
        img {
            border-radius: 5px;
            object-fit: cover;
        }

        /* Status Styles */
        td:last-child a {
            background: #28a745;
            padding: 8px 16px;
            font-size: 14px;
        }

        td:last-child a:hover {
            background: #218838;
        }

        td:last-child p {
            color: #6c757d;
            font-weight: 500;
            margin: 0;
        }

        /* No Data Message */
        td[colspan="5"] {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }

        .alert{
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 1.5em;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            table {
                font-size: 14px;
            }
            
            thead tr td,
            tbody td {
                padding: 10px 8px;
            }
            
            img {
                width: 80px !important;
            }
            
            .df {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .container {
                padding: 15px;
            }
            
            a {
                padding: 10px 20px;
                font-size: 14px;
                margin-bottom: 10px;
            }
            
            input[type="text"],
            input[type="file"] {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="report-list.php">List</a>
        <a href="?logout=1">Logout</a>
    </div>

    <div class="container">
        <?php if(!empty($message)) : ?>
                <div class="alert"
                    style="background: <?= $message == 'Laporan berhasil' ? '#c6ffa3ff' : '#fee2e2' ?>; 
                           color: <?= $message == 'Laporan berhasil' ? '#205200ff' : '#9a0404ff' ?>;"
                >
                    <p><?= $message ?></p>
                </div>
            <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="">
                <label for="">Deskripsi barang</label>
                <input type="text" name="deskripsi_barang">
            </div>
            <div class="">
                <label for="">Lokasi</label>
                <input type="text" name="lokasi">
            </div>
            <div class="">
                <label for="">Kontak</label>
                <input type="text" name="kontak">
            </div>
            <div class="">
                <label for="">Foto</label>
                <input type="file" name="foto">
            </div>
            <div class="df">
                <div class="df">
                    <input type="radio" name="status" value="ditemukan" checked>
                    <span>Ditemukan</span>
                </div>
                <div class="df">
                    <input type="radio" name="status" value="kehilangan">
                    <span>Kehilangan</span>
                </div>
            </div>
            <input type="submit" name="report">
        </form>
    </div>

    <div class="container">
        <table width="100%">
            <thead>
                <tr>
                    <td>Foto</td>
                    <td>Deskripsi</td>
                    <td>Lokasi</td>
                    <td>Kontak</td>
                    <td>Status</td>
                </tr>
            </thead>

            <tbody>
                <?php if($_SESSION["report_by_user"] == null) : ?>
                    <tr>
                        <td colspan="5">Data tidak tersedia</td>
                    </tr>
                <?php else : ?>
                    <?php foreach($_SESSION["report_by_user"] as $user_report) : ?>
                        <tr>
                            <td>
                                <img src="<?= "uploads/". $user_report["foto"] ?>" width="100px">
                            </td>
                            <td><?= $user_report["deskripsi_barang"] ?></td>
                            <td><?= $user_report["lokasi"] ?></td>
                            <td><?= $user_report["kontak"] ?></td>
                            <td>
                                <?php if($user_report["status"] == "selesai") : ?>
                                    <p>selesai</p>
                                <?php else : ?>
                                    <a href="?id=<?= $user_report["id"] ?>"><?= $user_report["status"] ?></a>
                                <?php endif; ?>    
                                
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>