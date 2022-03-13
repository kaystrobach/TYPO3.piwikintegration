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
    exit('Access denied.');
}

/*******************************************************************************
 * Add Backend Module and Ext.Direct for it
 */
    if (TYPO3_MODE == 'BE') {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'piwikintegration',
            'web',          // Main area
            'mod2',         // Name of the module
            '',             // Position of the module
            [          // Allowed controller action combinations
                \KayStrobach\Piwikintegration\Controller\PiwikController::class             => 'index,apiCode,help',
                \KayStrobach\Piwikintegration\Controller\PiwikInstallationController::class => 'index,download,patch,configure',
            ],
            [          // Additional configuration
                'access'    => 'user,group',
                'icon'      => 'EXT:piwikintegration/Resources/Public/Images/module.svg',
                'labels'    => 'LLL:EXT:piwikintegration/Resources/Private/Language/locallang_mod.xlf',
            ]
        );
    }
