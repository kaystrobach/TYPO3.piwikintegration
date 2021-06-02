<?php

defined('TYPO3_MODE') or exit();

// Static file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'piwikintegration',
    'Configuration/TypoScript/',
    'Piwik Integration'
);
