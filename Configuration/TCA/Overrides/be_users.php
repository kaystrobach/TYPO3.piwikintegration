<?php

defined('TYPO3_MODE') or die();

$tempColumns = [
    'tx_piwikintegration_api_code' => [
        'exclude' => 0,
        'label'   => 'LLL:EXT:piwikintegration/Resources/Private/Language/locallang_db.xlf:be_users.tx_piwikintegration_api_code',
        'config'  => [
            'type'     => 'input',
            'readOnly' => true,
            'eval'     => 'unique,uniqueInPid',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'be_users',
    $tempColumns
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_users',
    'tx_piwikintegration_api_code'
);
