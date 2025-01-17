<?php

namespace Smichaelsen\FluidViewHelperTimespan\ViewHelpers;

use Smichaelsen\FluidViewHelperTimespan\RelativeTimeService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class TimeSpanViewHelper
 *
 * Returns a human readable string stating the time interval between now and the given reference time.
 * Uses the locallang.xml/xlf of the current request's extension. The LLL keys are:
 * timespan.day
 * timespan.days
 * timespan.hour
 * timespan.hours
 * timespan.minute
 * timespan.minutes
 * timespan.month
 * timespan.months
 * timespan.second
 * timespan.seconds
 * timespan.since
 * timespan.until
 * timespan.year
 * timespan.years
 *
 * Usage: <m:timeSpan reference="{myTime}" />
 * Example output: 4 days 12 hours 7 minutes and 42 seconds ago
 * Example output: in 4 days 12 hours 7 minutes and 42 seconds
 *
 * Inline Usage: {myTime -> m:timeSpan()}
 */
class TimeSpanViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly RelativeTimeService $relativeTimeService,
    ) {}

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('extensionName', 'string', 'Load labels from this extension', true);
        $this->registerArgument('limitUnits', 'integer', 'Limit the amount of displayed units', false, 99);
        $this->registerArgument('precision', 'string', 'By default the timespan will be diplayed accurately down to the second. Provide "year", "month", "day", "hour" or "minute" to lower the precision.', false, 'second');
        $this->registerArgument('reference', \DateTime::class, 'The reference time, can also be passed as child content');
    }

    public function render(): string
    {
        $now = new \DateTime();
        /** @var \DateTime $reference */
        $reference = $this->arguments['reference'] ?: $this->renderChildren();
        if (is_numeric($reference)) {
            $reference = \DateTime::createFromFormat('U', $reference);
        }
        if (!$reference instanceof \DateTimeInterface) {
            return '';
        }
        $difference = $now->diff($reference);
        return $this->relativeTimeService->getRelativeTimeString(
            $difference,
            $this->arguments['extensionName'],
            $this->arguments['precision'],
            $this->arguments['limitUnits'],
            true
        );
    }
}
