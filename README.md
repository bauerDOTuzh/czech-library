# Czech Library

A PHP library for working with Czech name days and holidays, including support for movable holidays like Easter.

## Installation

```bash
composer require bauerdot/czech-library
```

## Features

- Get name days for a specific date
- Find when a specific name has its name day
- Check if a specific date is a Czech holiday (including movable holidays like Easter)
- Generate name day greetings
- Generate holiday announcements
- Support for localization
- Accurate Easter holiday calculations

## Usage Examples

### Basic Usage

```php
use Bauerdot\CzechLibrary\CzechLibraryFactory;

// Create the service
$czechService = CzechLibraryFactory::createCzechCelebrationService();

// Get today's name days
$today = new DateTime();
$nameDays = $czechService->getNameDaysForDate($today);

// Output example: ["Blahoslav"]
var_dump($nameDays);

// Generate a greeting
$greeting = $czechService->generateNameDayGreeting($today);
// Output example: "Dnes má svátek Blahoslav"
echo $greeting;

// Check if today is a holiday
$holiday = $czechService->getHolidayForDate($today);
if ($holiday) {
    // Output example: "Dnes je Svátek práce"
    echo $czechService->generateHolidayAnnouncement($today);
}

// Find when a specific name has its name day
$nameDate = $czechService->getDateForName('Karina');
echo $nameDate ? $nameDate->format('j.n.') : 'Name day not found'; // Output: "2.1."
```

### Working with Easter Holidays

The library accurately handles Czech Easter holidays (Good Friday, Easter Sunday, and Easter Monday):

```php
use Bauerdot\CzechLibrary\CzechLibraryFactory;

$czechService = CzechLibraryFactory::createCzechCelebrationService();

// Check Good Friday 2025 (April 18, 2025)
$goodFriday = new DateTime('2025-04-18');
echo $czechService->getHolidayForDate($goodFriday); // "Velký pátek"

// Check Easter Sunday 2025 (April 20, 2025)
$easterSunday = new DateTime('2025-04-20');
echo $czechService->getHolidayForDate($easterSunday); // "Velikonoční neděle"

// Check Easter Monday 2025 (April 21, 2025)
$easterMonday = new DateTime('2025-04-21');
echo $czechService->getHolidayForDate($easterMonday); // "Velikonoční pondělí"
```

### Custom Templates

```php
use Bauerdot\CzechLibrary\CzechLibraryFactory;

$czechService = CzechLibraryFactory::createCzechCelebrationService();
$today = new DateTime();

// Custom templates
$nameGreeting = $czechService->generateNameDayGreeting($today, "Dnes slaví svátek {names}");
$holidayAnnouncement = $czechService->generateHolidayAnnouncement($today, "Dnes je státní svátek: {holiday}");

echo $nameGreeting;
echo $holidayAnnouncement;
```

### Multiple Names

The library handles dates with multiple names correctly:

```php
use Bauerdot\CzechLibrary\CzechLibraryFactory;

$czechService = CzechLibraryFactory::createCzechCelebrationService();

// January 6 has three names: Kašpar, Melichar, Baltazar
$threeKings = new DateTime('2025-01-06');
$nameGreeting = $czechService->generateNameDayGreeting($threeKings);

echo $nameGreeting; // "Dnes má svátek Kašpar, Melichar a Baltazar"
```

### Combined Daily Greeting with Emojis

Get a combined greeting that includes both holiday and name day information, enhanced with emojis:

```php
use Bauerdot\CzechLibrary\CzechLibraryFactory;

$czechService = CzechLibraryFactory::createCzechCelebrationService();

// Get the daily greeting for today
$today = new DateTime();
$dailyGreeting = $czechService->getDailyGreeting($today);
echo $dailyGreeting; 
// Example output: "🎄 Dnes je Štědrý den a 🎂 svátek má Eva a Adam"

// Without emojis
$dailyGreetingNoEmojis = $czechService->getDailyGreeting($today, false);
echo $dailyGreetingNoEmojis;
// Example output: "Dnes je Štědrý den a svátek má Eva a Adam"
```

## Interface

The library implements the following interfaces:

### CelebrationInterface

```php
interface CelebrationInterface
{
    // Get all name days for a specific date
    public function getNameDaysForDate(\DateTimeInterface $date): array;
    
    // Find when a specific name has its name day
    public function getDateForName(string $name): ?\DateTimeInterface;
    
    // Check if a date is a holiday
    public function getHolidayForDate(\DateTimeInterface $date): ?string;
    
    // Generate name day greeting
    public function generateNameDayGreeting(\DateTimeInterface $date, string $template = null): string;
    
    // Generate holiday announcement
    public function generateHolidayAnnouncement(\DateTimeInterface $date, string $template = null): ?string;
    
    // Generate a complete daily greeting with both holiday and name day information with emojis
    public function getDailyGreeting(\DateTimeInterface $date, bool $includeEmojis = true): string;
}
```

### LocalizationInterface

```php
interface LocalizationInterface
{
    // Get locale code (e.g., 'cs')
    public function getLocaleCode(): string;
    
    // Get name day greeting template
    public function getNameDayTemplate(): string;
    
    // Get holiday announcement template
    public function getHolidayTemplate(): string;
    
    // Get combined daily greeting template with holiday and name day info
    public function getDailyGreetingTemplate(): string;
    
    // Get combined daily greeting template with emojis
    public function getDailyGreetingEmojiTemplate(): string;
    
    // Format multiple names for output
    public function formatNames(array $names): string;
}
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This library is licensed under the MIT License.
