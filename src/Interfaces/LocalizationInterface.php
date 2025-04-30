<?php

namespace Bauerdot\CzechLibrary\Interfaces;

interface LocalizationInterface
{
    /**
     * Get the supported locale code
     * 
     * @return string The locale code (e.g., 'cs' for Czech)
     */
    public function getLocaleCode(): string;
    
    /**
     * Get the name day greeting template
     * 
     * @return string The template with {names} placeholder
     */
    public function getNameDayTemplate(): string;
    
    /**
     * Get the holiday announcement template
     * 
     * @return string The template with {holiday} placeholder
     */
    public function getHolidayTemplate(): string;
    
    /**
     * Get the combined daily greeting template
     * 
     * @return string The template with {holiday} and {names} placeholders
     */
    public function getDailyGreetingTemplate(): string;
    
    /**
     * Get the combined daily greeting template with emojis
     * 
     * @return string The template with {holiday}, {names}, {holiday_emoji}, and {nameday_emoji} placeholders
     */
    public function getDailyGreetingEmojiTemplate(): string;
    
    /**
     * Format multiple names for output
     * 
     * @param array $names List of names to format
     * @return string Formatted string of names
     */
    public function formatNames(array $names): string;
}
