<?php

namespace Bauerdot\CzechLibrary;

use Bauerdot\CzechLibrary\Interfaces\CelebrationInterface;
use Bauerdot\CzechLibrary\Interfaces\LocalizationInterface;

class CzechLibraryFactory
{
    /**
     * Create a Czech celebration service
     * 
     * @param LocalizationInterface|null $localization Optional custom localization
     * @return CelebrationInterface
     */
    public static function createCzechCelebrationService(?LocalizationInterface $localization = null): CelebrationInterface
    {
        return new CzechCelebrationService($localization);
    }
}
