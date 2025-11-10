<?php 
session_start();

include 'config/db.php';

$students = [];
try {
    $result = $conn->query("SELECT * FROM students");
    $students = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading records: " . $e->getMessage();
    $popupType = "error";
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Student Record Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="background-overlay"></div>
<h2>List of Students</h2>
<?php if (count($students) > 0): ?>
<table>
    <tr>
        <th>ID</th>
        <th>Student_Number</th>
        <th>Fullname</th>
        <th>Branch</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Date Added</th>
        <th>Action</th>
    </tr>
    <?php foreach ($students as $row): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['student_no']); ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo htmlspecialchars($row['branch']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo $row['contact']; ?></td>
        <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                <td>
            <a href="update.php?id=<?php echo $row['id']; ?>" class="btnn edit-btn">Edit</a>
                <a href="delete.php" class="btnn delete-btn">Delete</a>
            </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p style="text-align:center; color:white;">No student records found.</p>
<?php endif; ?>
        <a href="index.php" class="btn">Go back to Homepage</a>
</body>
</html>