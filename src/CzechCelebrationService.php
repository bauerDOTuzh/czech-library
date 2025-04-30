<?php

namespace Bauerdot\CzechLibrary;

use Bauerdot\CzechLibrary\Interfaces\CelebrationInterface;
use Bauerdot\CzechLibrary\Interfaces\LocalizationInterface;
use Bauerdot\CzechLibrary\Localization\CzechLocalization;
use DateTimeInterface;
use DateTime;

class CzechCelebrationService implements CelebrationInterface
{
    /**
     * @var array The name days data
     */
    private array $nameDaysData = [];
    
    /**
     * @var array The holidays data
     */
    private array $holidaysData = [];
    
    /**
     * @var LocalizationInterface
     */
    private LocalizationInterface $localization;
    
    /**
     * @var array Cache for name-to-date lookups
     */
    private array $nameToDateCache = [];

    /**
     * Constructor
     * 
     * @param LocalizationInterface|null $localization
     */
    public function __construct(?LocalizationInterface $localization = null)
    {
        // Set default localization to Czech if not provided
        $this->localization = $localization ?? new CzechLocalization();
        
        // Load the name days data
        $this->loadNameDaysData();
        
        // Load the holidays data
        $this->loadHolidaysData();
    }

    /**
     * Load name days data from JSON file
     */
    private function loadNameDaysData(): void
    {
        $filePath = __DIR__ . '/names_cz.json';
        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            $this->nameDaysData = json_decode($jsonData, true) ?? [];
        }
    }
    
    /**
     * Load holidays data from JSON file
     */
    private function loadHolidaysData(): void
    {
        $filePath = __DIR__ . '/holidays_cz.json';
        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            $this->holidaysData = json_decode($jsonData, true) ?? [];
        }
    }
    
    /**
     * Get all name days for a specific date
     * 
     * @param DateTimeInterface $date The date to check
     * @return array List of names that have their name day on this date
     */
    public function getNameDaysForDate(DateTimeInterface $date): array
    {
        $dateKey = $date->format('m-d');
        return $this->nameDaysData[$dateKey] ?? [];
    }
    
    /**
     * Find when a specific name has its name day
     * 
     * @param string $name The name to search for
     * @return DateTimeInterface|null The date of the name day or null if not found
     */
    public function getDateForName(string $name): ?DateTimeInterface
    {
        $normalizedName = $this->normalizeNameForSearch($name);
        
        // Check if we've already cached this name lookup
        if (isset($this->nameToDateCache[$normalizedName])) {
            return $this->nameToDateCache[$normalizedName];
        }
        
        // Search through all dates for the name
        foreach ($this->nameDaysData as $dateKey => $names) {
            foreach ($names as $nameDayName) {
                if ($this->normalizeNameForSearch($nameDayName) === $normalizedName) {
                    // Calculate the date for this year
                    $currentYear = (new DateTime())->format('Y');
                    // The dateKey is already in format 'MM-DD'
                    $date = DateTime::createFromFormat('Y-m-d', "$currentYear-$dateKey");
                    
                    // Cache the result for future lookups
                    $this->nameToDateCache[$normalizedName] = $date;
                    return $date;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Check if a date is a holiday
     * 
     * @param DateTimeInterface $date The date to check
     * @return string|null The name of the holiday or null if not a holiday
     */
    public function getHolidayForDate(DateTimeInterface $date): ?string
    {
        // First check fixed holidays
        $dateKey = $date->format('m-d');
        if (isset($this->holidaysData[$dateKey])) {
            return $this->holidaysData[$dateKey];
        }
        
        // Check for movable holidays (e.g., Easter)
        if (isset($this->holidaysData['__movable_holidays__'])) {
            // Calculate Easter Sunday for the given year
            $year = (int)$date->format('Y');
            
            // Get normalized input date (just Y-m-d, no time)
            $normalizedInputDate = $date->format('Y-m-d');
            
            // We'll calculate the Easter date using our algorithm
            $easterDate = $this->calculateEasterDate($year);
            
            if ($easterDate) {
                $easterSundayString = $easterDate->format('Y-m-d');
                
                if (defined('DEBUG_HOLIDAYS') && DEBUG_HOLIDAYS) {
                    echo "Easter Sunday for $year calculated as: $easterSundayString\n";
                    echo "Checking if $normalizedInputDate is a holiday\n";
                }
                
                // Create a map of dates to holiday names
                $holidayDates = [];
                
                // Process each movable holiday
                foreach ($this->holidaysData['__movable_holidays__'] as $holidayKey => $holidayData) {
                    $offset = (int)$holidayData['offset'];
                    $holidayDate = clone $easterDate;
                    $holidayDate->modify("$offset days");
                    $holidayDateString = $holidayDate->format('Y-m-d');
                    
                    // Add to our map of dates
                    $holidayDates[$holidayDateString] = $holidayData['name'];
                    
                    if (defined('DEBUG_HOLIDAYS') && DEBUG_HOLIDAYS) {
                        echo "Holiday: {$holidayData['name']}, Date: $holidayDateString\n";
                    }
                }
                
                // Now check if our input date matches any holiday date
                if (isset($holidayDates[$normalizedInputDate])) {
                    return $holidayDates[$normalizedInputDate];
                }
            }
        }
        
        return null;
    }
    
    /**
     * Calculate Easter Sunday date for a given year
     * 
     * @param int $year The year to calculate Easter for
     * @return DateTime|null The Easter Sunday date
     */
    private function calculateEasterDate(int $year): ?DateTime
    {
        // === Use our own implementation for reliability ===
        
        // Use the Gauss algorithm to calculate Easter date
        // This provides the most reliable results across years and PHP versions
        
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
        
        try {
            // Format with leading zeros to avoid ambiguity
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $easterDate = new DateTime($dateStr);
            $easterDate->setTime(0, 0, 0);
            
            if (defined('DEBUG_HOLIDAYS') && DEBUG_HOLIDAYS) {
                // For debugging: compare with PHP's built-in function
                if (function_exists('easter_date')) {
                    $timestamp = easter_date($year);
                    $phpEasterDate = new DateTime("@$timestamp");
                    $phpEasterDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
                    $phpEasterDate->setTime(0, 0, 0);
                    echo "DEBUG - Our Easter: {$easterDate->format('Y-m-d')}, PHP Easter: {$phpEasterDate->format('Y-m-d')}\n";
                }
            }
            
            return $easterDate;
        } catch (\Exception $e) {
            if (defined('DEBUG_HOLIDAYS') && DEBUG_HOLIDAYS) {
                echo "ERROR calculating Easter: " . $e->getMessage() . "\n";
            }
            return null;
        }
    }
    
    /**
     * Normalize name for case-insensitive, diacritic-insensitive search
     * 
     * @param string $name The name to normalize
     * @return string Normalized name
     */
    private function normalizeNameForSearch(string $name): string
    {
        $normalized = mb_strtolower(trim($name));
        
        // Remove diacritics (Czech accents)
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $normalized);
        
        return $normalized;
    }
    
    /**
     * Generate a greeting string for today's name day celebrants
     * 
     * @param DateTimeInterface $date The date to check
     * @param string|null $template Template with {names} placeholder
     * @return string The formatted greeting
     */
    public function generateNameDayGreeting(DateTimeInterface $date, string $template = null): string
    {
        $names = $this->getNameDaysForDate($date);
        
        if (empty($names)) {
            return '';
        }
        
        $template = $template ?? $this->localization->getNameDayTemplate();
        $formattedNames = $this->localization->formatNames($names);
        
        return str_replace('{names}', $formattedNames, $template);
    }
    
    /**
     * Generate a holiday announcement
     * 
     * @param DateTimeInterface $date The date to check
     * @param string|null $template Template with {holiday} placeholder
     * @return string|null The holiday announcement or null if not a holiday
     */
    public function generateHolidayAnnouncement(DateTimeInterface $date, string $template = null): ?string
    {
        $holiday = $this->getHolidayForDate($date);
        
        if (!$holiday) {
            return null;
        }
        
        $template = $template ?? $this->localization->getHolidayTemplate();
        
        return str_replace('{holiday}', $holiday, $template);
    }
    
    /**
     * Generate a complete daily greeting with both holiday and name day information
     * Including emojis for a modern touch if requested
     * 
     * @param DateTimeInterface $date The date to check
     * @param bool $includeEmojis Whether to include emojis in the greeting
     * @return string The complete daily greeting
     */
    public function getDailyGreeting(DateTimeInterface $date, bool $includeEmojis = true): string
    {
        $names = $this->getNameDaysForDate($date);
        $holiday = $this->getHolidayForDate($date);
        
        // If no names and no holiday, return empty string
        if (empty($names) && !$holiday) {
            return '';
        }
        
        // Get appropriate emojis for holiday types
        $holidayEmoji = $this->getHolidayEmoji($holiday);
        $nameDayEmoji = '🎂';
        
        // Format the names if available
        $formattedNames = empty($names) ? '' : $this->localization->formatNames($names);
        
        // Choose template based on what's available and emoji preference
        if ($holiday && !empty($names)) {
            // Both holiday and names
            if ($includeEmojis) {
                $template = $this->localization->getDailyGreetingEmojiTemplate();
                return str_replace(
                    ['{holiday}', '{names}', '{holiday_emoji}', '{nameday_emoji}'],
                    [$holiday, $formattedNames, $holidayEmoji, $nameDayEmoji],
                    $template
                );
            } else {
                $template = $this->localization->getDailyGreetingTemplate();
                return str_replace(
                    ['{holiday}', '{names}'],
                    [$holiday, $formattedNames],
                    $template
                );
            }
        } elseif ($holiday) {
            // Only holiday
            $template = $includeEmojis ? 
                "{$holidayEmoji} " . $this->localization->getHolidayTemplate() : 
                $this->localization->getHolidayTemplate();
            return str_replace('{holiday}', $holiday, $template);
        } else {
            // Only names
            $template = $includeEmojis ? 
                "{$nameDayEmoji} " . $this->localization->getNameDayTemplate() : 
                $this->localization->getNameDayTemplate();
            return str_replace('{names}', $formattedNames, $template);
        }
    }
    
    /**
     * Get an appropriate emoji for a holiday type
     * 
     * @param string|null $holiday The holiday name
     * @return string The emoji
     */
    private function getHolidayEmoji(?string $holiday): string
    {
        if (!$holiday) {
            return '📅';
        }
        
        // Holiday specific emojis
        $holidayEmojis = [
            // Christmas related
            'Štědrý den' => '🎄',
            'svátek vánoční' => '🎄',
            'Silvestr' => '🎉',
            'Nový rok' => '🎊',
            
            // Easter related
            'Velikonoční' => '🐰',
            'Velký pátek' => '✝️',
            
            // Other holidays
            'Svátek práce' => '👷',
            'Den vítězství' => '🎖️',
            'Den české státnosti' => '🇨🇿',
            'Den vzniku samostatného československého státu' => '🇨🇿',
            'Den boje za svobodu a demokracii' => '🕊️',
            'upálení' => '🔥',
            'Cyrila a Metoděje' => '📚',
        ];
        
        // Check if the holiday name contains any of the known keywords
        foreach ($holidayEmojis as $keyword => $emoji) {
            if (mb_stripos($holiday, $keyword) !== false) {
                return $emoji;
            }
        }
        
        // Default emoji for other holidays
        return '🎯';
    }
}
