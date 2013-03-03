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
 * pi1/tx_piwikintegration_flexform
 *
 * helper for the flexform
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
include_once(t3lib_extMgm::extPath('piwikintegration', 'Classes/Lib/Div.php'));
class tx_piwikintegration_flexform {
	function init() {
		$this->tablePrefix = tx_piwikintegration_install::getInstaller()->getConfigObject()->getTablePrefix();
	}
	function getSitesForFlexForm(&$PA,&$fobj) {
		$this->init();
		//fetch anonymous accessable idsites
		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'idsite',
			tx_piwikintegration_div::getTblName('access'),
			'login="anonymous"'
		);

		//build array for selecting more information
		$sites = array();
		foreach($erg as $site) {
			$sites[] = $site['idsite'];
		}
		$accessableSites = implode(',',$sites);
		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'idsite,name,main_url',
			tx_piwikintegration_div::getTblName('site'),
			'idsite IN('.$accessableSites.')',
			'',
			'name, main_url, idsite'
		);
		$PA['items'] = array();

		//render items
		while(($site = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($erg)) !== false) {
			$PA['items'][] = array(
				$site['idsite'].' : '.($site['name'] ? $site['name'].' : '.$site['main_url'] : $site['main_url']),
				$site['idsite'],
				'i/domain.gif',
			);
		}
	}
	static function getWidgetsForFlexForm(&$PA,&$fobj) {
		$PA['items'] = array();
		
		tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
		$controller = Piwik_FrontController::getInstance()->init();
		$_GET['idSite']=1;
		$widgets = Piwik_GetWidgetsList();
		

		foreach($widgets as $pluginCat => $plugin) {
			foreach($plugin as $widget) {
				$PA['items'][] = array(
					$pluginCat.' : '.$widget['name'],
					base64_encode(json_encode($widget['parameters'])),
					'i/catalog.gif'
				);
			}
		}
	}
}