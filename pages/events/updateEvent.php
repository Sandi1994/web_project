<?php
require_once '../../config.php'; 
require_once '../auth.php'; 
eventManagerOnly();

// 1. GET THE ID FROM URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;
if (!$id) { header("Location: ../updateEvent.php"); exit(); }

// 2. FETCH CURRENT USER DATA + ROLE NAME
$query = "SELECT u.*, r.role_name AS current_role_name 
          FROM users u 
          LEFT JOIN roles r ON u.role_id = r.role_id 
          WHERE u.id = '$id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// --- GLOBAL VARIABLES (FIXES UNDEFINED WARNINGS) ---
$orig_username = isset($user['username']) ? trim($user['username']) : '';
$orig_role_id  = isset($user['role_id']) ? (int)$user['role_id'] : 0;
$orig_role_name = isset($user['current_role_name']) ? $user['current_role_name'] : "No Role";

// 3. FETCH ALL ROLES FOR DROPDOWN
$roles_list = array();
$roles_query = mysqli_query($conn, "SELECT * FROM roles");
while($row = mysqli_fetch_assoc($roles_query)) {
    $roles_list[(int)$row['role_id']] = $row['role_name'];
}

// 4. HANDLE FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_user = isset($_POST['username']) ? trim(mysqli_real_escape_string($conn, $_POST['username'])) : '';
    $new_role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
    $new_pass = $_POST['password']; 

    $is_user_changed = ($new_user !== $orig_username);
    $is_role_changed = ($new_role_id !== $orig_role_id);
    $is_pass_changed = (!empty($new_pass));

    // --- CHARACTER VALIDATIONS ONLY ---
    // Validate username length only if they provided one
    if (!empty($new_user) && strlen($new_user) < 4) {
        $error = "Username must be at least 4 characters long.";
    } 
    // Validate password length only if they are trying to change it
    elseif ($is_pass_changed && strlen($new_pass) < 8) {
        $error = "New password must be at least 8 characters long.";
    } 
    // Check if anything actually changed
    elseif (!$is_user_changed && !$is_role_changed && !$is_pass_changed) {
        $error = "No changes detected.";
    } 
    else {
        $change_log = "Changes made:\\n";
        
        if ($is_user_changed) {
            $change_log .= "- Username: $orig_username -> $new_user\\n";
            $has_changes = true;
        }
        
        if ($is_role_changed) {
            $new_role_name = isset($roles_list[$new_role_id]) ? $roles_list[$new_role_id] : "Unknown";
            $change_log .= "- Role: $orig_role_name -> $new_role_name\\n";
            $has_changes = true;
        }

        if ($is_pass_changed) {
            // HASH THE PASSWORD BEFORE SAVING
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $change_log .= "- Password has been updated (Hashed)\\n";
            $has_changes = true;
            
            $sql = "UPDATE users SET username='$new_user', password='$hashed_pass', role_id='$new_role_id' WHERE id='$id'";
        } else {
            $sql = "UPDATE users SET username='$new_user', role_id='$new_role_id' WHERE id='$id'";
        }
        // Final check before sending the log to the alert
        if (!$has_changes) { $change_log = "No data was modified."; }


        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('User Updated Successfully!\\n\\n$change_log'); window.location.href='../admin.php';</script>";
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="create-user-page">

    <header class="header-section">
        <nav class="nav-left">
            <a href="admin.php" class="nav-link"><span class="icon">&#8592;</span> BACK TO LIST</a>
        </nav>
        <div class="nav-center"><h2>USER DETAILS</h2></div>
        <div class="nav-right">
             <span class="user-display"><span class="icon">&#128100;</span>Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
        </div>
    </header>

<main class="form-wrapper">
    <div class="glass-card">
        <form action="" method="POST" id="updateForm">
            
            <?php if(isset($error)): ?>
                <div style="background: rgba(255,0,0,0.2); color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid red;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" id="username" 
                       placeholder="Enter username (min 4 characters)" 
                       value="<?php echo htmlspecialchars($orig_username); ?>">
            </div>

            <div class="input-group">
                <label>New Password (Optional)</label>
                <input type="password" name="password" id="password" 
                       placeholder="Leave blank to keep current (min 8 characters)">
            </div>

            <div class="input-group">
                <label>Confirm New Password</label>
                <input type="password" id="confirm_password" 
                       placeholder="Repeat new password">
            </div>

<div class="input-group">
    <label>Role</label>
    <select name="role_id" style="width: 100%; padding: 12px; border-radius: 25px; background: white; color: black;">
        <?php 
        // 1. Get the current user's role ID safely
        $current_role = isset($user['role_id']) ? (int)$user['role_id'] : (isset($user['Role_Id']) ? (int)$user['Role_Id'] : 0);

        // 2. Loop through the roles list
        foreach ($roles_list as $r_id => $r_name): 
            // Use == instead of === to avoid strict type issues between string/int
            $selected = ($current_role == $r_id) ? 'selected="selected"' : '';
        ?>
            <option value="<?php echo htmlspecialchars($r_id); ?>" <?php echo $selected; ?>>
                <?php echo htmlspecialchars($r_name); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

            <div class="view-actions">
                <button type="submit" class="submit-btn edit-btn">Save Changes</button>
                <button type="button" class="submit-btn close-btn" onclick="location.href='admin.php'">Close</button>
            </div>
        </form>
    </div>
</main>

<script>
document.getElementById('updateForm').onsubmit = function(e) {
    const username = document.getElementById('username').value;
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;

    // 1. Username length validation (only if not empty)
    if (username.length > 0 && username.length < 4) {
        alert("Username must be at least 4 characters long.");
        e.preventDefault();
        return false;
    }

    // 2. Password length validation (only if not empty)
    if (pass.length > 0) {
        if (pass.length < 8) {
            alert("Password must be at least 8 characters long.");
            e.preventDefault();
            return false;
        }
        // 3. Confirm password match
        if (pass !== confirm) {
            alert("Passwords do not match!");
            e.preventDefault();
            return false;
        }
    }
};
</script>
</body>
</html>