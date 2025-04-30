<?php

namespace Bauerdot\CzechLibrary\Tests;

use Bauerdot\CzechLibrary\Localization\CzechLocalization;
use PHPUnit\Framework\TestCase;

class CzechLocalizationTest extends TestCase
{
    private CzechLocalization $localization;
    
    protected function setUp(): void
    {
        $this->localization = new CzechLocalization();
    }
    
    public function testGetLocaleCode()
    {
        $this->assertEquals('cs', $this->localization->getLocaleCode());
    }
    
    public function testGetNameDayTemplate()
    {
        $this->assertEquals('Dnes má svátek {names}', $this->localization->getNameDayTemplate());
    }
    
    public function testGetHolidayTemplate()
    {
        $this->assertEquals('Dnes je {holiday}', $this->localization->getHolidayTemplate());
    }
    
    public function testFormatNames()
    {
        // Single name
        $this->assertEquals('Jan', $this->localization->formatNames(['Jan']));
        
        // Two names
        $this->assertEquals('Jan a Petr', $this->localization->formatNames(['Jan', 'Petr']));
        
        // Three names
        $this->assertEquals('Jan, Petr a Pavel', $this->localization->formatNames(['Jan', 'Petr', 'Pavel']));
        
        // Four names
        $this->assertEquals('Jan, Petr, Pavel a Marie', $this->localization->formatNames(['Jan', 'Petr', 'Pavel', 'Marie']));
        
        // Empty array
        $this->assertEquals('', $this->localization->formatNames([]));
    }
}
