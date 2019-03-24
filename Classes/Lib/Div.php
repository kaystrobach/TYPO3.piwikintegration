<?php

namespace KayStrobach\Piwikintegration\Lib;

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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * div functions to handle Matomo stuff.
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
class Div
{
    /**
     * @param $uid
     * @param $siteid
     * @param $config
     */
    public function correctTitle($uid, $siteid, $config)
    {
        if ($config['customerRefresh'] && $config['customerName'] && $config['customerRootPid']) {
            $newName = $config['customerName'];
            $newName = str_replace('%siteid%', $config['piwik_idsite'], $newName);
            $page = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('pages', intval($config['customerRootPid']));
            foreach ($page as $key => $value) {
                $newName = str_replace('%'.$key.'%', $value, $newName);
            }
            $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
                $this->tblNm('site'),
                'idsite='.intval($siteid),
                [
                    'name' => $newName,
                ]
            );
        }
    }

    /**
     * @param string $table Matomo tablename without prefix
     *
     * @return string Name of the table with table prefix and prefixed with database
     */
    public static function getDBandTableName($table = '')
    {
        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->initPiwikFrameWork();
        $database = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->getDBName();
        $tablePrefix = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->getTablePrefix();
        if ($database != '') {
            $database = $database.'.';
        }

        return $database.$tablePrefix.$table;
    }

    /**
     * @param string $table Matomo tablename without prefix
     *
     * @return string Name of the table prefixed with database
     *
     * @deprecated Will be removed soon, use getDBandTableName() instead.
     */
    public static function getTblName($table = '')
    {
        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->initPiwikFrameWork();
        $database = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->getDBName();
        $tablePrefix = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->getTablePrefix();
        if ($database != '') {
            $database = '`'.$database.'`.';
        }

        return $database.'`'.$tablePrefix.$table.'`';
    }

    /**
     * @param  $table string Matomo tablename without prefix
     *
     * @return string name of the table prefixed with database
     */
    public function tblNm($table)
    {
        return self::getTblName($table);
    }

    /**
     * returns the Matomo config for a given page
     * call it with $this->pageinfo['uid'] as param from a backend module.
     *
     * @param int $uid Page ID
     *
     * @throws \Exception
     *
     * @return array Matomo config array
     */
    public function getPiwikConfigArray($uid = 0)
    {
        $path = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->initPiwikDatabase();

        if ($uid <= 0 || $uid != intval($uid)) {
            throw new \Exception('Problem with uid in tx_piwikintegration_helper.php::getPiwikSiteIdForPid');
        }

        if (isset($this->piwik_option[$uid])) {
            return $this->piwik_option[$uid];
        }
        //parse ts template
        $template_uid = 0;
        $tmpl = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\ExtendedTemplateService::class);    // Defined global here!

        $tplRow = $tmpl->ext_getFirstTemplate($uid, $template_uid);
        if (is_array($tplRow) || 1) {    // IF there was a template...
            $rootLine = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Utility\RootlineUtility::class, $uid)->get();
            $tmpl->runThroughTemplates($rootLine);    // This generates the constants/config + hierarchy info for the template.
            $tmpl->generateConfig();
            if ($tmpl->setup['config.']['tx_piwik.']['customerPidLevel']) {
                $k = $tmpl->setup['config.']['tx_piwik.']['customerPidLevel'];
                $tmpl->setup['config.']['tx_piwik.']['customerRootPid'] = $rootLine[$k]['uid'];
            }
            if (!$tmpl->setup['config.']['tx_piwik.']['customerRootPid']) {
                $tmpl->setup['config.']['tx_piwik.']['customerRootPid'] = $rootLine[0]['uid'];
            }

            return $this->piwik_option[$uid] = $tmpl->setup['config.']['tx_piwik.'];
        }

        return [];
    }

    /**
     * returns the Matomo site id for a given page
     * call it with $this->pageinfo['uid'] as param from a backend module.
     *
     * @param int $uid: Page ID
     *
     * @return int Matomo site id
     */
    public function getPiwikSiteIdForPid($uid)
    {
        //save time get config
        $r = $this->getPiwikConfigArray($uid);
        if (isset($r['piwik_idsite'])) {
            $id = (int) $r['piwik_idsite'];
        } else {
            $id = 0;
        }
        //check wether site already exists in Matomo db
        $this->makePiwikSiteExisting($id);
        //return
        return $id;
    }

    /**
     * creates Matomo site, if not existing.
     *
     * @param $id
     *
     * @internal param int $siteid : Matomo ID
     *
     * @return int Matomo site id
     */
    public function makePiwikSiteExisting($id)
    {
        if ($id !== 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($this->getDBandTableName('site'));
            $erg = $queryBuilder
                    ->select('*')
                    ->from($this->getDBandTableName('site'))
                    ->where(
                        $queryBuilder->expr()->eq('idsite', (int) $id)
                    )
                    ->setMaxResults(1)
                    ->execute()
                    ->fetchAll();
            if (count($erg) == 0) {
                //FIX currency for current Matomo version, since 0.6.3
                $currency = \Piwik\Option::get('SitesManager_DefaultCurrency') ? \Piwik\Option::get('SitesManager_DefaultCurrency') : 'USD';
                //FIX timezone for current Matomo version, since 0.6.3
                $timezone = \Piwik\Option::get('SitesManager_DefaultTimezone') ? \Piwik\Option::get('SitesManager_DefaultTimezone') : 'UTC';

                $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                    self::getTblName('site'),
                    [
                        'idsite'     => $id,
                        'main_url'   => 'http://'.$_SERVER['SERVER_NAME'],
                        'name'       => 'Customer '.$id,
                        'timezone'   => $timezone,
                        'currency'   => $currency,
                        'ts_created' => date('Y-m-d H:i:s', time()),
                    ]
                );
            }
        }
    }

    /**
     * @param $uid
     *
     * @throws \Exception
     */
    public function correctUserRightsForPid($uid)
    {
        $uid = $this->getPiwikSiteIdForPid($uid);

        $this->correctUserRightsForSiteId($uid);
    }

    /**
     * This function makes a page statistics accessable for a user
     * call it with $this->pageinfo['uid'] as param from a backend module.
     *
     * @param int $uid siteid for which the user will get access
     *
     * @throws \Exception
     */
    public function correctUserRightsForSiteId($uid = 0)
    {
        if ($uid <= 0 || $uid != intval($uid)) {
            throw new \Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
        }
        $beUserName = $GLOBALS['BE_USER']->user['username'];
        /*
         * ensure, that the user is added to the database
         * needed to change user attributes (mail, ...)
         * tx_piwikintegration_user
         */

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->getDBandTableName('user'));
        $erg = $queryBuilder
                ->select('*')
                ->from($this->getDBandTableName('user'))
                ->where(
                    $queryBuilder->expr()->eq('login', $queryBuilder->createNamedParameter($beUserName))
                )
                ->setMaxResults(1)
                ->execute()
                ->fetchAll();
        if ($GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'] === '' || $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'] === null) {
            $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'] = md5(microtime(true));
            $GLOBALS['TYPO3_DB']->exec_Updatequery(
                'be_users',
                'username = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($beUserName, 'be_users').'',
                [
                    'tx_piwikintegration_api_code' => $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'],
                ]
            );
        }

        if (count($erg) != 1) {
            $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                    $this->tblNm('user'),
                    [
                        'login'            => $beUserName,
                        'alias'            => $GLOBALS['BE_USER']->user['realName'] ? $GLOBALS['BE_USER']->user['realName'] : $beUserName,
                        'email'            => $GLOBALS['BE_USER']->user['email'],
                        'date_registered'  => date('Y-m-d H:i:s', time()),
                        'token_auth'       => $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'],
                        'superuser_access' => $GLOBALS['BE_USER']->user['admin'],
                    ]
                );
        } else {
            $GLOBALS['TYPO3_DB']->exec_Updatequery(
                    $this->tblNm('user'),
                    'login = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($beUserName, $this->tblNm('user')).'',
                    [
                        'alias'            => $GLOBALS['BE_USER']->user['realName'] ? $GLOBALS['BE_USER']->user['realName'] : $beUserName,
                        'email'            => $GLOBALS['BE_USER']->user['email'],
                        'token_auth'       => $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code'],
                        'superuser_access' => $GLOBALS['BE_USER']->user['admin'],
                    ]
                );
        }
        /*
         * ensure, that user's right are added to the database
         * tx_piwikintegration_access
         */
        if ($GLOBALS['BE_USER']->user['admin'] != 1) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($this->getDBandTableName('access'));
            $erg = $queryBuilder
                    ->select('*')
                    ->from($this->getDBandTableName('access'))
                    ->where(
                        $queryBuilder->expr()->eq('login', $queryBuilder->createNamedParameter($beUserName))
                    )
                    ->andWhere(
                        $queryBuilder->expr()->eq('idsite', (int) $uid)
                    )
                    ->setMaxResults(1)
                    ->execute()
                    ->fetchAll();
            if (count($erg) == 0) {
                $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                    $this->tblNm('access'),
                    [
                        'login'  => $beUserName,
                        'idsite' => $uid,
                        'access' => 'view',
                    ]
                );
            }
        }
    }
}
