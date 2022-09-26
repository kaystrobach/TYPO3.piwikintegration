<?php

defined('TYPO3_MODE') or exit();

// remove default plugin fields
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['piwikintegration_pi1'] =
    'layout,select_key,pages,recursive';

//add flexform to pi1
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['piwikintegration_pi1'] =
    'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'piwikintegration_pi1',
    'FILE:EXT:piwikintegration/pi1/flexform_ds.xml'
);

//add pi1 plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:piwikintegration/pi1/locallang.xlf:piwikintegration_pi1',
        'piwikintegration_pi1',
    ],
    'list_type',
    'piwikintegration'
);
