<?php
namespace Smichaelsen\FluidViewHelperTimespan\ViewHelpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
class TimeSpanViewHelper extends AbstractViewHelper {

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('extensionName', 'string', 'By default labels will be loaded from the request\'s current extension. Can be overwritten by this attribute.', FALSE);
		$this->registerArgument('limitUnits', 'integer', 'Limit the amount of displayed units', FALSE, 99);
		$this->registerArgument('precision', 'string', 'By default the timespan will be diplayed accurately down to the second. Provide "year", "month", "day", "hour" or "minute" to lower the precision.', FALSE, 'second');
		$this->registerArgument('reference', \DateTime::class, 'The reference time, can also be passed as child content', FALSE);
	}

	/**
	 * return string
	 */
	public function render() {
		$this->arguments['extensionName'] = $this->arguments['extensionName'] ?: $this->controllerContext->getRequest()->getControllerExtensionName();
		$messageParts = [];
		$now = new \DateTime();
		/** @var \DateTime $reference */
		$reference = $this->arguments['reference'] ?: $this->renderChildren();
        if (!$reference instanceof \DateTimeInterface) {
            return '';
        }
		$difference = $now->diff($reference, TRUE);
		$timeunits = [
			'year' => $difference->y,
			'month' => $difference->m,
			'day' => $difference->d,
			'hour' => $difference->h,
			'minute' => $difference->i,
			'second' => $difference->s,
		];
		foreach ($timeunits as $label => $value) {
			if ($value) {
				$messageParts[] = $this->translate('timespan.' . $label . ($value > 1 ? 's' : ''), array($value));
			}
			if ($label === $this->arguments['precision']) {
				break;
			}
			if (count($messageParts) === (int)$this->arguments['limitUnits']) {
				break;
			}
		}
		return $this->translate('timespan.' . ($now > $reference ? 'since' : 'until'), array(join(' ', $messageParts)));
	}

	/**
	 * @param string $id
	 * @param array $arguments
	 * @return string
	 */
	protected function translate($id, $arguments = NULL) {
		return LocalizationUtility::translate($id, $this->arguments['extensionName'], $arguments);
	}

}
