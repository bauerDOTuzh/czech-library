<?php

require __DIR__ . '/vendor/autoload.php';

use Bauerdot\CzechLibrary\CzechLibraryFactory;

// Create the service
$czechService = CzechLibraryFactory::createCzechCelebrationService();

// Get today's date
$today = new \DateTime();
echo "Today's date: " . $today->format('Y-m-d') . "\n";

// Get name days for today
$nameDays = $czechService->getNameDaysForDate($today);
if ($nameDays) {
    echo "Today's name days: " . implode(', ', $nameDays) . "\n";
} else {
    echo "No name days for today.\n";
}

// Generate a name day greeting
$greeting = $czechService->generateNameDayGreeting($today);
if ($greeting) {
    echo "$greeting\n";
}

// Generate the new combined daily greeting with emojis
echo "\n=== Daily Greeting (with emojis) ===\n";
$dailyGreeting = $czechService->getDailyGreeting($today);
echo $dailyGreeting . "\n";

// Generate the new combined daily greeting without emojis
echo "\n=== Daily Greeting (without emojis) ===\n";
$dailyGreetingNoEmojis = $czechService->getDailyGreeting($today, false);
echo $dailyGreetingNoEmojis . "\n";

// Check if today is a holiday
$holidayAnnouncement = $czechService->generateHolidayAnnouncement($today);
if ($holidayAnnouncement) {
    echo "$holidayAnnouncement\n";
}

// Example of looking up a name
$name = 'Karina';
$nameDate = $czechService->getDateForName($name);
if ($nameDate) {
    echo "$name celebrates name day on: " . $nameDate->format('j.n.') . "\n";
} else {
    echo "No name day found for $name\n";
}

// Example with Christmas
$christmas = new \DateTime('2025-12-24');
$holidayAnnouncement = $czechService->generateHolidayAnnouncement($christmas);
echo "On Christmas Eve: $holidayAnnouncement\n";

// Let's manually check various dates for Easter-related holidays
echo "\nChecking for Easter-related holidays:\n";

// Get Easter Sunday calculation from the service
function calculateEasterSunday($year) {
    // Butcher/Meeus algorithm
    $a = $year % 19;
    $b = floor($year / 100);
    $c = $year % 100;
    $d = floor($b / 4);
    $e = $b % 4;
    $f = floor(($b + 8) / 25);
    $g = floor(($b - $f + 1) / 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = floor($c / 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = floor(($a + 11 * $h + 22 * $l) / 451);
    $month = floor(($h + $l - 7 * $m + 114) / 31);
    $day = (($h + $l - 7 * $m + 114) % 31) + 1;
    
    return sprintf("%04d-%02d-%02d", $year, $month, $day);
}

$year = 2025;
$easterSunday = calculateEasterSunday($year);
$easterSundayDate = new \DateTime($easterSunday);

// Create Good Friday and Easter Monday based on Easter Sunday
$goodFriday = clone $easterSundayDate;
$goodFriday->modify('-2 days');

$easterMonday = clone $easterSundayDate;
$easterMonday->modify('+1 day');

$easterDates = [
    $goodFriday->format('Y-m-d'), // Good Friday
    $easterSunday,                // Easter Sunday
    $easterMonday->format('Y-m-d') // Easter Monday
];

echo "Easter Sunday {$year} is calculated as: {$easterSunday}\n";

foreach ($easterDates as $dateStr) {
    $date = new \DateTime($dateStr);
    $holiday = $czechService->getHolidayForDate($date);
    echo $date->format('Y-m-d') . ": " . ($holiday ?? "Not a holiday") . "\n";
}
