<?php
session_start();
require 'logic/class.php';
$message = "";

User::logout();
User::rollback();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["report"])) {
    $report = new Report($_POST["deskripsi_barang"], $_POST["lokasi"], $_POST["kontak"], $_POST["status"]);
    $report->setFoto($_FILES["foto"]);
    $report->addReport();
    $message = $report->getMessage();
}

$data = new Data();
$data->getReportDataBy($_SESSION["credential"]);

if(isset($_GET["id"])){
    $data->setState($_GET["id"]);
}

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
            <a href="history.php">History</a>
        </div>
    </div>

    <div class="container">
        <?php if(!empty($message)) : ?>
            <div class="alert"
                 style="background: <?= $message == 'Laporan berhasil' ? '#c6ffa3ff' : '#fee2e2' ?>; 
                        color: <?= $message == 'Laporan berhasil' ? '#205200ff' : '#9a0404ff' ?>;">
                <p><?= $message ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="">
                <label for="">Deskripsi barang</label>
                <input type="text" name="deskripsi_barang">
            </div>
            <div class="">
                <label for="">Lokasi</label>
                <input type="text" name="lokasi">
            </div>
            <div class="">
                <label for="">Kontak </label>
                <input type="text" name="kontak" placeholder="DM IG, Line, dsb">
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
                    <td>Kontak (dm IG, Line, dsb)</td>
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
                                <a href="?id=<?= $user_report["id"] ?>"><?= $user_report["status"] ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <a href="?logout=1" style="margin-top: 1em;">Logout</a>
    </div>

</body>
</html>