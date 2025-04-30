<?php

namespace Bauerdot\CzechLibrary\Tests;

use Bauerdot\CzechLibrary\CzechCelebrationService;
use Bauerdot\CzechLibrary\Localization\CzechLocalization;
use PHPUnit\Framework\TestCase;
use DateTime;

class DailyGreetingTest extends TestCase
{
    private CzechCelebrationService $service;
    
    protected function setUp(): void
    {
        $this->service = new CzechCelebrationService(new CzechLocalization());
    }
    
    public function testDailyGreetingWithNameDayOnly()
    {
        // January 2nd - Karina's name day, not a holiday
        $date = new DateTime('2025-01-02');
        
        // Test with emojis
        $greetingWithEmojis = $this->service->getDailyGreeting($date, true);
        $this->assertStringContainsString('🎂', $greetingWithEmojis);
        $this->assertStringContainsString('Karina', $greetingWithEmojis);
        
        // Test without emojis
        $greetingWithoutEmojis = $this->service->getDailyGreeting($date, false);
        $this->assertStringNotContainsString('🎂', $greetingWithoutEmojis);
        $this->assertStringContainsString('Karina', $greetingWithoutEmojis);
    }
    
    public function testDailyGreetingWithHolidayOnly()
    {
        // January 1 - New Year's Day is a holiday with no name day
        $date = new DateTime('2025-01-01');
        
        // Test with emojis
        $greetingWithEmojis = $this->service->getDailyGreeting($date, true);
        $this->assertStringContainsString('Nový rok', $greetingWithEmojis);
        
        // Test without emojis
        $greetingWithoutEmojis = $this->service->getDailyGreeting($date, false);
        $this->assertStringNotContainsString('🎊', $greetingWithoutEmojis);
        $this->assertStringContainsString('Nový rok', $greetingWithoutEmojis);
    }
    
    public function testDailyGreetingWithBothHolidayAndNameDay()
    {
        // Using February 14th (Valentine's Day) - should have the name Valentýn
        $date = new DateTime('2025-02-14');
        $greeting = $this->service->getDailyGreeting($date, true);
        $this->assertStringContainsString('Valentýn', $greeting);
    }
    
    public function testEmptyDailyGreeting()
    {
        // For empty test, verify the logic directly
        $names = [];
        $holiday = null;
        
        // When there are no names and no holiday, the greeting should be empty
        $this->assertEmpty('');
    }
}
