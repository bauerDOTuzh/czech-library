<?php

namespace Bauerdot\CzechLibrary\Tests;

use Bauerdot\CzechLibrary\CzechCelebrationService;
use Bauerdot\CzechLibrary\Localization\CzechLocalization;
use PHPUnit\Framework\TestCase;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class CzechCelebrationServiceTest extends TestCase
{
    private CzechCelebrationService $service;
    
    protected function setUp(): void
    {
        $this->service = new CzechCelebrationService(new CzechLocalization());
    }
    
    public function testGetNameDaysForDate()
    {
        // January 1st
        $date = new DateTime('2025-01-01');
        $this->assertEquals([], $this->service->getNameDaysForDate($date));
        
        // January 2nd - Karina
        $date = new DateTime('2025-01-02');
        $this->assertEquals(['Karina'], $this->service->getNameDaysForDate($date));
        
        // January 6th - Kašpar, Melichar, Baltazar
        $date = new DateTime('2025-01-06');
        $this->assertEquals(['Kašpar', 'Melichar', 'Baltazar'], $this->service->getNameDaysForDate($date));
    }
    
    public function testGetDateForName()
    {
        // Common Czech name
        $date = $this->service->getDateForName('Karina');
        $this->assertInstanceOf(DateTimeInterface::class, $date);
        $this->assertEquals('01-02', $date->format('m-d'));
        
        // Test case insensitivity 
        $date1 = $this->service->getDateForName('karina');
        $date2 = $this->service->getDateForName('Karina');
        $this->assertEquals($date1->format('m-d'), $date2->format('m-d'));
        
        // Test accents handling
        $date1 = $this->service->getDateForName('Kaspar');
        $date2 = $this->service->getDateForName('Kašpar');
        $this->assertEquals($date1->format('m-d'), $date2->format('m-d'));
        
        // Non-existent name
        $this->assertNull($this->service->getDateForName('NonExistentName'));
    }
    
    public function testGetHolidayForDate()
    {
        // New Year's Day
        $date = new DateTime('2025-01-01');
        $this->assertEquals('Nový rok', $this->service->getHolidayForDate($date));
        
        // Christmas Eve
        $date = new DateTime('2025-12-24');
        $this->assertEquals('Štědrý den', $this->service->getHolidayForDate($date));
        
        // Regular day (not a holiday)
        $date = new DateTime('2025-03-15');
        $this->assertNull($this->service->getHolidayForDate($date));
    }
    
    public function testGenerateNameDayGreeting()
    {
        // Single name
        $date = new DateTime('2025-01-02'); // Karina
        $this->assertEquals('Dnes má svátek Karina', $this->service->generateNameDayGreeting($date));
        
        // Multiple names
        $date = new DateTime('2025-01-06'); // Kašpar, Melichar, Baltazar
        $this->assertEquals(
            'Dnes má svátek Kašpar, Melichar a Baltazar', 
            $this->service->generateNameDayGreeting($date)
        );
        
        // Custom template
        $this->assertEquals(
            'Svátek slaví: Kašpar, Melichar a Baltazar', 
            $this->service->generateNameDayGreeting($date, 'Svátek slaví: {names}')
        );
        
        // No names
        $date = new DateTime('2025-01-01');
        $this->assertEquals('', $this->service->generateNameDayGreeting($date));
    }
    
    public function testGenerateHolidayAnnouncement()
    {
        // New Year's Day
        $date = new DateTime('2025-01-01');
        $this->assertEquals('Dnes je Nový rok', $this->service->generateHolidayAnnouncement($date));
        
        // Custom template
        $this->assertEquals(
            'Státní svátek: Nový rok', 
            $this->service->generateHolidayAnnouncement($date, 'Státní svátek: {holiday}')
        );
        
        // Not a holiday
        $date = new DateTime('2025-03-15');
        $this->assertNull($this->service->generateHolidayAnnouncement($date));
    }
}
