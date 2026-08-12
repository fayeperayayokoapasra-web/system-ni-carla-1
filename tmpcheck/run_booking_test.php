<?php
// Simulate POST and include booking logic
chdir(__DIR__ . '/..');
$_POST = [
    'submit' => '1',
    'ajax' => '1',
    'name' => 'Test User',
    'phone' => '09171234567',
    'email' => 'test@example.com',
    'date' => date('Y-m-d H:i', strtotime('+3 days 10:00')),
    'staff' => 'Anna (Available)',
    'services' => ['Classic Manicure', 'Hand Spa'],
    'payment' => 'Cash',
    'reference' => ''
];

include __DIR__ . '/../customer/functions/customerbook_logic.php';

echo "Script completed\n";
