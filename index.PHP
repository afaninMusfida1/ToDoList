<?php
include 'config.php';

// Mengambil semua tugas
$result = $conn->query("SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8em;
            color: #333;
        }
        form {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        form input[type="text"] {
            flex: 1;
            padding: 15px;
            font-size: 16px;
            border: none;
            outline: none;
        }
        form button {
            padding: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        form button:hover {
            background-color: #0056b3;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        li {
            background-color: #f9f9f9;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background-color 0.3s;
        }
        li:hover {
            background-color: #f1f1f1;
        }
        li a {
            color: #f44336;
            text-decoration: none;
            font-size: 1.2em;
            transition: color 0.3s;
        }
        li a:hover {
            color: #d32f2f;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            form input[type="text"], form button {
                padding: 12px;
                font-size: 14px;
            }
            li {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>

        <form method="post" action="config.php">
            <input type="text" name="title" placeholder="Tambahkan tugas baru" required>
            <button type="submit"><i class="fas fa-plus"></i></button>
        </form>

        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($row['title']); ?>
                    <a href="config.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>

<?php
$conn->close();
?>
