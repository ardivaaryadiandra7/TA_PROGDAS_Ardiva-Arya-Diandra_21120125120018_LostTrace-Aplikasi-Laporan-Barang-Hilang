<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["credential"])) {
    $_SESSION["credential"] = null;
}

if (!isset($_SESSION["user_data"])) {
    $_SESSION["user_data"] = [
        ["username" => "budi", "password" => "budi123"],
        ["username" => "abdi", "password" => "abdi123"],
    ];
}

if (!isset($_SESSION["report_data"])) {
    $_SESSION["report_data"] = [];
}

class Data {
    public function getUserData() {
        return $_SESSION["user_data"];
    }

    public function setUserData($data) {
        $_SESSION["user_data"] = $data;
    }

    public function getReportData() {
        return $_SESSION["report_data"];
    }

    public function getReportDataBy($username) {
        $userReports = [];

        foreach($_SESSION["report_data"] as $report) {      
            if($username !== $report["username"]){
                continue;
            }
            $userReports[] = $report;
        }

        $_SESSION["report_by_user"] = $userReports;
        return $userReports;
    }

    public function setReportData($data) {
        if (!isset($_SESSION["report_data"]) || !is_array($_SESSION["report_data"])) {
            $_SESSION["report_data"] = [];
        }
    
        $_SESSION["report_data"][] = $data;
        
        return true;
    }

    public function setState($state) {
        foreach($_SESSION["report_data"] as &$report) {
            if ($report["id"] == $state) {
                $report["status"] = "selesai";
            }
        }
    }
}

class Report {
    private $deskripsi_barang;
    private $lokasi;
    private $kontak;
    private $foto;
    private $status;
    private $message;
    private $data;

    public function __construct($deskripsi_barang, $lokasi, $kontak, $status) {
        $this->data = new Data();

        $this->deskripsi_barang = trim(htmlspecialchars($deskripsi_barang));
        $this->lokasi = trim(htmlspecialchars($lokasi));
        $this->kontak = trim(htmlspecialchars($kontak));
        $this->status = $status;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setFoto($foto) {
        $this->foto = $foto;
    }

    public function addReport() {
        if (!isset($_SESSION["credential"])) {
            $this->message = "Anda harus login untuk membuat laporan";
            return false;
        }

        if(!$this->formValidate()) return false;
        if(!$this->imageValidate()) return false;

        $data = [
                "id" => uniqid(),
                "username" => $_SESSION["credential"],
                "deskripsi_barang" => $this->deskripsi_barang,
                "lokasi" => $this->lokasi,
                "kontak" => $this->kontak,
                "foto" => $this->foto,
                "status" => $this->status
        ];

        $this->data->setReportData($data);

        $this->message = "Laporan berhasil";
        
        return true;
    }

    private function formValidate() {
        if (empty($this->deskripsi_barang) || empty($this->lokasi) || empty($this->kontak)) {
            $this->message = "Form tidak boleh kosong";
            return false;
        }

        return true;
    }

    private function imageValidate() {
        if (!isset($this->foto) || $this->foto["error"] !== 0) {
            $this->message = "Foto harus diupload";
            return false;
        }

        $maxSize = 2 * 1024 * 1024;
        if ($this->foto["size"] > $maxSize) {
            $this->message = "Ukuran foto maksimal 2MB";
            return false;
        }

        $allowedExt = ["jpg", "jpeg", "png", "webp"];
        $fileName = $this->foto["name"];
        $fileTmp = $this->foto["tmp_name"];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            $this->message = "Format foto harus JPG, JPEG, PNG, atau WEBP";
            return false;
        }

        $newName = uniqid() . "." . $ext;
        $target = "uploads/" . $newName;

        if (!move_uploaded_file($fileTmp, $target)) {
            $this->message = "Gagal menyimpan foto";
            return false;
        }

        $this->foto = $newName;
        return true;
    }
}

class User {
    private $username;
    private $password;
    private $message;
    private $data;

    public function __construct($username, $password) {
        $this->username = trim(htmlspecialchars($username));
        $this->password = trim($password);
        $this->message = "";
        $this->data = new Data();
    }

    public function getMessage() {
        return $this->message;
    }

    public function userCredential() {
        if($this->loginValidate($this->auth())) {
            $_SESSION["credential"] = $this->username;
            $this->message = "Login berhasil";
            header("Location: report-list.php");
            return true;
        }

        return false;
    }

    public function addUser() {
        $user_data = $this->data->getUserData();

        if (empty($this->username) || empty($this->password)) {
            $this->message = "Form tidak boleh kosong";
            return false;
        } 

        if (in_array($this->username, array_column($user_data, "username"))){
            $this->message = "Username telah tersedia";
            return false;
        }

        $user_data[] = [
            "username" => $this->username,
            "password" => $this->password
        ];

        $this->data->setUserData($user_data);
        $this->message = "Registrasi berhasil";
        header("Location: login-register.php");
        return true;
    }

    private function loginValidate($auth) {
        if (empty($this->username) || empty($this->password)) {
            $this->message = "Form tidak boleh kosong";
            return false;
        } else {
            if(!$auth) {
                $this->message = "Username atau Password tidak tersedia";
                return false;
            } else {
                return true;
            }
        }
    }

    private function auth() {
        $user_data = $this->data->getUserData();
    
        foreach($user_data as $user) {
            if($user["username"] === $this->username && $user["password"] === $this->password) {
                return true;
            }
        }

        return false;
    }
}

?>