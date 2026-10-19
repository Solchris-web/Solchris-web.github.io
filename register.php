<?php
// register.php - Customer Self-Registration
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: /customer/dashboard.php');
    exit;
}

$pdo = get_db_connection();
$barbers = $pdo->query("SELECT u.id, u.full_name FROM users u JOIN barber_profiles bp ON u.id = bp.user_id WHERE u.role = 'barber' AND bp.status = 'Active' AND u.is_active = 1")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $preferred_barber_id = !empty($_POST['preferred_barber_id']) ? intval($_POST['preferred_barber_id']) : null;
    $preferred_style = sanitize_input($_POST['preferred_style'] ?? '');

    if (empty($full_name) || empty($email) || empty($password) || empty($phone)) {
        set_flash_message('error', 'Please fill in all required fields (Full Name, Email, Phone, Password).');
    } elseif ($password !== $confirm_password) {
        set_flash_message('error', 'Password and confirmation password do not match.');
    } elseif (strlen($password) < 6) {
        set_flash_message('error', 'Password must be at least 6 characters long.');
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            set_flash_message('error', 'An account with this email address already exists.');
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmtInsert = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, phone, role, preferred_barber_id, preferred_style) VALUES (?, ?, ?, ?, 'customer', ?, ?)");
            
            if ($stmtInsert->execute([$full_name, $email, $password_hash, $phone, $preferred_barber_id, $preferred_style])) {
                $new_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $new_id;
                $_SESSION['user_role'] = 'customer';
                $_SESSION['user_name'] = $full_name;

                set_flash_message('success', 'Registration successful! Welcome to Noli at Fili.');
                header('Location: /customer/dashboard.php');
                exit;
            } else {
                set_flash_message('error', 'Database error during account registration. Please try again.');
            }
        }
    }
}

$page_title = "Customer Registration";
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <div class="card-header text-center">
            <div>
                <span class="eyebrow-label">Join Noli at Fili</span>
                <h1 class="card-title card-title-lg">Create Customer Account</h1>
            </div>
        </div>

        <form action="/register.php" method="POST">
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="e.g. Juan Dela Cruz" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="text" id="phone" name="phone" class="form-control" required placeholder="09171234567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Re-type password">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="preferred_barber_id" class="form-label">Preferred Barber (Optional)</label>
                    <select id="preferred_barber_id" name="preferred_barber_id" class="form-select">
                        <option value="">-- Any Available Barber --</option>
                        <?php foreach ($barbers as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= (($_POST['preferred_barber_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($b['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="preferred_style" class="form-label">Preferred Haircut Style (Optional)</label>
                    <input type="text" id="preferred_style" name="preferred_style" class="form-control" placeholder="e.g. Low Skin Fade & Textured Top" value="<?= htmlspecialchars($_POST['preferred_style'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="/login.php" class="auth-link">Log In Here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
