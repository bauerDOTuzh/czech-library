<?php

require __DIR__ . '/vendor/autoload.php';

use Bauerdot\CzechLibrary\CzechLibraryFactory;

// Create the service
$czechService = CzechLibraryFactory::createCzechCelebrationService();

// Test Christmas Eve which should have both a holiday and name days
$christmasEve = new \DateTime('2025-12-24');

echo "=== Testing Daily Greeting for Christmas Eve (2025-12-24) ===\n\n";

// Get individual components
$holiday = $czechService->getHolidayForDate($christmasEve);
$nameDays = $czechService->getNameDaysForDate($christmasEve);

echo "Holiday: " . ($holiday ?? "None") . "\n";
echo "Name days: " . (empty($nameDays) ? "None" : implode(', ', $nameDays)) . "\n\n";

// Get the combined greeting
echo "=== Daily Greeting with emojis ===\n";
echo $czechService->getDailyGreeting($christmasEve, true) . "\n\n";

echo "=== Daily Greeting without emojis ===\n";
echo $czechService->getDailyGreeting($christmasEve, false) . "\n\n";

// Test Easter related holidays
$easterSunday = new \DateTime('2025-04-20'); // Easter Sunday 2025

echo "=== Testing Daily Greeting for Easter Sunday (2025-04-20) ===\n\n";

// Get individual components
$holiday = $czechService->getHolidayForDate($easterSunday);
$nameDays = $czechService->getNameDaysForDate($easterSunday);

echo "Holiday: " . ($holiday ?? "None") . "\n";
echo "Name days: " . (empty($nameDays) ? "None" : implode(', ', $nameDays)) . "\n\n";

// Get the combined greeting
echo "=== Daily Greeting with emojis ===\n";
echo $czechService->getDailyGreeting($easterSunday, true) . "\n\n";

echo "=== Daily Greeting without emojis ===\n";
echo $czechService->getDailyGreeting($easterSunday, false) . "\n\n";
