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
 * lib/class.tx_piwikintegration_scheduler_archive.php
 *
 * scheduler task class
 *
 * $Id: class.tx_piwikintegration_scheduler_archive.php 43324 2011-02-09 11:47:35Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

require_once(t3lib_extMgm::extPath('scheduler', 'class.tx_scheduler_task.php'));


/**
 * scheduler task class
 *
 * $Id: class.tx_piwikintegration_scheduler_archive.php 43324 2011-02-09 11:47:35Z kaystrobach $
 * 
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 *
 */
 
 
class tx_piwikintegration_scheduler_archive extends tx_scheduler_Task {
	/**
	 * execute the piwik archive task
	 *
	 * @return	boolean  always returns true
	 */
	public function execute() {
		//set execution time
		ini_set('max_execution_time',0);
		//find piwik
		
		$piwikScriptPath = dirname(dirname(__FILE__)).'/../../piwik/piwik';
		
		file_put_contents('e:\log.txt',$piwikScriptPath);
		
		define('PIWIK_INCLUDE_PATH'         , $piwikScriptPath);
		define('PIWIK_ENABLE_DISPATCH'      , false);
		define('PIWIK_ENABLE_ERROR_HANDLER' , false);
		define('PIWIK_DISPLAY_ERRORS'       , false);
		ini_set('display_errors',0);
		include_once PIWIK_INCLUDE_PATH . "/index.php";
		include_once PIWIK_INCLUDE_PATH . "/core/API/Request.php";
		
		Piwik_FrontController::getInstance()->init();

		$piwikConfig = parse_ini_file($piwikScriptPath.'/config/config.ini.php',true);

		//log
		$this->writeLog(
			'EXT:piwikintegration cronjob'
		);
		//get API key
		$request = new Piwik_API_Request('
			module=API
			&method=UsersManager.getTokenAuth
			&userLogin='.$piwikConfig['superuser']['login'].'
			&md5Password='.$piwikConfig['superuser']['password'].'
			&format=php
			&serialize=0'
		);
		$TOKEN_AUTH = $request->process();

		//get all piwik siteid's
		$request = new Piwik_API_Request('
			module=API
			&method=SitesManager.getSitesWithAdminAccess
			&token_auth='.$TOKEN_AUTH.'
			&format=php
			&serialize=0'
		);
		$piwikSiteIds = $request->process();

		//log
		$this->writeLog(
			'EXT:piwikintegration got '.count($piwikSiteIds).' siteids and piwik token ('.$TOKEN_AUTH.'), start archiving '
		);
		//create Archive in piwik
		$periods = array(
			'day',
			'week',
			'month',
			'year',
		);
		//iterate through sites
		//can be done with allSites, but this cannot create the logentries
		foreach($periods as $period) {
			foreach($piwikSiteIds as $siteId) {
				$starttime = microtime(true);
				$request = new Piwik_API_Request('
				            module=API
							&method=VisitsSummary.getVisits
							&idSite='.intval($siteId['idsite']).'
							&period='.$period.'
							&date=last52
							&format=xml
							&token_auth='.$TOKEN_AUTH.'"
				');
				$request->process();
				//log
				$this->writeLog(
					'EXT:piwikintegration period '.$period.' ('.$siteId['idsite'].') '.$siteId['name'].' ('.round(microtime(true)-$starttime,3).'s)'
				);
			}
		}
		//log
		$this->writeLog(
			'EXT:piwikintegration cronjob ended'
		);
		return true;
	}

	/**
	 * write something into the logfile
	 *
	 * @param	string		$message: message for the log
	 * @param	mixed		$data: mixed data to store in the log
	 * @return	void
	 */
	function writeLog($message,$data='') {
		if(!array_key_exists('REMOTE_ADDR',$_SERVER)) {
			$_SERVER['REMOTE_ADDR'] = 'local CLI';
		}
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['piwikintegration']);
		if($conf['enableSchedulerLoging']) {
			$GLOBALS['BE_USER']->writeLog(
				4,	//extension
				0,	//no categorie
				0,	//message
				0,	//messagenumber
				$message,
				$data
			);
		}
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/lib/class.tx_piwikintegration_scheduler_archive.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/lib/class.tx_piwikintegration_scheduler_archive.php']);
}

?>