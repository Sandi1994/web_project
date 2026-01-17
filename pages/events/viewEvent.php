<?php
require_once '../../config.php'; 
require_once '../auth.php'; 
eventManagerOnly();

if (isset($_GET['Id'])) {
    $event_id = mysqli_real_escape_string($conn, $_GET['Id']);
    $query = "SELECT e.*, t.Type, s.Status_Type FROM event e LEFT JOIN event_type t ON e.Type_Id = t.Type_Id LEFT JOIN event_status s ON e.Status_Id = s.Status_Id WHERE e.Id = '$event_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
    } else {
        echo "Event not found.";
        exit;
    }
} else {
    echo "No Event ID provided.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Event Details</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="create-user-page">

    <header class="header-section">
        <nav class="nav-left">
            <a href="events.php" class="nav-link"><span class="icon">&#8592;</span> BACK TO LIST</a>
        </nav>
        <div class="nav-center"><h2>EVENT DETAILS</h2></div>
        <div class="nav-right">
             <span class="user-display"><span class="icon">&#128100;</span>Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
        </div>
    </header>

    <main class="form-wrapper" style="padding-top: 20px;">
        <div class="glass-card">
            <div class="view-group" style="width: 100%; max-width: 480px;">
                
                <div class="input-group">
                    <label>Event ID</label>
                    <div class="view-data"><?php echo $event['Id']; ?></div>
                </div>

                <div class="input-group">
                    <label>Code</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['Code']); ?></div>
                </div>

                <div class="input-group">
                    <label>Title</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['Title']); ?></div>
                </div>
                
                <div class="input-group">
                    <label>Venue</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['Venue']); ?></div>
                </div>
                
                <div class="input-group">
                    <label>City</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['City']); ?></div>
                </div>

                
                <div class="input-group">
                    <label>Date</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['Date']); ?></div>
                </div>

                
                <div class="input-group">
                    <label>Start Time</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['Start_Time']); ?></div>
                </div>

                <div class="input-group">
                    <label>End Time</label>
                    <div class="view-data"><?php echo htmlspecialchars($event['End_Time']); ?></div>
                </div>
                
               <div class="input-group">
                    <label>Assign Event Type</label>
                    <div class="view-data"><?php echo htmlspecialchars(isset($event['Type']) ? $event['Type'] : 'No Event Type Assigned'); ?></div>
                </div>

                <div class="input-group">
                    <label>Assign Event Status</label>
                    <div class="view-data"><?php echo htmlspecialchars(isset($event['Status_Type']) ? $event['Status_Type'] : 'No Event Status Assigned'); ?></div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <div class="view-actions">
                        <button class="submit-btn edit-btn" onclick="location.href='updateEvent.php'">Edit Event</button>
                        <button class="submit-btn close-btn" onclick="location.href='events.php'" >Close</button>
                    </div>

                    
                </div>

            </div>
        </div>
    </main>
</body>
</html>
