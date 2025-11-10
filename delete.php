<?php 
session_start();
include 'config/db.php';

if (isset($_POST['delete'])) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->execute([$_POST['id']]);
    $_SESSION['message'] = "<p>Student deleted successfully! Redirecting to Student List</p> 
    <img src='https://media1.tenor.com/m/m_3TrKUGTkYAAAAd/czn-chaos-zero-nightmare.gif' alt='GIF' width='220'>";
    $_SESSION['popupType'] = "deleted";
    $_SESSION['showPopup'] = true;
    header("Location: delete.php");
    exit();
}

$students = [];
try {
    $result = $conn->query("SELECT * FROM students");
    $students = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading records: " . $e->getMessage();
    $popupType = "error";
}

$showPopup = $_SESSION['showPopup'] ?? false;
$message = $_SESSION['message'] ?? "";
$popupType = $_SESSION['popupType'] ?? "";
unset($_SESSION['showPopup'], $_SESSION['message'], $_SESSION['popupType']);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Delete Students</title>
    <link rel="stylesheet" href="css/delete.css">
</head>
<body>
<div class="background-overlay"></div>
<h2>Delete Students?</h2>
<?php if (count($students) > 0): ?>
    
<table>
    <tr>
        <th>ID</th>
        <th>Student Number</th>
        <th>Fullname</th>
        <th>Branch</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Date Added</th>
        <th style="color:red; font-weight:bold;">Delete</th>
    </tr>
    <?php foreach ($students as $row): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['student_no']); ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo htmlspecialchars($row['branch']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['contact']); ?></td>
        <td><?php echo htmlspecialchars($row['date_added']); ?></td>
        <td>
            <form id="deleteForm_<?php echo $row['id']; ?>" method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="delete" value="1">
                <button type="button" onclick="openFirstPopup(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['fullname']); ?>', '<?php echo htmlspecialchars($row['student_no']); ?>')">Delete</button>
            </form>
            <a href="read.php" class="btnn delete-btn">Cancel</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No student records found.</p>
<?php endif; ?>

<div class="popup-overlay" id="firstPopup">
  <div class="popup-box">
    <h3>Do you wish to delete this student?</h3>
    <p id="studentInfo" style="font-weight:bold; color:#007bff; margin-bottom:10px;"></p>
    <img src='https://media.tenor.com/s-hLpC547CAAAAAi/czn-chaos-zero-nightmare.gif' alt='GIF' width='220'>
    <button class="confirm" id="yesBtn">Yes</button>
    <button class="cancel" onclick="closePopup('firstPopup')">No</button>
  </div>
</div>

<div class="popup-overlay" id="secondPopup">
  <div class="popup-box">
    <h3>⚠️ Warning ⚠️</h3>
    <p>Once the action is confirmed, it cannot be undone.<br>Do you want to proceed?</p>
    <button class="confirm" id="confirmBtn">Confirm</button>
    <button class="cancel" onclick="closePopup('secondPopup')">Cancel</button>
  </div>
</div>

<div class="overlay" id="popupOverlay">
    <div class="popup <?php echo $popupType; ?>">
        <?php echo $message; ?>
    </div>
</div>
<a href="index.php" class="btn">Go back to Homepage</a>
<script>
let selectedId = null;

function openFirstPopup(id, fullname, studentNo) {
    selectedId = id;
    const info = document.getElementById('studentInfo');
    info.textContent = `(${studentNo}) ${fullname}`;
    document.getElementById('firstPopup').style.display = 'flex';
}

document.getElementById('yesBtn').onclick = function() {
    document.getElementById('firstPopup').style.display = 'none';
    document.getElementById('secondPopup').style.display = 'flex';
}

document.getElementById('confirmBtn').onclick = function() {
    if (selectedId) {
        document.getElementById('deleteForm_' + selectedId).submit();
    }
}

function closePopup(id) {
    document.getElementById(id).style.display = 'none';
}

<?php if ($showPopup): ?>
document.getElementById("popupOverlay").style.display = "flex";
setTimeout(function() {
    window.location.href = "read.php";
}, 5000);
<?php endif; ?>
</script>

</body>
</html>
