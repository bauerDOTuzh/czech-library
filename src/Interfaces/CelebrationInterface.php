<?php

namespace Bauerdot\CzechLibrary\Interfaces;

interface CelebrationInterface
{
    /**
     * Get all name days for a specific date
     * 
     * @param \DateTimeInterface $date The date to check
     * @return array List of names that have their name day on this date
     */
    public function getNameDaysForDate(\DateTimeInterface $date): array;
    
    /**
     * Find when a specific name has its name day
     * 
     * @param string $name The name to search for
     * @return \DateTimeInterface|null The date of the name day or null if not found
     */
    public function getDateForName(string $name): ?\DateTimeInterface;
    
    /**
     * Check if today is a holiday
     * 
     * @param \DateTimeInterface $date The date to check
     * @return string|null The name of the holiday or null if not a holiday
     */
    public function getHolidayForDate(\DateTimeInterface $date): ?string;
    
    /**
     * Generate a greeting string for today's name day celebrants
     * 
     * @param \DateTimeInterface $date The date to check
     * @param string $template Template with {names} placeholder
     * @return string The formatted greeting
     */
    public function generateNameDayGreeting(\DateTimeInterface $date, string $template = "Dnes má svátek {names}"): string;
    
    /**
     * Generate a holiday announcement
     * 
     * @param \DateTimeInterface $date The date to check
     * @param string $template Template with {holiday} placeholder
     * @return string|null The holiday announcement or null if not a holiday
     */
    public function generateHolidayAnnouncement(\DateTimeInterface $date, string $template = "Dnes je {holiday}"): ?string;
    
    /**
     * Generate a complete daily greeting with both holiday and name day information
     * Including emojis for a modern touch
     * 
     * @param \DateTimeInterface $date The date to check
     * @param bool $includeEmojis Whether to include emojis in the greeting
     * @return string The complete daily greeting
     */
    public function getDailyGreeting(\DateTimeInterface $date, bool $includeEmojis = true): string;
}
