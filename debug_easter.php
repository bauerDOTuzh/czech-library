<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use Bauerdot\CzechLibrary\CzechLibraryFactory;

// Enable debug mode for holiday calculations
define('DEBUG_HOLIDAYS', true);

// Create the service
$czechService = CzechLibraryFactory::createCzechCelebrationService();

// Calculate Easter dates for 2024, 2025, 2026
$years = [2024, 2025, 2026];

foreach ($years as $year) {
    echo "\n=== EASTER HOLIDAYS FOR $year ===\n";
    
    // Calculate Easter Sunday using the Gauss/Meeus algorithm
    $a = $year % 19;
    $b = intval($year / 100);
    $c = $year % 100;
    $d = intval($b / 4);
    $e = $b % 4;
    $f = intval(($b + 8) / 25);
    $g = intval(($b - $f + 1) / 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intval($c / 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = intval(($a + 11 * $h + 22 * $l) / 451);
    $month = intval(($h + $l - 7 * $m + 114) / 31);
    $day = (($h + $l - 7 * $m + 114) % 31) + 1;
    
    $easterSunday = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $easterSundayDate = new DateTime($easterSunday);
    
    // Get Easter-related dates
    $goodFriday = clone $easterSundayDate;
    $goodFriday->modify('-2 days');
    
    $easterMonday = clone $easterSundayDate;
    $easterMonday->modify('+1 day');
    
    echo "Easter Sunday: " . $easterSundayDate->format('Y-m-d') . "\n";
    echo "Good Friday: " . $goodFriday->format('Y-m-d') . "\n";
    echo "Easter Monday: " . $easterMonday->format('Y-m-d') . "\n";
    
    echo "\n--- Holiday Check ---\n";
    
    // Check if our service recognizes these as holidays
    $dates = [
        'Good Friday' => $goodFriday,
        'Easter Sunday' => $easterSundayDate,
        'Easter Monday' => $easterMonday
    ];
    
    foreach ($dates as $label => $date) {
        $holiday = $czechService->getHolidayForDate($date);
        echo "$label ({$date->format('Y-m-d')}): " . ($holiday ?? "Not a holiday") . "\n";
    }
}
