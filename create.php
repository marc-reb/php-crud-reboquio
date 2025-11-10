<?php 
session_start();

include 'config/db.php';

$message = $_SESSION['message'] ?? "";
$popupType = $_SESSION['popupType'] ?? "";
$showPopup = !empty($message);
unset($_SESSION['message'], $_SESSION['popupType']);

if (isset($_POST["submit"])) {
    $student_no = $_POST["student_no"];
    $fullname = $_POST["fullname"];
    $branch = $_POST["branch"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];

    if (empty($student_no) || empty($fullname) || empty($branch) || empty($email) || empty($contact)) {
        $_SESSION['message'] = "Please fill in all fields.";
        $_SESSION['popupType'] = "error";
        $showPopup = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

    } elseif (!preg_match('/^\d{11,12}$/', $contact)) {
        $_SESSION['message'] = "
        <p>Contact must be a number and must be 11 or 12 digits only!</p>
        <img src='https://media1.tenor.com/m/HniICDNOAakAAAAd/chaos-zero-nightmare-chaos-zero-nightmare-magna.gif'alt='GIF' width='220'>";
        $_SESSION['popupType'] = "error";
        $showPopup = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
         try {
            $sql = "INSERT INTO students (student_no, fullname, branch, email, contact)
                    VALUES (:student_no, :fullname, :branch, :email, :contact)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':student_no', $student_no);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':branch', $branch);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact', $contact);
            $stmt->execute();

            $_SESSION['message'] = "<h3>Student added successfully!</h3>
            <img src='https://media.tenor.com/4U6JdB3MKN0AAAAi/rin-chaos-zero-nightmare.gif'
                alt='IMG' width='250'>";
            $_SESSION['popupType'] = "success";
            header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            $_SESSION['popupType'] = "error";
            $showPopup = true;
            header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        }
    }
}

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
    <title>Student Insert Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="background-overlay"></div>
<h2>Student Insert</h2>
<div class="form-container">
    <form method="POST">
        <label>Student_No:</label>
        <input type="text" name="student_no">

        <label>Fullname:</label>
        <input type="text" name="fullname">

        <label>Branch:</label>
        <select name="branch" class="drop-down">
            <option value="">Select a Branch</option>
            <option value="Quezon City">Quezon City</option>
            <option value="North Manila">North Manila</option>
            <option value="Antipolo">Antipolo</option>
            <option value="Binalonan">Binalonan</option>
            <option value="Guimba">Guimba</option>
        </select>

        <label>Email:</label>
        <input type="text" name="email">

        <label>Contact:</label>
        <input type="text" name="contact">

        <button type="submit" name="submit">Add Student</button>
    </form>
</div>
<div class="overlay" id="popupOverlay">
    <div class="popup <?php echo $popupType; ?>">
        <?php echo $message; ?>
        <button class="close-btn" onclick="closePopup()">Close</button>
    </div>
</div>
<a href="index.php" class="btn">Go back to Homepage</a>
<script>
function closePopup() {
    document.getElementById("popupOverlay").style.display = "none";
}
<?php if ($showPopup): ?>
document.getElementById("popupOverlay").style.display = "flex";
<?php endif; ?>
</script>
</body>
</html>
