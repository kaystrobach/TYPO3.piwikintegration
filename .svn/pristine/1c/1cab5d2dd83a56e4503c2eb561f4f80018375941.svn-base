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
 * class.ext_update.php
 *
 * extmgm update script
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

class ext_update {
	function access($what = 'all') {
		return TRUE;
	}
	function main() {
		global $LANG;
		$LANG->includeLLFile('EXT:piwikintegration/locallang.xml');
		$func = trim(t3lib_div::_GP('func'));
		$buffer = '';
		if(t3lib_div::_GP('do_update')) {
			if (method_exists($this, $func)) {
				$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$this->$func(),
					'',
					t3lib_FlashMessage::OK
			    );
				$buffer.= $flashMessage->render();
			} else {
				$buffer.=$LANG->getLL('methodNotFound');
			}
		}
		
		$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$LANG->getLL('installedPiwikNeeded'),
					'',
					t3lib_FlashMessage::INFO
			    );
		$buffer.= $flashMessage->render();
		$buffer.= $this->getHeader($LANG->getLL('header.installation'));
		$buffer.= $this->getButton('installPiwik',false);
		$buffer.= $this->getButton('updatePiwik');
		$buffer.= $this->getButton('removePiwik');
		$buffer.= $this->getFooter();

		$buffer.= $this->getHeader($LANG->getLL('header.configuration'));
		$buffer.= $this->getButton('patchPiwik');
		$buffer.= $this->getButton('configurePiwik');
		$buffer.= $this->getButton('enableSuggestedPlugins');
		$buffer.= $this->getButton('respectGermanDataPrivacyAct');
		$buffer.= $this->getButton('showPiwikConfig');
		$buffer.= $this->getFooter();

		$buffer.= $this->getHeader($LANG->getLL('header.database'));
		$buffer.= $this->getButton('resetUserRights');
		$buffer.= $this->getButton('truncatePiwikDB');
		$buffer.= $this->getButton('reInitPiwikDB');
		$buffer.= $this->getFooter();
		return $buffer;
	}
	function installPiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->installPiwik();
		return 'Piwik installed';
	}
	function updatePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->updatePiwik();
		return 'Piwik installed';
	}
	function removePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		if($installer->removePiwik()) {
			return 'Piwik removed';
		} else {
			return 'Piwik not removed';
		}
	}
	function patchPiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$exclude = array(
			'config/config.ini.php',
		);
		$installer->patchPiwik($exclude);
		return 'Piwik patched - without modifying config/config.ini.php';
	}
	function configurePiwik() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->getConfigObject()->makePiwikConfigured();
		return 'Piwik is configured now';
	}
	function resetUserRights() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_div.php'));
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->getConfigObject();
		$GLOBALS['TYPO3_DB']->admin_query('TRUNCATE TABLE '.tx_piwikintegration_div::getTblName('access'));
		return 'Userrights reseted';
	}
	function getHeader($text) {
		$buffer = '';
		$buffer.= '<table class="typo3-dblist">';
		$buffer.= '<cols><col width="80%"><col width="20%"></cols>';
		$buffer.= '<tr class="t3-row-header"><td colspan="2">'.$text.'</td></tr>';
		return $buffer;
	}
	function getFooter() {
		return '</table>';
	}
	function getButton($func,$piwikNeeded=true) {
		global $LANG;
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . t3lib_div::linkThisScript($params) . "'; return false;";
		
		$button = '<tr class="db_list_normal">';
		$button.= '<td>';
		$button.= '<span class="typo3-dimmed" style="float:right;">['.$func.']</span>';
		$button.= '<b style="float:left;">'.$LANG->getLL('action.'.$func).'</b><br>';
		$button.= '<p>'.$LANG->getLL('desc.'.$func).'</p>';
		$button.= '</td><td>';
			//<a href="javascript:' . htmlspecialchars($onClick) . '">'.$LANG->getLL('DoIt').'</a>
			try{
				if($piwikNeeded) {
					tx_piwikintegration_install::getInstaller()->getConfigObject();
				}
				if(method_exists($this, $func)) {
					$button.= '<input type="submit" value="' . $LANG->getLL('button.DoIt') . '" onclick="' . htmlspecialchars($onClick) . '">';
				} else {
					$button.='<input type="submit" value="' . $LANG->getLL('button.DoIt') . '" onclick="' . htmlspecialchars($onClick) . '" disabled="disabled" title="Method not implemented!">';
				}
			} catch(Exception $e) {
				$button.='<input type="submit" value="' . $LANG->getLL('button.DoIt') . '" onclick="' . htmlspecialchars($onClick) . '" disabled="disabled" title="Piwik not installed!">';
			}
			
		$button.='</td>';
		$button.='</tr>';
		return $button;
	}
	function truncatePiwikDB() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path   = tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
		$tablesInstalled = Piwik::getTablesInstalled();
		$buffer = 'Dropped Tables:';
		foreach($tablesInstalled as $table) {
			$GLOBALS['TYPO3_DB']->admin_query('DROP TABLE `'.$table.'`');
			 $buffer.= $table.', ';
		}
		return $buffer;
	}
	function reInitPiwikDB() {
		$this->truncatePiwikDB();
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path   = tx_piwikintegration_install::getInstaller()->getConfigObject()->installDatabase();
		return 'Tables dropped an recreated';
	}//*/
	function showPiwikConfig() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$path   = tx_piwikintegration_install::getInstaller()->getAbsInstallPath().'piwik/config/config.ini.php';
		$button.= $path;
		$button.= '</b><pre style="width:80%;height:300px;overflow-y:scroll;border:1px solid silver;padding:10px;">';
		$button.= file_get_contents($path);
		$button.= '</pre><b>';
		return $button;
	}
	function enableSuggestedPlugins() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$config =  tx_piwikintegration_install::getInstaller()->getConfigObject();
		$config->enablePlugin('TYPO3Login');
		$config->enablePlugin('TYPO3Menu');
		$config->enablePlugin('SecurityInfo');
		$config->enablePlugin('DBStats');
		$config->enablePlugin('AnonymizeIP');
		$config->disablePlugin('Login');
		return 'installed: TYPO3Login, TYPO3Menu, SecurityInfo, DBStats, AnonymizeIP<br />removed: Login';
	}
	function respectGermanDataPrivacyAct() {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
		$config =  tx_piwikintegration_install::getInstaller()->getConfigObject();
		
		$config->setOption('Tracker','ip_address_mask_length',2);
		$config->setOption('Tracker','cookie_expire',604800);
		
		return 'installed: AnonymizeIP<br />set: Tracker.ip_address_mask_length=2<br />set: Tracker.cookie_expire=604800';
	}
}