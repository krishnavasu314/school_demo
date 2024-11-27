<?php
$conn = new mysqli('localhost', 'root', '', 'school_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

// Fetch student details with class name
$sql = "SELECT student.*, classes.name AS class_name 
        FROM student 
        LEFT JOIN classes ON student.class_id = classes.class_id 
        WHERE student.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>View Student</h1>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($student['name']) ?></h4>
            <p>Email: <?= htmlspecialchars($student['email']) ?></p>
            <p>Address: <?= htmlspecialchars($student['address']) ?></p>
            <p>Class: <?= htmlspecialchars($student['class_name']) ?></p>
            <p>Created At: <?= htmlspecialchars($student['created_at']) ?></p>
            <img src="uploads/<?= htmlspecialchars($student['image']) ?>" alt="Student Image" width="150">
            <br><br>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
</body>
</html>
