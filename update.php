<?php 
session_start();
include 'config/db.php';

$message = $_SESSION['message'] ?? "";
$popupType = $_SESSION['popupType'] ?? "";
$showPopup = !empty($message);
unset($_SESSION['message'], $_SESSION['popupType']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student not found!");
    }
} else {
    die("No student ID provided!");
}

if (isset($_POST['update'])) {
    $id = $_POST['update_id'];
    $student_no = $_POST['student_number'];
    $fullname = $_POST['fullname'];
    $branch = $_POST['branch'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("UPDATE students SET 
        student_no = :student_no,
        fullname = :fullname,
        branch = :branch,
        email = :email,
        contact = :contact
        WHERE id = :id
    ");
    $stmt->execute([
        ':student_no' => $student_no,
        ':fullname' => $fullname,
        ':branch' => $branch,
        ':email' => $email,
        ':contact' => $contact,
        ':id' => $id
    ]);

    $_SESSION['message'] = "<p>Student updated successfully!</p> <img src='https://media.tenor.com/CJgtOQiQif4AAAAi/renoa-chaos-zero-nightmare.gif'alt='GIF' width='220'>";
    $_SESSION['popupType'] = "success";
    $showPopup = true;
    header("Location: update.php?id=" . $id);
    exit();
}
    
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Update Student</title>
    <link rel="stylesheet" href="css/update.css">
</head>
<body>
<div class="background-overlay"></div>

<h2>Update Student Information</h2>

<form method="POST">
    <input type="hidden" name="update_id" value="<?php echo $student['id']; ?>">

    <table>
        <tr>
            <th>Student Number</th>
            <td><input type="text" name="student_number" value="<?php echo htmlspecialchars($student['student_no']); ?>"></td>
        </tr>
        <tr>
            <th>Fullname</th>
            <td><input type="text" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>"></td>
        </tr>
        <tr>
            <th>Branch</th>
            <td>
                <select name="branch">
                    <option value="Quezon City" <?php if($student['branch'] == 'Quezon City') echo 'selected'; ?>>Quezon City</option>
                    <option value="North Manila" <?php if($student['branch'] == 'North Manila') echo 'selected'; ?>>North Manila</option>
                    <option value="Antipolo" <?php if($student['branch'] == 'Antipolo') echo 'selected'; ?>>Antipolo</option>
                    <option value="Binalonan" <?php if($student['branch'] == 'Binalonan') echo 'selected'; ?>>Binalonan</option>
                    <option value="Guimba" <?php if($student['branch'] == 'Guimba') echo 'selected'; ?>>Guimba</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"></td>
        </tr>
        <tr>
            <th>Contact</th>
            <td><input type="text" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>" maxlength="12" pattern="^\d{11,12}$" title="Please enter 11 or 12 digits only"></td>
        </tr>
        <tr>
            <th>Date Added</th>
            <td><?php echo htmlspecialchars($student['date_added']); ?></td>
        </tr>
    </table>

    <br>
    <button type="submit" class="update-btn" name="update">Update</button>
    <a href="read.php" class="btnn cancel-btn">Cancel</a>
</form>
<a href="index.php" class="btn">Go back to Homepage</a>
<div class="overlay" id="popupOverlay">
    <div class="popup <?php echo $popupType; ?>">
        <?php echo $message; ?>
    </div>
</div>
<script>
<?php if ($showPopup): ?>
document.getElementById("popupOverlay").style.display = "flex";

setTimeout(function() {
    window.location.href = "read.php";
}, 3000);
<?php endif; ?>

function closePopup() {
    document.getElementById("popupOverlay").style.display = "none";
    window.location.href = "read.php";
}
</script>
</body>
</html>
