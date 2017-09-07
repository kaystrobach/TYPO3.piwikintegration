<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * ext_tables.php.
 *
 * Ext Tables configuration
 *
 * $Id: ext_tables.php 42977 2011-02-02 12:08:56Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/*******************************************************************************
 * Add Backend Module and Ext.Direct for it
 */
    if (TYPO3_MODE == 'BE') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'KayStrobach.'.$_EXTKEY,
            'web',          // Main area
            'mod2',         // Name of the module
            '',             // Position of the module
            [          // Allowed controller action combinations
                'Piwik'             => 'index,apiCode,help',
                'PiwikInstallation' => 'index,download,patch,configure',
            ],
            [          // Additional configuration
                'access'    => 'user,group',
                'icon'      => 'EXT:piwikintegration/Resources/Public/Images/module.png',
                'labels'    => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang_mod.xml',
            ]
        );
    }

/*******************************************************************************
 * Static file
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/',
    'Piwik Integration'
);

$tempColumns = [
    'tx_piwikintegration_api_code' => [
        'exclude' => 0,
        'label'   => 'LLL:EXT:piwikintegration/Resources/Private/Language/locallang_db.xml:be_users.tx_piwikintegration_api_code',
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
    'tx_piwikintegration_api_code;;;;1-1-1'
);

// remove default plugin fields
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] =
    'layout,select_key,pages,recursive';

//add flexform to pi1
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] =
    'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $_EXTKEY.'_pi1',
    'FILE:EXT:piwikintegration/pi1/flexform_ds.xml'
);

//add pi1 plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:piwikintegration/pi1/locallang.xml:piwikintegration_pi1',
        $_EXTKEY.'_pi1',
    ]
);
if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_piwikintegration_pi1_wizicon'] =
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'pi1/class.tx_piwikintegration_pi1_wizicon.php';
}
