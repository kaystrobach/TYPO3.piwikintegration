<?php

defined('TYPO3_MODE') or die();

// Static file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'piwikintegration',
    'Configuration/TypoScript/',
    'Piwik Integration'
);
