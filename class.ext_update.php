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
 * $Id: class.ext_update.php 66302 2012-09-24 12:06:02Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */


class ext_update {
	function access($what = 'all') {
		return TRUE;
	}
	function main() {
		global $LANG;
		$LANG->includeLLFile('EXT:piwikintegration/Resources/Private/Language/locallang.xml');
		$func = trim(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('func'));
		$buffer = '';
		if(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('do_update')) {
			if (method_exists($this, $func)) {
				try {
					$result = $this->$func();
					$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $result, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
				} catch (Exception $e) {
					$result = $e->getMessage();
					$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $result, '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
				}
				$buffer.= $flashMessage->render();
			} else {
				$buffer.=$LANG->getLL('methodNotFound');
			}
		}

		try {
			tx_piwikintegration_install::getInstaller()->getConfigObject();
		} catch (Exception $e) {
			$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $LANG->getLL('installedPiwikNeeded'), '', \TYPO3\CMS\Core\Messaging\FlashMessage::INFO
			);
			$buffer.= $flashMessage->render();
		}

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
		$buffer.= $this->getButton('renameTables');
		$buffer.= $this->getButton('resetUserRights');
		$buffer.= $this->getButton('deletePiwikTables');
		$buffer.= $this->getButton('createPiwikTables');
		$buffer.= $this->getFooter();
		return $buffer;
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
		$params = array('do_update' => 1, 'func' => $func);
		$onClick = "document.location='" . \TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript($params) . "'; return false;";

		$button = '<tr class="db_list_normal">';
		$button.= '<td>';
		$button.= '<span class="typo3-dimmed" style="float:right;">['.$func.']</span>';
		$button.= '<b style="float:left;">'.$LANG->getLL('action.'.$func).'</b><br>';
		$button.= '<p>'.$LANG->getLL('desc.'.$func).'</p>';
		$button.= '</td><td>';
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
	function installPiwik() {
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->installPiwik();
		return $GLOBALS['LANG']->getLL('action.installPiwik.success');
	}
	function updatePiwik() {
		throw new Exception($GLOBALS['LANG']->getLL('action.updatePiwik.error'));
	}
	function removePiwik() {
		$installer =  tx_piwikintegration_install::getInstaller();
		if(!$installer->removePiwik()) {
			throw new Exception($GLOBALS['LANG']->getLL('action.removePiwik.error'));
		}
		return $GLOBALS['LANG']->getLL('action.removePiwik.success');
	}
	function patchPiwik() {
		$installer =  tx_piwikintegration_install::getInstaller();
		$exclude = array(
			'config/config.ini.php',
		);
		$installer->patchPiwik($exclude);
		return $GLOBALS['LANG']->getLL('action.patchPiwik.success');
	}
	function configurePiwik() {
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->getConfigObject()->makePiwikConfigured();
		return $GLOBALS['LANG']->getLL('action.configurePiwik.success');
	}
	function resetUserRights() {
		$installer =  tx_piwikintegration_install::getInstaller();
		$installer->getConfigObject();
		$GLOBALS['TYPO3_DB']->admin_query('TRUNCATE TABLE '.tx_piwikintegration_div::getTblName('access'));
		return $GLOBALS['LANG']->getLL('action.resetUserRights.success');
	}
	function deletePiwikTables() {
		tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();
		$tablesInstalled = \Piwik\DbHelper::getTablesInstalled();
		$buffer = $GLOBALS['LANG']->getLL('action.deletePiwikTables.success');
		foreach($tablesInstalled as $table) {
			$GLOBALS['TYPO3_DB']->admin_query('DROP TABLE `' . $table . '`');
			 $buffer.= $table.', ';
		}
		return $buffer;
	}
	function createPiwikTables() {
		$this->deletePiwikTables();
		tx_piwikintegration_install::getInstaller()->getConfigObject()->installDatabase();
		return $GLOBALS['LANG']->getLL('action.createPiwikTables.success');
	}
	function showPiwikConfig() {
		$path   = tx_piwikintegration_install::getInstaller()->getAbsInstallPath().'piwik/config/config.ini.php';
		$button = $path;
		$button.= '</b><pre style="width:80%;height:300px;overflow-y:scroll;border:1px solid silver;padding:10px;">';
		$button.= file_get_contents($path);
		$button.= '</pre><b>';
		return $button;
	}
	function enableSuggestedPlugins() {
		$config =  tx_piwikintegration_install::getInstaller()->getConfigObject();
		$suggestedPlugins = $config->enableSuggestedPlugins();
		$config->disablePlugin('Login');
		return 'Installed: ' . $suggestedPlugins . '<br />Removed: Login';
	}
	function respectGermanDataPrivacyAct() {
		$config =  tx_piwikintegration_install::getInstaller()->getConfigObject();
		tx_piwikintegration_install::getInstaller()->getConfigObject()->enablePlugin('AnonymizeIP');
		$config->setOption('Tracker','ip_address_mask_length',2);
		$config->setOption('Tracker','cookie_expire',604800);

		return 'Installed: AnonymizeIP<br />Set: Tracker.ip_address_mask_length=2<br />Set: Tracker.cookie_expire=604800';
	}
	function renameTables() {
		$buffer = 'Renamed all tables prepended with tx_piwikintegration to user_piwikintegration:';
		$tablesInstalled = $GLOBALS['TYPO3_DB']->admin_get_tables();
		foreach($tablesInstalled as $table) {
			if(substr($table['Name'], 0, 20) === 'tx_piwikintegration_') {
				$newTableName = str_replace('tx_piwikintegration_', 'user_piwikintegration_', $table['Name']);
				$GLOBALS['TYPO3_DB']->admin_query('RENAME TABLE `'.$table['Name'].'` to `' . $newTableName .'`');
				$buffer.= $table['Name'].', ';
			}
		}
		tx_piwikintegration_install::getInstaller()->getConfigObject()->setOption('database' ,'tables_prefix','user_piwikintegration_');
		return $buffer;
	}
}
