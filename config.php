<?php
include 'config.php';

// Mengambil semua tugas
$result = $conn->query("SELECT * FROM tasks");

// Outputkan hasil dalam format HTML
$html_output = '<ul>';
while ($row = $result->fetch_assoc()) {
    $html_output .= '<li>';
    $html_output .= htmlspecialchars($row['title']);
    $html_output .= '<a href="config.php?delete=' . $row['id'] . '" onclick="return confirm(\'Apakah Anda yakin ingin menghapus tugas ini?\')">';
    $html_output .= '<i class="fas fa-trash-alt"></i></a>';
    $html_output .= '</li>';
}
$html_output .= '</ul>';

$conn->close();

// Simpan hasil dalam file HTML
file_put_contents('index.html', $html_output);
?>
