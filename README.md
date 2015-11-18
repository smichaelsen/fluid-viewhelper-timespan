# TYPO3 Fluid Timespan ViewHelper

This package serves a ViewHelper for the TYPO3 templating engine Fluid.

## Installation

1. Add to your `composer.json`:

`"require": { "smichaelsen/fluid-viewhelper-timespan": "dev-master" }`

2. Copy the file `Resources/Private/Language/locallang.xlf` to your TYPO3 extension (or merge the contents if you already have a locallang file) and adjust/translate the labels.

No installation in the TYPO3 extension manager needed, because it is no TYPO3 extension!

## Usage example

    <html data-namespace-typo3-fluid="true" xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers" xmlns:vhts="http://typo3.org/ns/Smichaelsen/FluidViewHelperTimespan/ViewHelpers">

      User {user.name} registered {user.crdate -> vhts:timeSpan(limitUnits:'2')}.

    </html>

Output is something like:

User Sebastian registered 3 months 7 days ago.
