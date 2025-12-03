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

if (!isset($_SESSION["history_data"])) {
    $_SESSION["history_data"] = [];
}

if (!isset($_SESSION["history_by_user"])) {
    $_SESSION["history_by_user"] = [];
}

// Kelas untuk mengelola data aplikasi
class Data {
    private $HISTORY_MAX_SIZE = 5;

    // method untuk mengambil data user dalam bentuk array
    public function getUserData() {
        return $_SESSION["user_data"];
    }
    
    // method untuk mengambil data laporan dalam bentuk array
    public function getReportData() {
        return $_SESSION["report_data"];
    }
    
    // method untuk mengambil data laporan berdasarkan username 
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

    // method untuk mengambil data history berdasarkan username 
    public function getHistoryDataBy($username) {
        $userHistory = [];
        
        foreach($_SESSION["history_data"] as $history) {      
            if($username !== $history["username"]){
                continue;
            }
            $userHistory[] = $history;
        }
        
        $_SESSION["history_by_user"] = $userHistory;
        return $userHistory;
    }
    
    // method untuk menambahkan data pengguna baru
    public function setUserData($data) {
        $_SESSION["user_data"] = $data;
    }

    // method untuk menambahkan data laporan baru
    public function setReportData($data) {
        if (!isset($_SESSION["report_data"]) || !is_array($_SESSION["report_data"])) {
            $_SESSION["report_data"] = [];
        }
    
        $_SESSION["report_data"][] = $data;
        
        return true;
    }

    // method untuk menetapkan status (ditemukan / kehilangan)
    public function setState($state) {
        foreach($_SESSION["report_data"] as $index => $report) {
            if ($report["id"] == $state) {
                $this->enqueue($report); 
                unset($_SESSION["report_data"][$index]);
                break;
            }
        }
        header('Location: report.php');
    }

    public function enqueue($report) {
        if (!isset($_SESSION["history_data"])) {
            $_SESSION["history_data"] = [];
        }

        $_SESSION["history_data"][] = $report;

        $this->dequeue();
    }

    public function dequeue() {
        if (count($_SESSION["history_data"]) > $this->HISTORY_MAX_SIZE) {
            // hapus elemen pertama (paling lama)
            array_shift($_SESSION["history_data"]);
        }
    }
}

// Kelas untuk mengelola form laporan
class Report {
    private $deskripsi_barang;
    private $lokasi;
    private $kontak;
    private $foto;
    private $status;
    private $message;
    private $data;

    // constructor : menginisialisasi property
    public function __construct($deskripsi_barang, $lokasi, $kontak, $status) {
        $this->data = new Data();

        $this->deskripsi_barang = trim(htmlspecialchars($deskripsi_barang));
        $this->lokasi = trim(htmlspecialchars($lokasi));
        $this->kontak = trim(htmlspecialchars($kontak));
        $this->status = $status;
    }

    // method untuk mendapatkan pesan aksi (error / berhasil)
    public function getMessage() {
        return $this->message;
    }

    // method untuk menambahkan file gambar
    public function setFoto($foto) {
        $this->foto = $foto;
    }

    // method untuk memvalidasi form
    private function formValidate() {
        if (empty($this->deskripsi_barang) || empty($this->lokasi) || empty($this->kontak)) {
            $this->message = "Form tidak boleh kosong";
            return false;
        }

        return true;
    }

    // method untuk memvalidasi gambar
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

    // method untuk menambah laporan ke session
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
}

// Kelas untuk mengelola user
class User {
    private $username;
    private $password;
    private $message;
    private $data;

    // method constructor : untuk menginsialisasi atribut
    public function __construct($username, $password) {
        $this->username = trim(htmlspecialchars($username));
        $this->password = trim($password);
        $this->message = "";
        $this->data = new Data();
    }

    // method untuk mendapatkan pesan aksi (error / berhasil)
    public function getMessage() {
        return $this->message;
    }

    // method untuk mengecek apakah user telah login
    public function userCredential() {
        if($this->loginValidate($this->auth())) {
            $_SESSION["credential"] = $this->username;
            $this->message = "Login berhasil";
            header("Location: report-list.php");
            return true;
        }

        return false;
    }

    // method untuk menambahkan user ke session setelah register
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
        $_SESSION["credential"] = $this->username;
        header("Location: report-list.php");
        return true;
    }

    // method untuk memvalidasi login, apakah user tersedia atau tidak
    private function loginValidate($auth) {
        if (empty($this->username) || empty($this->password)) {
            $this->message = "Form tidak boleh kosong";
            return false;
        } else {
            if(!$auth) {
                $this->message = "Pengguna tidak tersedia";
                return false;
            } else {
                return true;
            }
        }
    }

    // method untuk autentikasi, dicek apakah isi form sama dengan di session
    private function auth() {
        $user_data = $this->data->getUserData();
    
        foreach($user_data as $user) {
            if($user["username"] === $this->username && $user["password"] === $this->password) {
                return true;
            }
        }

        return false;
    }

    // method static untuk logout
    public static function logout() {
        if(isset($_GET["logout"])){
            unset($_SESSION["credential"]);
            header('Location: login-register.php');
        }
    }

    // method static untuk mengembalikan user ke login jika belum login
    public static function rollback() {
        if(empty($_SESSION["credential"])) {
            header("Location: login-register.php");
        }
    }
}

?>