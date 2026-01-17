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
$sql = "SELECT t.ticket_id, t.code, t.category_id, t.event_id, t.price, c.category_id, c.Name, e.Id, e.Code, e.Title, e.Type_Id, e.Venue, e.City, e.Date, e.Start_Time, e.End_Time, e.Status_Id
        FROM ticket t
        LEFT JOIN ticket_category c ON t.category_id = c.category_id
        LEFT JOIN event e ON t.event_id = e.Id";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
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
        <h2>TICKETS </h2>
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

 <!-- Create ticket and ticket category Buttons -->
<div class="action-section">
    <div class="button-wrapper">
        <button class="reg-btn" onclick="location.href='createTicketCategory.php'">Create New Ticket Category</button>
        <button class="reg-btn" onclick="location.href='createTicket.php'">Create New Ticket</button>        
    </div>
</div>


    <!-- Table View -->
<div class="table-section">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>TicketCategory</th>
                    <th>Event</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    <?php while ($ticket = $result->fetch_assoc()): ?>
                        <?php
                           // Ensure Type_Id and Status_Id are always defined
                            $ticket_categoryid = isset($ticket['category_id']) ? intval($ticket['category_id']) : 0;
                            $ticket_eventid = isset($ticket['event_id']) ? intval($ticket['event_id']) : 0;
                        ?>
                <tr class="cell-record-row">
                <td><?php echo isset($ticket['code']) ? htmlspecialchars($ticket['code']) : ''; ?></td>
                <td><?php echo isset($ticket['Name']) ? htmlspecialchars($ticket['Name']) : ''; ?></td>
                <td><?php echo isset($ticket['Title']) ? htmlspecialchars($ticket['Title']) : ''; ?></td>
                <td><?php echo isset($ticket['price']) ? htmlspecialchars($ticket['price']) : ''; ?></td>
               
                            <td>
                                <button class="view-btn" onclick="location.href='viewTicket.php?Id=<?php echo $ticket['Id']; ?>'">View</button>
                                <button class="upt-btn" onclick="location.href='updateTicket.php?Id=<?php echo $ticket['Id']; ?>'">Update</button>
                                <button class="del-btn" onclick="if(confirm('Are you sure you want to delete this Ticket?')) location.href='deleteTicket.php?Id=<?php echo $ticket['Id']; ?>'">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
