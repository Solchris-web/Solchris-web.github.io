<?php
// login.php - Unified Authentication
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    $user = current_user();
    switch ($user['role']) {
        case 'admin': header('Location: /admin/dashboard.php'); exit;
        case 'receptionist': header('Location: /receptionist/dashboard.php'); exit;
        case 'barber': header('Location: /barber/dashboard.php'); exit;
        case 'customer': default: header('Location: /customer/dashboard.php'); exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash_message('error', 'Please enter both email address and password.');
    } else {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
                set_flash_message('error', 'This account has been deactivated. Please contact the administrator.');
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];

                set_flash_message('success', 'Welcome back, ' . $user['full_name'] . '!');

                switch ($user['role']) {
                    case 'admin':
                        header('Location: /admin/dashboard.php');
                        break;
                    case 'receptionist':
                        header('Location: /receptionist/dashboard.php');
                        break;
                    case 'barber':
                        header('Location: /barber/dashboard.php');
                        break;
                    case 'customer':
                    default:
                        header('Location: /customer/dashboard.php');
                        break;
                }
                exit;
            }
        } else {
            set_flash_message('error', 'Invalid email address or password credentials.');
        }
    }
}

$page_title = "Log In";
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container-sm">
    <div class="card">
        <div class="card-header text-center">
            <div>
                <span class="eyebrow-label">Portal Access</span>
                <h1 class="card-title card-title-lg">Log In to Noli at Fili</h1>
            </div>
        </div>

        <form action="/login.php" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="e.g. customer@noliatfili.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Enter password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>

        <div class="auth-footer">
            Don't have an account yet? <a href="/register.php" class="auth-link">Register Here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
