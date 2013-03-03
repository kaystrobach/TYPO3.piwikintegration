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
 * ext_localconf.php
 *
 * localconf
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
 
if (!defined ("TYPO3_MODE"))     die ("Access denied.");

/*******************************************************************************
 * Hook für templavoilaPreview
 */ 
	$TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1']['renderPreviewContentClass'][]     = 'EXT:piwikintegration/pi1/class.tx_piwikintegration_pi1_templavoila_preview.php:tx_piwikintegration_pi1_templavoila_preview';
/*******************************************************************************
 * Save hook für ExtMgm
 */ 
	$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/mod/tools/em/index.php']['tsStyleConfigForm'][] = 'EXT:piwikintegration/Classes/Lib/Extmgm.php:tx_piwikintegration_extmgm->emSaveConstants';
/*******************************************************************************
 * Save hook für table be_users
 */
	$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:piwikintegration/Classes/Hooks/BeUserProcessing.php:tx_piwikintegration_Hooks_BeUserProcessing';

/*******************************************************************************
 * unserialize extConf
 */ 
	$_EXTCONF = unserialize($_EXTCONF);

/*******************************************************************************
 * Add widgets for Frontend
 */ 
	t3lib_extMgm::addPItoST43(
		$_EXTKEY,
		'pi1/class.tx_piwikintegration_pi1.php',
		'_pi1',
		'list_type',
		1
	);
/*******************************************************************************
 * load fe hooks
 */ 
	if(TYPO3_MODE=='FE') {
		include_once(t3lib_extMgm::extPath('piwikintegration').'Classes/Tracking/Tracking.php');
		if($_EXTCONF['enableIndependentMode']) {
			$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'tx_piwikintegration_tracking->contentPostProc_output'; 
		}
		$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'tx_piwikintegration_tracking->contentPostProc_all'; 
	}
/******************************************************************************
 * load scheduler class if scheduler is installed
 */ 
	if(t3lib_extMgm::isLoaded ('scheduler') && $_EXTCONF['enableSchedulerTask']) {
		//add task to scheduler list
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_piwikintegration_scheduler_archive'] = array(
				'extension'        => $_EXTKEY,
				'title'            => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:piwikArchiveTask.name',
				'description'      => 'LLL:EXT:' . $_EXTKEY . '/locallang.xml:piwikArchiveTask.description',
				#'additionalFields' => 'tx_piwikintegration_piwikArchiveTask_AdditionalFieldProvider',
		);
		require_once(t3lib_extMgm::extPath('piwikintegration', 'Classes/SchedulerTasks/Archive.php'));
	}
?>