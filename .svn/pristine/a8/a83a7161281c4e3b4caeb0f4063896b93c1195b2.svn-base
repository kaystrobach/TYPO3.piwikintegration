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
 * lib/class.tx_piwikintegration_div.php
 *
 * div functions to handle piwik stuff
 *
 * $Id$
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
class tx_piwikintegration_div {
    function correctTitle($uid,$siteid,$config) {
		if($config['customerRefresh'] && $config['customerName'] && $config['customerRootPid']) {
			$newName = $config['customerName'];
			$newName = str_replace('%siteid%',$config['piwik_idsite'],$newName);
			$page = t3lib_BEfunc::getRecord('pages',intval($config['customerRootPid']));
			foreach($page as $key=>$value) {
				$newName = str_replace('%'.$key.'%',$value,$newName);
			}
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				$this->tblNm('site'),
				'idsite='.intval($siteid),
				array(
					'name' => $newName
				)
			);
		}
	}
	/**
     * @param  $table piwik tablename without prefix
     * @return string name of the table prefixed with database
     *
     */
    static function getTblName($table) {
		tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikFrameWork();
        $database = tx_piwikintegration_install::getInstaller()->getConfigObject()->getDBName();
        $tablePrefix = tx_piwikintegration_install::getInstaller()->getConfigObject()->getTablePrefix();
        if($database != '') {
            $database = '`'.$database.'`.';
        }
        return $database.'`'.$tablePrefix.$table.'`';
    }
    /**
     * @param  $table piwik tablename without prefix
     * @return string name of the table prefixed with database
     *
     */
    function tblNm($table) {
        return self::getTblName($table);
    }
	/**
	 * returns the piwik config for a given page
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: Page ID
	 * @return	array    	piwik config array
	 */
	function getPiwikConfigArray($uid) {
		include_once(t3lib_extMgm::extPath('piwikintegration', 'lib/class.tx_piwikintegration_install.php'));
        $path              = tx_piwikintegration_install::getInstaller()->getConfigObject()->initPiwikDatabase();

		if($uid <= 0 || $uid!=intval($uid)) {
			throw new Exception('Problem with uid in tx_piwikintegration_helper.php::getPiwikSiteIdForPid');
		}

		if(isset($this->piwik_option[$uid])) {
			return $this->piwik_option[$uid];
		}
		//parse ts template
			$template_uid = 0;
			$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");	// Defined global here!
			$tmpl->tt_track = 0;	// Do not log time-performance information
			$tmpl->init();

			$tplRow = $tmpl->ext_getFirstTemplate($uid,$template_uid);
			if (is_array($tplRow) || 1)	{	// IF there was a template...
				$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
				$rootLine = $sys_page->getRootLine($uid);
				$tmpl->runThroughTemplates($rootLine);	// This generates the constants/config + hierarchy info for the template.
				$tmpl->generateConfig();
				if($tmpl->setup['config.']['tx_piwik.']['customerPidLevel']) {
					$k = $tmpl->setup['config.']['tx_piwik.']['customerPidLevel'];
					$tmpl->setup['config.']['tx_piwik.']['customerRootPid'] = $rootLine[$k]['uid'];
				}
				if(!$tmpl->setup['config.']['tx_piwik.']['customerRootPid']) {
					$tmpl->setup['config.']['tx_piwik.']['customerRootPid'] = $rootLine[0]['uid'];
				}
				return $this->piwik_option[$uid] = $tmpl->setup['config.']['tx_piwik.'];
			}
		return array();
	}
	
	/**
	 * returns the piwik site id for a given page
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: Page ID
	 * @return	integer     piwik site id
	 */
	function getPiwikSiteIdForPid($uid) {
		//save time get config
			$r = $this->getPiwikConfigArray($uid);
			if($r['piwik_idsite']) {
				$id = intval($r['piwik_idsite']);
			} else {
				$id = 0;
			}
		//check wether site already exists in piwik db
			$this->makePiwikSiteExisting($id);
		//return
			return $id;
	}
	/**
	 * creates piwik site, if not existing
	 *
	 * @param	integer		$siteid: Piwik ID
	 * @return	integer     piwik site id
	 */
	function makePiwikSiteExisting($id) {
		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			tx_piwikintegration_div::getTblName('site'),
			'idsite = '.intval($id),
			'',
			'',
			'0,1'
		);
		if(count($erg)==0) {
			//FIX currency for current Piwik version, since 0.6.3
			$currency = Piwik_GetOption('SitesManager_DefaultCurrency') ? Piwik_GetOption('SitesManager_DefaultCurrency') : 'USD';
			//FIX timezone for current Piwik version, since 0.6.3
			$timezone = Piwik_GetOption('SitesManager_DefaultTimezone') ? Piwik_GetOption('SitesManager_DefaultTimezone') : 'UTC';
			
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				tx_piwikintegration_div::getTblName('site'),
				array(
					'idsite'     => $id,
					'main_url'   => 'http://'.$_SERVER["SERVER_NAME"],
					'name'       => 'Customer '.$id,
					'timezone'   => $timezone,
					'currency'   => $currency,
					'ts_created' => date('Y-m-d H:i:s',time()),
				)
			);
		}
	}
	function correctUserRightsForPid($uid) {
		$uid = $this->getPiwikSiteIdForPid($uid);
		return $this->correctUserRightsForSiteId($uid);
	}
	/**
	 * This function makes a page statistics accessable for a user
	 * call it with $this->pageinfo['uid'] as param from a backend module
	 *
	 * @param	integer		$uid: siteid for which the user will get access
	 * @return	void
	 */
	function correctUserRightsForSiteId($uid) {
		//t3lib_div::debug('Add access grants for:'.$uid);
		if($uid <= 0 || $uid!=intval($uid)) {
			throw new Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
		}
		$beUserName = $GLOBALS['BE_USER']->user['username'];
		/**
		 * ensure, that the user is added to the database
		 * needed to change user attributes (mail, ...)	
		 * tx_piwikintegration_user		 	 
		 */		 		

		$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->tblNm('user'),
			'login="'.$beUserName.'"',
			'',
			'',
			'0,1'
			);
		if(count($erg)!=1) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					$this->tblNm('user'),
					array(
						'login'           => $beUserName,
						'alias'           => $GLOBALS['BE_USER']->user['realName'] ? $GLOBALS['BE_USER']->user['realName'] : $beUserName,
						'email'           => $GLOBALS['BE_USER']->user['email'],
						'date_registered' => date('Y-m-d H:i:s',time()),
						'token_auth'      => $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'],
					)
				);
		} else {
			$GLOBALS['TYPO3_DB']->exec_Updatequery(
					$this->tblNm('user'),
					'login = "'.mysql_escape_string($beUserName).'"',
					array(
						'alias'           => $GLOBALS['BE_USER']->user['realName'] ? $GLOBALS['BE_USER']->user['realName'] : $beUserName,
						'email'           => $GLOBALS['BE_USER']->user['email'],
						'token_auth'      => $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'],
					)
				);		
		}
		/**
		 * ensure, that user's right are added to the database
		 * tx_piwikintegration_access		 
		 */
		if($GLOBALS['BE_USER']->user['admin']!=1) {
			$erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					$this->tblNm('access'),
					'login="'.$beUserName.'" AND idsite='.$uid,
					'',
					'',
					'0,1'
			);
			if(count($erg)==0) {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					$this->tblNm('access'),
					array(
						'login' => $beUserName,
						'idsite'=> $uid,
						'access'=> 'view',
					)
				);
			}
		}
	}
}