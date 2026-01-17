<?php
require_once "../auth.php";
adminOnly();

require_once '../../config.php'; // your DB connection file

// Redirect if not logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../../login.php");
    exit();
}

$current_user = $_SESSION['username'];

// Fetch all users
$sql = "SELECT u.id, u.username, u.password, u.role_id, r.role_name 
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.role_id";
$result = $conn->query($sql);

// Fetch roles for dropdown
$role_sql = "SELECT * FROM roles";
$roles_result = $conn->query($role_sql);
$roles = array();     
while ($row = $roles_result->fetch_assoc()) {
    $roles[$row['role_id']] = $row['role_name'];
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
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
        <h2>USER REGISTRATION PAGE</h2>
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
    <!-- Registration Button & User Roles Button -->
<div class="action-section">
    <div class="button-wrapper">
        <button class="reg-btn" onclick="location.href='createRoles.php'">Create New User Roles</button>
        <button class="reg-btn" onclick="location.href='createUser.php'">Register New User</button>        
    </div>
</div>

    <!-- Table View -->
    <div class="table-section">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <!-- <th>Password</th> -->
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <?php
                        // Ensure role_id and role_name are always defined
                        $user_role_id = isset($user['role_id']) ? intval($user['role_id']) : 0;
                    ?>
                <tr class="cell-record-row">
                    <td><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?></td>
                
                <td>
                    <select class="drop-down" disabled onchange="updateRole(<?php echo $user['id']; ?>, this.value)">                    
                    <!-- Populate roles -->
                            <?php foreach ($roles as $id => $role_name): ?>
                            <?php $id = intval($id); // ensure integer ?>
                        <option value="<?php echo $id; ?>" 
                            <?php echo ($user_role_id == $id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role_name); ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                <button class="view-btn" onclick="location.href='viewUser.php?id=<?php echo $user['id']; ?>'">View</button>
                <button class="upt-btn" onclick="location.href='updateUser.php?id=<?php echo $user['id']; ?>'">Update</button>
                <button class="del-btn" onclick="location.href='deleteUser.php?id=<?php echo $user['id']; ?>'">Delete</button>
                </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


<!-- <script>
// Change role via AJAX
function updateRole(userId, roleId) {
    if(confirm('Change user role?')) {
        fetch('update_role.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id=' + userId + '&role_id=' + roleId
        })
        .then(res => res.text())
        .then(data => alert(data));
    }
}

function viewUser(userId) {
    window.location.href = 'view_user.php?id=' + userId;
}

function updateUser(userId) {
    window.location.href = 'update_user.php?id=' + userId;
}

function deleteUser(userId) {
    if(confirm('Are you sure you want to delete this user?')) {
        window.location.href = 'delete_user.php?id=' + userId;
    }
}
</script> -->

</body>
</html>
