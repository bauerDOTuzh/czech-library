<?php

namespace Bauerdot\CzechLibrary\Tests;

use Bauerdot\CzechLibrary\CzechCelebrationService;
use Bauerdot\CzechLibrary\Localization\CzechLocalization;
use PHPUnit\Framework\TestCase;
use DateTime;

class EasterHolidaysTest extends TestCase
{
    private CzechCelebrationService $service;
    
    protected function setUp(): void
    {
        $this->service = new CzechCelebrationService(new CzechLocalization());
    }
    
    /**
     * Calculate Easter Sunday date using the Gauss/Meeus algorithm
     */
    private function calculateEasterSunday(int $year): DateTime
    {
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
        
        $date = DateTime::createFromFormat('Y-n-j', "$year-$month-$day");
        $date->setTime(0, 0, 0);
        return $date;
    }
    
    public function testEasterHolidays2024()
    {
        // Test Easter 2024
        $year = 2024;
        $easterSunday = $this->calculateEasterSunday($year);
        
        $goodFriday = clone $easterSunday;
        $goodFriday->modify('-2 days');
        
        $easterMonday = clone $easterSunday;
        $easterMonday->modify('+1 day');
        
        // Good Friday should be a holiday
        $this->assertEquals('Velký pátek', $this->service->getHolidayForDate($goodFriday));
        
        // Easter Sunday should be a holiday
        $this->assertEquals('Velikonoční neděle', $this->service->getHolidayForDate($easterSunday));
        
        // Easter Monday should be a holiday
        $this->assertEquals('Velikonoční pondělí', $this->service->getHolidayForDate($easterMonday));
    }
    
    public function testEasterHolidays2025()
    {
        // Test Easter 2025
        $year = 2025;
        $easterSunday = $this->calculateEasterSunday($year);
        
        $goodFriday = clone $easterSunday;
        $goodFriday->modify('-2 days');
        
        $easterMonday = clone $easterSunday;
        $easterMonday->modify('+1 day');
        
        // Good Friday should be a holiday
        $this->assertEquals('Velký pátek', $this->service->getHolidayForDate($goodFriday));
        
        // Easter Sunday should be a holiday
        $this->assertEquals('Velikonoční neděle', $this->service->getHolidayForDate($easterSunday));
        
        // Easter Monday should be a holiday
        $this->assertEquals('Velikonoční pondělí', $this->service->getHolidayForDate($easterMonday));
    }
}
