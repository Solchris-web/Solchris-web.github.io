<?php
// add_categories.php - Migration script to add 'category' column and set categories & durations
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = get_db_connection();

try {
    $pdo->beginTransaction();

    // 1. Add category column if not existing
    $columns = $pdo->query("PRAGMA table_info(services)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('category', $columns)) {
        $pdo->exec("ALTER TABLE services ADD COLUMN category TEXT;");
        echo "Added 'category' column to 'services' table.\n";
    } else {
        echo "'category' column already exists.\n";
    }

    // 2. Define Category & Approved Duration Mapping for the 20 Haircut Services
    $category_updates = [
        // Fades (7)
        'Low Fade' => ['category' => 'Fades', 'duration' => 30],
        'Mid Fade' => ['category' => 'Fades', 'duration' => 30],
        'High Fade' => ['category' => 'Fades', 'duration' => 35],
        'Skin/Bald Fade' => ['category' => 'Fades', 'duration' => 40],
        'Taper Fade' => ['category' => 'Fades', 'duration' => 30],
        'Burst Fade' => ['category' => 'Fades', 'duration' => 40],
        'Drop Fade' => ['category' => 'Fades', 'duration' => 40],

        // Classic & Short Cuts (5)
        'Caesar Cut' => ['category' => 'Classic & Short Cuts', 'duration' => 25],
        'Crew Cut' => ['category' => 'Classic & Short Cuts', 'duration' => 25],
        'Buzz Cut / Army Cut' => ['category' => 'Classic & Short Cuts', 'duration' => 20],
        'Classic Barber Cut / 2×3' => ['category' => 'Classic & Short Cuts', 'duration' => 30],
        'Side Part' => ['category' => 'Classic & Short Cuts', 'duration' => 30],

        // Crop & Contrast Cuts (4)
        'Two-Block' => ['category' => 'Crop & Contrast Cuts', 'duration' => 40],
        'Undercut' => ['category' => 'Crop & Contrast Cuts', 'duration' => 30],
        'Textured Crop' => ['category' => 'Crop & Contrast Cuts', 'duration' => 40],
        'French Crop' => ['category' => 'Crop & Contrast Cuts', 'duration' => 30],

        // Long & Styled Cuts (4)
        'Slick Back' => ['category' => 'Long & Styled Cuts', 'duration' => 40],
        'Comma Hair' => ['category' => 'Long & Styled Cuts', 'duration' => 40],
        'Wolf Cut' => ['category' => 'Long & Styled Cuts', 'duration' => 50],
        'Mullet / Burst Fade Mullet' => ['category' => 'Long & Styled Cuts', 'duration' => 50]
    ];

    $stmtUp = $pdo->prepare("UPDATE services SET category = ?, duration_minutes = ? WHERE name = ?");
    $updated_count = 0;

    foreach ($category_updates as $name => $info) {
        $stmtUp->execute([$info['category'], $info['duration'], $name]);
        $updated_count += $stmtUp->rowCount();
    }

    // Default any legacy or uncategorized active service to 'Other'
    $pdo->exec("UPDATE services SET category = 'Other' WHERE category IS NULL OR category = '';");

    $pdo->commit();

    echo "Successfully updated $updated_count services with categories and approved durations!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Migration failed: " . $e->getMessage() . "\n";
}
