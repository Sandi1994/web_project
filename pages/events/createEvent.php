<?php
// 1. Load the database connection first
require_once '../../config.php'; 

// 2. Load auth.php
require_once '../auth.php'; 
eventManagerOnly();

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code      = mysqli_real_escape_string($conn, $_POST['code']);
    $title     = mysqli_real_escape_string($conn, $_POST['title']);
    $type_id   = mysqli_real_escape_string($conn, $_POST['Type_Id']);
    $venue     = mysqli_real_escape_string($conn, $_POST['venue']);
    $city      = mysqli_real_escape_string($conn, $_POST['city']);
    $date      = mysqli_real_escape_string($conn, $_POST['date']);
    $starttime = mysqli_real_escape_string($conn, $_POST['starttime']);
    $endtime   = mysqli_real_escape_string($conn, $_POST['endtime']);
    $status_id = mysqli_real_escape_string($conn, $_POST['Status_Id']);


    // Insert query (MATCHES YOUR TABLE)
    $sql = "INSERT INTO event 
        (Code, Title, Type_Id, Venue, City, Date, Start_Time, End_Time, Status_Id)
        VALUES
        ('$code', '$title', '$type_id', '$venue', '$city', '$date', '$starttime', '$endtime', '$status_id')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Event created successfully'); window.location='events.php';</script>";
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}

// Fetch event types for the dropdown using db
$etype_query = "SELECT Type_Id,Type FROM event_type ORDER BY Type ASC";
$etype_result = mysqli_query($conn, $etype_query);

$etype_list = array();
if ($etype_result && mysqli_num_rows($etype_result) > 0) {
    while ($row = mysqli_fetch_assoc($etype_result)) {
        $etype_list[] = $row;
    }
}

// Fetch event statuses for the dropdown using db
$estatus_query = "SELECT Status_Id, Status_Type FROM event_status ORDER BY Status_Type ASC";
$estatus_result = mysqli_query($conn, $estatus_query);

$estatus_list = array();
if ($estatus_result && mysqli_num_rows($estatus_result) > 0) {
    while ($row = mysqli_fetch_assoc($estatus_result)) {
        $estatus_list[] = $row;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Event</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="create-user-page">

    <header class="header-section">
        <nav class="nav-left">
            <a href="events.php" class="nav-link">
                <span class="icon">&#8592;</span> BACK TO LIST
            </a>
        </nav>
        <div class="nav-center"><h2>CREATE NEW EVENT</h2></div>
        <div class="nav-right">
            <span class="user-display"><span class="icon">&#128100;</span> Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
        </div>
    </header>

    <main class="form-wrapper">
        <div class="glass-card">
            <form action="" method="POST">
                <div class="input-group">
                    <label>Code</label>
                    <input type="text" name="code" id="code" placeholder="Enter Event Code" required>
                </div>

                <div class="input-group">
                    <label>Title</label>
                    <input type="text" name="title" id="title" placeholder="Enter Event Title" required>
                </div>

                <div class="input-group">
                    <label>Venue</label>
                    <input type="text" name="venue" id="venue" placeholder="Enter Event Venue" required>
                </div>

                <div class="input-group">
                    <label>City</label>
                    <input type="text" name="city" id="city" placeholder="Enter Event City" required>
                </div>

                <div class="input-group">
                    <label>Date</label>
                    <input type="date" name="date" id="date" placeholder="Enter Event Date" required>
                </div>

                <div class="input-group">
                    <label>Start Time</label>
                    <input type="time" name="starttime" id="starttime" placeholder="Enter Event Start Time" required>
                </div>

                <div class="input-group">
                    <label>End Time</label>
                    <input type="time" name="endtime" id="endtime" placeholder="Enter Event End Time" required>
                </div>

                <div class="input-group">
                    <label>Assign Event Type</label>
                    <select name="Type_Id" class="drop-down-active" required>
                        <option value="" disabled selected>-- Select a Event Type --</option>
                        <?php 
                        if (!empty($etype_list)) {
                            foreach ($etype_list as $type) {
                                echo '<option value="' . $type['Type_Id'] . '">' . htmlspecialchars($type['Type']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Assign Event Staus</label>
                    <select name="Status_Id" class="drop-down-active" required>
                        <option value="" disabled selected>-- Select a Event Status --</option>
                        <?php 
                        if (!empty($estatus_list)) {
                            foreach ($estatus_list as $status) {
                                echo '<option value="' . $status['Status_Id'] . '">' . htmlspecialchars($status['Status_Type']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="button-row">
                    <button type="submit" class="submit-btn">Create an Event</button>
                    <button type="button" class="submit-btn close-btn" onclick="location.href='events.php'" >Close</button>
                </div>
            </form>
        </div>
    </main>


    <script>

<script>
document.querySelector('form').onsubmit = function(e) {
    let code = document.getElementById('code').value;

    if (code.length < 10) {
        alert("Event code must be exactly 10 characters");
        e.preventDefault();
    }
};
</script>

</script>

</body>
</html>