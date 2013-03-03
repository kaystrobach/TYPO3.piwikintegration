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
 * ext_tables.php
 *
 * Ext Tables configuration
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

 
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
/*******************************************************************************
 * Add Backend Module and Ext.Direct for it
 */ 
	if (TYPO3_MODE == 'BE') {
		t3lib_extMgm::addModulePath('web_txpiwikintegrationM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		t3lib_extMgm::addModule('web', 'txpiwikintegrationM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}

/*******************************************************************************
 * Static file
 */ 
t3lib_extMgm::addStaticFile($_EXTKEY,'static/piwik_integration/', 'Piwik Integration');

$tempColumns = array (
	'tx_piwikintegration_api_code' => array (		
		'exclude' => 0,		
		'label'   => 'LLL:EXT:piwikintegration/locallang_db.xml:be_users.tx_piwikintegration_api_code',		
		'config'  => array (
			'type'    => 'input',
			'readOnly' => true,
			'eval'    => 'unique,uniqueInPid',
		),
	),
);


t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','tx_piwikintegration_api_code;;;;1-1-1');

//add flexform to pi1
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:piwikintegration/pi1/flexform_ds.xml');

//add pi1 plugin
t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:piwikintegration/pi1/locallang.xml:piwikintegration_pi1',
		$_EXTKEY.'_pi1'
	)
);
if (TYPO3_MODE=="BE")    {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_piwikintegration_pi1_wizicon"] = 
	t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_piwikintegration_pi1_wizicon.php";
}


?>