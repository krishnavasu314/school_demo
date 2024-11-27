<?php
$conn = new mysqli('localhost', 'root', '', 'school_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

// Fetch student details
$studentQuery = "SELECT * FROM student WHERE id = ?";
$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$studentResult = $stmt->get_result();
$student = $studentResult->fetch_assoc();

if (!$student) {
    die("Student not found!");
}

// Fetch classes for dropdown
$classQuery = "SELECT * FROM classes";
$classResult = $conn->query($classQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $class_id = $_POST['class_id'];

    // Handle file upload
    $image = $student['image']; // Keep current image by default
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate image
        $allowed = ['jpg', 'png'];
        if (!in_array($imageFileType, $allowed)) {
            die("Only JPG and PNG files are allowed.");
        }

        // Move uploaded file
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    // Update student details
    $updateQuery = "UPDATE student SET name = ?, email = ?, address = ?, class_id = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssisi", $name, $email, $address, $class_id, $image, $id);
    $stmt->execute();
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Student</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($student['address']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Class</label>
            <select name="class_id" class="form-control">
                <?php while ($class = $classResult->fetch_assoc()): ?>
                    <option value="<?= $class['class_id'] ?>" <?= $class['class_id'] == $student['class_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
            <small>Current image: <?= htmlspecialchars($student['image']) ?></small>
        </div>
        <button type="submit" class="btn btn-success">Update Student</button>
    </form>
</div>
</body>
</html>
