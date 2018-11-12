<?php
declare(strict_types=1);
namespace Smichaelsen\FluidViewHelperTimespan;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class RelativeTimeUtility
{
    public static function getRelativeTimeString(\DateInterval $interval, string $extensionName, string $precision, int $limitUnits = 99, bool $wrapInPreposition = false): string
    {
        $messageParts = [];
        $timeunits = [
            'year' => $interval->y,
            'month' => $interval->m,
            'day' => $interval->d,
            'hour' => $interval->h,
            'minute' => $interval->i,
            'second' => $interval->s,
        ];
        foreach ($timeunits as $unit => $value) {
            if ($value) {
                if ($value === 1 && $unit === 'day' && $precision === 'day') {
                    if ($interval->invert) {
                        $key = 'yesterday';
                    } else {
                        $key = 'tomorrow';
                    }
                    // "yesterday" and "tomorrow" are returned without wrapping them in "since" or "until"
                    return LocalizationUtility::translate('timespan.' . $key, $extensionName, [$value]);
                }
                $key = $unit . ($value > 1 ? 's' : '');
                $messageParts[] = LocalizationUtility::translate('timespan.' . $key, $extensionName, [$value]);
            }
            if ($unit === $precision || count($messageParts) === $limitUnits) {
                break;
            }
        }
        if (count($messageParts) === 0) {
            if ($precision === 'day') {
                // reference less than a day, but "day" is the precision
                $key = 'today';
            } elseif (self::dateIntervalIsNull($interval)) {
                // reference is just now
                $key = 'now';
            } elseif ($interval->invert) {
                // reference is in the past but less than the provided precision
                $key = 'recently';
            } else {
                // reference is in the future but less than the provided precision
                $key = 'soon';
            }
            // "today", "now", "recently" and "soon" are returned without wrapping them in "since" or "until"
            return LocalizationUtility::translate('timespan.' . $key, $extensionName);
        }
        $timeString = implode(' ', $messageParts);
        if ($wrapInPreposition) {
            $key = ($interval->invert ? 'since' : 'until');
            $timeString = LocalizationUtility::translate('timespan.' . $key, $extensionName, [$timeString]);
        }
        return $timeString;
    }

    public static function createDateIntervalFromSeconds(int $seconds): \DateInterval
    {
        $reference = new \DateTime();
        $reference->add(\DateInterval::createFromDateString($seconds . ' seconds'));
        return (new \DateTime())->diff($reference);
    }

    protected static function dateIntervalIsNull(\DateInterval $dateInterval): bool
    {
        return (new \DateTime()) === (new \DateTime())->add($dateInterval);
    }
}
