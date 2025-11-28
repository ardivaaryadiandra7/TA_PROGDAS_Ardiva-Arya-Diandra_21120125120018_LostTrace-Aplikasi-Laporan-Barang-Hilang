<?php
session_start();

if(empty($_SESSION["credential"])) {
    header("Location: login-register.php");
}

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

        /* Link Lapor */
        a {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #0056b3;
            text-decoration: none;
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

        /* Status Colors */
        td:last-child {
            font-weight: 600;
        }

        /* No Data Message */
        td[colspan="5"] {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
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
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="report.php">Lapor</a>
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
                <?php if($_SESSION["report_data"] == null) : ?>
                    <tr>
                        <td colspan="5">Data tidak tersedia</td>
                    </tr>
                <?php else : ?>
                    <?php foreach($_SESSION["report_data"] as $user_report) : ?>
                        <tr>
                            <td>
                                <img src="<?= "uploads/". $user_report["foto"] ?>" width="100px">
                            </td>
                            <td><?= $user_report["deskripsi_barang"] ?></td>
                            <td><?= $user_report["lokasi"] ?></td>
                            <td><?= $user_report["kontak"] ?></td>
                            <td><?= $user_report["status"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>