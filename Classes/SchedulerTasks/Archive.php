<?php

namespace KayStrobach\Piwikintegration\SchedulerTask;

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
/*
 * lib/class.tx_piwikintegration_scheduler_archive.php.
 *
 * scheduler task class
 *
 * $Id: class.tx_piwikintegration_scheduler_archive.php 43324 2011-02-09 11:47:35Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class Archive extends AbstractTask
{
    /**
     * execute the Matomo archive task.
     *
     * @return bool always returns true
     */
    public function execute()
    {
        //set execution time
        ini_set('max_execution_time', 0);

        //find Matomo
        $piwikScriptPath = dirname(dirname(__FILE__)).'/../../piwik/piwik';

        file_put_contents('e:\log.txt', $piwikScriptPath);

        define('PIWIK_INCLUDE_PATH', $piwikScriptPath);
        define('PIWIK_ENABLE_DISPATCH', false);
        define('PIWIK_ENABLE_ERROR_HANDLER', false);
        define('PIWIK_DISPLAY_ERRORS', false);
        ini_set('display_errors', 0);
        require_once PIWIK_INCLUDE_PATH.'/index.php';
        require_once PIWIK_INCLUDE_PATH.'/core/API/Request.php';

        \Piwik\FrontController::getInstance()->init();

        $piwikConfig = parse_ini_file($piwikScriptPath.'/config/config.ini.php', true);

        //log
        $this->writeLog(
            'EXT:piwikintegration cronjob'
        );
        //get API key
        $request = new \Piwik\API\Request(
            '
			module=API
			&method=UsersManager.getTokenAuth
			&userLogin='.$piwikConfig['superuser']['login'].'
			&md5Password='.$piwikConfig['superuser']['password'].'
			&format=php
			&serialize=0'
        );
        $tokenAuth = $request->process();

        //get all Matomo siteid's
        $request = new \Piwik\API\Request(
            '
			module=API
			&method=SitesManager.getSitesWithAdminAccess
			&token_auth='.$tokenAuth.'
			&format=php
			&serialize=0'
        );
        $piwikSiteIds = $request->process();

        //log
        $this->writeLog(
            'EXT:piwikintegration got '.count($piwikSiteIds).' siteids and Matomo token ('.$tokenAuth.'), start archiving '
        );
        //create Archive in Matomo
        $periods = [
            'day',
            'week',
            'month',
            'year',
        ];
        //iterate through sites
        //can be done with allSites, but this cannot create the logentries
        foreach ($periods as $period) {
            foreach ($piwikSiteIds as $siteId) {
                $starttime = microtime(true);
                $request = new \Piwik\API\Request('
				            module=API
							&method=VisitsSummary.getVisits
							&idSite='.intval($siteId['idsite']).'
							&period='.$period.'
							&date=last52
							&format=xml
							&token_auth='.$tokenAuth.'"
				');
                $request->process();
                //log
                $this->writeLog(
                    'EXT:piwikintegration period '.$period.' ('.$siteId['idsite'].') '.$siteId['name'].' ('.round(microtime(true) - $starttime, 3).'s)'
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
     * write something into the logfile.
     *
     * @param string $message: message for the log
     * @param mixed  $data:    mixed data to store in the log
     *
     * @return void
     */
    protected function writeLog($message, $data = '')
    {
        if (!array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $_SERVER['REMOTE_ADDR'] = 'local CLI';
        }
        $conf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('piwikintegration');
        if ($conf['enableSchedulerLogging']) {
            $GLOBALS['BE_USER']->writeLog(
                // extension | no categorie | message | messagenumber
                4,
                0,
                0,
                0,
                $message,
                $data
            );
        }
    }
}
