<?php
// admin/manage-users.php
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

check_login();
check_admin();

// Handle POST request to update a user's role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id_to_update = $_POST['user_id'];
    $new_role = $_POST['role'];
    // Basic validation: role can be 'admin' or 'customer'
    if ($new_role === 'admin' || $new_role === 'customer') {
        $sql = "UPDATE users SET role = :role WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['role' => $new_role, 'user_id' => $user_id_to_update]);
    }
    header("Location: " . BASE_URL . "/admin/manage-users.php");
    exit;
}

// Handle GET request to delete a user
if (isset($_GET['delete'])) {
    $user_id_to_delete = $_GET['delete'];
    // CRITICAL: Prevent admin from deleting themselves!
    if ($user_id_to_delete != $_SESSION['user_id']) {
        $sql = "DELETE FROM users WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id_to_delete]);
    }
    header("Location: " . BASE_URL . "/admin/manage-users.php");
    exit;
}

// Fetch all users to display
$users = $pdo->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY user_id ASC")->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT_PATH . '/includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h3>Admin Menu</h3>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/admin/index.php">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/products.php">Manage Products</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/boxes.php">Manage Boxes</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage-users.php" class="active">Manage Users</a></li>
            </ul>
        </nav>
    </aside>
    <main class="admin-content">
        <h2>Manage Users</h2>
        <p>View all registered users and manage their roles.</p>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <form action="<?php echo BASE_URL; ?>/admin/manage-users.php" method="post" style="margin: 0;">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <select name="role" onchange="this.form.submit()">
                                <option value="customer" <?php if ($user['role'] === 'customer') echo 'selected'; ?>>Customer</option>
                                <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                            <noscript><button type="submit" name="update_role">Update</button></noscript>
                        </form>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php // Safety check: Do not show delete button for the currently logged-in admin ?>
                        <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                            <a href="<?php echo BASE_URL; ?>/admin/manage-users.php?delete=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" style="color: #c62828;">Delete</a>
                        <?php else: ?>
                            (You)
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

</body>
</html>