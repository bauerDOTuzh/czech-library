<?php

namespace Bauerdot\CzechLibrary\Localization;

use Bauerdot\CzechLibrary\Interfaces\LocalizationInterface;

class CzechLocalization implements LocalizationInterface
{
    /**
     * Get the supported locale code
     * 
     * @return string The locale code (e.g., 'cs' for Czech)
     */
    public function getLocaleCode(): string
    {
        return 'cs';
    }
    
    /**
     * Get the name day greeting template
     * 
     * @return string The template with {names} placeholder
     */
    public function getNameDayTemplate(): string
    {
        return 'Dnes má svátek {names}';
    }
    
    /**
     * Get the holiday announcement template
     * 
     * @return string The template with {holiday} placeholder
     */
    public function getHolidayTemplate(): string
    {
        return 'Dnes je {holiday}';
    }
    
    /**
     * Get the combined daily greeting template
     * 
     * @return string The template with {holiday} and {names} placeholders
     */
    public function getDailyGreetingTemplate(): string
    {
        return 'Dnes je {holiday} a svátek má {names}';
    }
    
    /**
     * Get the combined daily greeting template with emojis
     * 
     * @return string The template with {holiday}, {names}, {holiday_emoji}, and {nameday_emoji} placeholders
     */
    public function getDailyGreetingEmojiTemplate(): string
    {
        return '{holiday_emoji} Dnes je {holiday} a {nameday_emoji} svátek má {names}';
    }
    
    /**
     * Format multiple names for output
     * 
     * @param array $names List of names to format
     * @return string Formatted string of names
     */
    public function formatNames(array $names): string
    {
        if (empty($names)) {
            return '';
        }
        
        if (count($names) === 1) {
            return $names[0];
        }
        
        $lastNameIndex = count($names) - 1;
        $lastNamePart = $names[$lastNameIndex];
        $firstNamesPart = array_slice($names, 0, $lastNameIndex);
        
        return implode(', ', $firstNamesPart) . ' a ' . $lastNamePart;
    }
}
