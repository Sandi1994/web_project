<?php
require_once "../auth.php";
eventManagerOnly(); // Make sure this function exists in auth.php

require_once '../../config.php'; // Database connection

// Redirect if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'Event Manager') {
    header("Location: ../login.php");
    exit();
}

$current_user = $_SESSION['username'];

// Fetch all users with roles
$sql = "SELECT e.Id, e.Code, e.Title, e.Type_Id, e.Venue, e.City, e.Date, e.Start_Time, e.End_Time, e.Status_Id, s.Status_Type, t.Type
        FROM event e
        LEFT JOIN event_type t ON e.Type_Id = t.Type_Id
        LEFT JOIN event_status s ON e.Status_Id = s.Status_Id";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch roles for dropdown
$eventtype_sql = "SELECT * FROM event_type";
$eventtype_result = $conn->query($eventtype_sql);
$eventtype = array();     
while ($row = $eventtype_result->fetch_assoc()) {
    $eventtype[$row['Type_Id']] = $row['Type'];
}

$eventstatus_sql = "SELECT * FROM event_status";
$eventstatus_result = $conn->query($eventstatus_sql);
$eventstatus = array();     
while ($row = $eventstatus_result->fetch_assoc()) {
    $eventstatus[$row['Status_Id']] = $row['Status_Type'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events Panel</title>
<link rel="stylesheet" href="../../css/style.css">
</head>

<body class="page-layout">

<header class="header-section">
    <nav class="nav-left">
        <a href="../home.php" class="nav-link active">
            <span class="icon">&#8962;</span> Home
        </a>
    </nav>

    <div class="nav-center">
        <h2>EVENTS </h2>
    </div>

    <div class="nav-right">
        <span class="user-display">
            <span class="icon">&#128100;</span> Welcome, <strong><?php echo htmlspecialchars($current_user); ?></strong>
        </span>
        <span class="nav-divider">|</span>
        <a href="logout.php" class="logout-link">
            Logout <span class="icon">&#10150;</span>
        </a>
    </div>
</header>

 <!-- Create Event Button & EventType and EventStatus Buttons -->
<div class="action-section">
    <div class="button-wrapper">
        <button class="reg-btn" onclick="location.href='createEventType.php'">Create New Event Type</button>
        <button class="reg-btn" onclick="location.href='createEventStatus.php'">Create New Event Status</button>
        <button class="reg-btn" onclick="location.href='createEvent.php'">Create New Event</button>        
    </div>
</div>


    <!-- Table View -->
<div class="table-section">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Venue</th>
                    <th>Date</th>
                    <th>StartTime</th>
                    <th>EndTime</th>
                    <th>StatusType</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    <?php while ($event = $result->fetch_assoc()): ?>
                        <?php
                           // Ensure Type_Id and Status_Id are always defined
                            $event_type_id = isset($event['Type_Id']) ? intval($event['Type_Id']) : 0;
                            $event_status_id = isset($event['Status_Id']) ? intval($event['Status_Id']) : 0;
                        ?>
                <tr class="cell-record-row">
                <td><?php echo isset($event['Code']) ? htmlspecialchars($event['Code']) : ''; ?></td>
                <td><?php echo isset($event['Title']) ? htmlspecialchars($event['Title']) : ''; ?></td>
                <td><?php echo isset($event['Type']) ? htmlspecialchars($event['Type']) : ''; ?></td>
                <td><?php echo isset($event['Venue']) ? htmlspecialchars($event['Venue']) : ''; ?></td>
                <td><?php echo isset($event['Date']) ? htmlspecialchars($event['Date']) : ''; ?></td>
                <td><?php echo isset($event['Start_Time']) ? htmlspecialchars($event['Start_Time']) : ''; ?></td>
                <td><?php echo isset($event['End_Time']) ? htmlspecialchars($event['End_Time']) : ''; ?></td>
                <td><?php echo isset($event['Status_Type']) ? htmlspecialchars($event['Status_Type']) : ''; ?></td>
                
                            <td>
                                <button class="view-btn" onclick="location.href='viewEvent.php?Id=<?php echo $event['Id']; ?>'">View</button>
                                <button class="upt-btn" onclick="location.href='updateEvent.php?Id=<?php echo $event['Id']; ?>'">Update</button>
                                <button class="del-btn" onclick="if(confirm('Are you sure you want to delete this Event?')) location.href='deleteEvent.php?id=<?php echo $event['Id']; ?>'">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
