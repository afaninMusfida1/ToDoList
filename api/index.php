<?php
// api/index.php
include 'config.php';

// --- LOGIC PHP (CRUD) ---

// 1. Menambah tugas baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && !isset($_POST['edit_title'])) {
    $title = $_POST['title'];
    $stmt = $conn->prepare("INSERT INTO tasks (title) VALUES (?)");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// 2. Menghapus tugas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// 3. Mengedit tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_title']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['edit_title'];
    $stmt = $conn->prepare("UPDATE tasks SET title = ? WHERE id = ?");
    $stmt->bind_param("si", $title, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// 4. Ubah status (Done/Undone)
if (isset($_POST['change_status'])) {
    $taskId = $_POST['task_id'];
    $currentStatus = $_POST['current_status'];
    $newStatus = $currentStatus == 'done' ? 'undone' : 'done';
    
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $taskId);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// 5. Mengambil semua tugas untuk ditampilkan
$result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="container mx-auto p-4 max-w-md bg-white rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold text-center mb-6">To-Do List</h1>

        <form method="post" action="" class="flex mb-6 border rounded overflow-hidden">
            <input type="text" name="title" placeholder="Tambahkan tugas baru" required
                class="flex-grow p-3 text-lg border-none outline-none">
            <button type="submit" class="bg-blue-500 text-white p-3 hover:bg-blue-700 flex items-center justify-center">
                <i class="fas fa-plus"></i>
            </button>
        </form>

        <ul>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $taskClass = $row['status'] == 'done' ? 'bg-green-100 opacity-75' : 'bg-white';
                    $textClass = $row['status'] == 'done' ? 'line-through text-gray-500' : 'text-gray-800';
                    ?>
                    <li class="mb-2 p-4 rounded-lg shadow flex justify-between items-center border <?php echo $taskClass; ?>">
                        <span class="text-lg <?php echo $textClass; ?>"><?php echo htmlspecialchars($row['title']); ?></span>
                        
                        <div class="flex items-center space-x-2">
                            <a href="#" data-id="<?php echo $row['id']; ?>"
                                data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                class="text-blue-500 hover:text-blue-700 edit-btn px-2">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="index.php?delete=<?php echo $row['id']; ?>"
                                onclick="return confirm('Hapus tugas ini?')"
                                class="text-red-500 hover:text-red-700 px-2">
                                <i class="fas fa-trash-alt"></i>
                            </a>

                            <form action="" method="post" class="inline">
                                <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="current_status" value="<?php echo $row['status']; ?>">
                                <button type="submit" name="change_status"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-white <?php echo $row['status'] == 'done' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600'; ?>">
                                    <?php if ($row['status'] == 'done'): ?>
                                        <i class="fas fa-undo text-xs"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check text-xs"></i>
                                    <?php endif; ?>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">Belum ada tugas.</p>
            <?php endif; ?>
        </ul>
    </div>

    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-2xl mb-4 font-bold">Edit Task</h2>
            <form id="editForm" method="post" action="">
                <input type="hidden" name="id" id="editId">
                <input type="text" name="edit_title" id="editTitle" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelBtn"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editBtns = document.querySelectorAll('.edit-btn');
            const editModal = document.getElementById('editModal');
            const editId = document.getElementById('editId');
            const editTitle = document.getElementById('editTitle');
            const cancelBtn = document.getElementById('cancelBtn');

            editBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const taskId = btn.getAttribute('data-id');
                    const taskTitle = btn.getAttribute('data-title');
                    editId.value = taskId;
                    editTitle.value = taskTitle;
                    editModal.classList.remove('hidden');
                });
            });

            const closeModal = () => editModal.classList.add('hidden');
            cancelBtn.addEventListener('click', closeModal);

            window.addEventListener('click', function (event) {
                if (event.target === editModal) closeModal();
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>