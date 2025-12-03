<?php
session_start();
require 'logic/class.php';

User::logout();
User::rollback();

$data = new Data();
$data->getHistoryDataBy($_SESSION["credential"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LostTrace : Aplikasi laporan barang hilang</title>
    <link rel="stylesheet" href="style/main.css">
</head>
<body>

    <div class="container df-nav">
        <h2>| Laporan</h2>
        <div class="nav">
            <a href="report-list.php">List</a>
            <a href="report.php">Lapor</a>
        </div>
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
                <?php if($_SESSION["history_by_user"] == null) : ?>
                    <tr>
                        <td colspan="5">Data tidak tersedia</td>
                    </tr>
                <?php else : ?>
                    <?php foreach($_SESSION["history_by_user"] as $user_history) : ?>
                        <tr>
                            <td>
                                <img src="<?= "uploads/". $user_history["foto"] ?>" width="100px">
                            </td>
                            <td><?= $user_history["deskripsi_barang"] ?></td>
                            <td><?= $user_history["lokasi"] ?></td>
                            <td><?= $user_history["kontak"] ?></td>
                            <td><?= $user_history["status"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="?logout=1" style="margin-top: 1em;">Logout</a>
    </div>
</body>
</html>