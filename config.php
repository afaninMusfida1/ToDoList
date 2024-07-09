<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "todo_list";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menambah tugas baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $stmt = $conn->prepare("INSERT INTO tasks (title) VALUES (?)");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Mengarahkan kembali ke index.php setelah menambah tugas
    exit();
}

// Menghapus tugas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Mengarahkan kembali ke index.php setelah menghapus tugas
    exit();
}

// Mengambil semua tugas
$result = $conn->query("SELECT * FROM tasks");
?>
