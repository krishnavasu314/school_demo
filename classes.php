<?php
$conn = new mysqli('localhost', 'root', '', 'school_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to add a new class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $class_name = $_POST['class_name'];
    if (!empty($class_name)) {
        $stmt = $conn->prepare("INSERT INTO classes (name) VALUES (?)");
        $stmt->bind_param("s", $class_name);
        $stmt->execute();
        header("Location: classes.php");
    }
}

// Handle delete class
if (isset($_GET['delete_id'])) {
    $class_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    header("Location: classes.php");
}

// Handle edit class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_class'])) {
    $class_id = $_POST['class_id'];
    $class_name = $_POST['class_name'];
    if (!empty($class_name)) {
        $stmt = $conn->prepare("UPDATE classes SET name = ? WHERE class_id = ?");
        $stmt->bind_param("si", $class_name, $class_id);
        $stmt->execute();
        header("Location: classes.php");
    }
}

// Fetch all classes
$classQuery = "SELECT * FROM classes";
$classes = $conn->query($classQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Manage Classes</h1>

    <!-- Form to add a new class -->
    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="class_name">New Class Name</label>
            <input type="text" name="class_name" id="class_name" class="form-control" placeholder="Enter class name" required>
        </div>
        <button type="submit" name="add_class" class="btn btn-success">Add Class</button>
    </form>

    <!-- List of classes -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($class = $classes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($class['class_id']) ?></td>
                    <td><?= htmlspecialchars($class['name']) ?></td>
                    <td>
                        <button 
                            class="btn btn-warning btn-sm" 
                            data-toggle="modal" 
                            data-target="#editModal<?= $class['class_id'] ?>">Edit</button>
                        <a href="classes.php?delete_id=<?= $class['class_id'] ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $class['class_id'] ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Class</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                                    <div class="form-group">
                                        <label for="class_name">Class Name</label>
                                        <input type="text" name="class_name" id="class_name" class="form-control" 
                                               value="<?= htmlspecialchars($class['name']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit_class" class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Edit Modal -->
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
