<?php
// add_services.php - One-time migration script to insert the 20 haircut services
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/plain; charset=utf-8');

$pdo = get_db_connection();

$new_services = [
    [
        'name' => 'Low Fade',
        'description' => 'A subtle, clean fade starting low around the ears and nape for a natural, tapered blend.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Mid Fade',
        'description' => 'A balanced fade starting at the midpoint of the head for a clean, sharp look.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'High Fade',
        'description' => 'A bold, high-contrast fade starting near the crown to highlight top length.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'Skin/Bald Fade',
        'description' => 'An ultra-clean razor or foil shaver finish blending seamlessly down to bare skin.',
        'price' => 180.00,
        'duration_minutes' => 45
    ],
    [
        'name' => 'Taper Fade',
        'description' => 'A subtle taper around the sideburns and neckline while maintaining side volume.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Burst Fade',
        'description' => 'A rounded fade curving around the ear, creating a distinct burst effect.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'Drop Fade',
        'description' => 'A smooth fade line that drops low behind the ear following the natural shape of the head.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'Two-Block',
        'description' => 'A popular Asian-inspired haircut with closely cropped sides and longer, layered top hair.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'Undercut',
        'description' => 'Sharp contrast with short sides disconnected from a longer top.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Textured Crop',
        'description' => 'Short, choppily textured top styling with a blunt or faded fringe.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'French Crop',
        'description' => 'Classic short haircut featuring a blunt horizontal fringe and low fade or taper.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Caesar Cut',
        'description' => 'A short, uniform cut with a straight-cut short fringe across the forehead.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Crew Cut',
        'description' => 'A timeless, low-maintenance cut with short faded sides and slightly longer top hair.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Buzz Cut / Army Cut',
        'description' => 'A uniform, ultra-short clipper cut for a sharp military-inspired appearance.',
        'price' => 150.00,
        'duration_minutes' => 25
    ],
    [
        'name' => 'Classic Barber Cut / 2×3',
        'description' => 'Traditional, neat barbering cut using standard guard settings for a clean finish.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Side Part',
        'description' => 'A polished, classic look featuring a defined side part and groomed sides.',
        'price' => 150.00,
        'duration_minutes' => 30
    ],
    [
        'name' => 'Slick Back',
        'description' => 'Sleek, combed-back top styling with neat faded or tapered sides.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'Comma Hair',
        'description' => 'A trendy Korean-style haircut featuring an inward-curving front fringe shaped like a comma.',
        'price' => 180.00,
        'duration_minutes' => 40
    ],
    [
        'name' => 'Wolf Cut',
        'description' => 'A layered, textured hybrid of a shag and mullet with voluminous movement.',
        'price' => 180.00,
        'duration_minutes' => 45
    ],
    [
        'name' => 'Mullet / Burst Fade Mullet',
        'description' => 'Short front and sides with a burst fade, paired with longer hair at the back.',
        'price' => 180.00,
        'duration_minutes' => 45
    ]
];

try {
    $pdo->beginTransaction();

    // 1. Mark existing services as inactive (is_active = 0) so old records remain valid but do not show up in active bookings
    $new_names = array_column($new_services, 'name');
    $in_clause = implode(',', array_fill(0, count($new_names), '?'));
    
    // Set active = 0 for any service NOT in the new 20 service names
    $stmtDeactivate = $pdo->prepare("UPDATE services SET is_active = 0 WHERE name NOT IN ($in_clause)");
    $stmtDeactivate->execute($new_names);

    // 2. Insert the 20 new services, skipping any that already exist
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM services WHERE name = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO services (name, description, price, duration_minutes, is_active) VALUES (?, ?, ?, ?, 1)");

    $inserted_count = 0;
    $skipped_count = 0;

    foreach ($new_services as $svc) {
        $stmtCheck->execute([$svc['name']]);
        if ($stmtCheck->fetchColumn() > 0) {
            // Already exists, ensure it is set to active
            $stmtUpdate = $pdo->prepare("UPDATE services SET description = ?, price = ?, duration_minutes = ?, is_active = 1 WHERE name = ?");
            $stmtUpdate->execute([$svc['description'], $svc['price'], $svc['duration_minutes'], $svc['name']]);
            $skipped_count++;
        } else {
            $stmtInsert->execute([$svc['name'], $svc['description'], $svc['price'], $svc['duration_minutes']]);
            $inserted_count++;
        }
    }

    $pdo->commit();

    echo "Migration completed successfully!\n";
    echo "Inserted: $inserted_count new services\n";
    echo "Updated/Existed: $skipped_count services\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Migration failed: " . $e->getMessage() . "\n";
}
