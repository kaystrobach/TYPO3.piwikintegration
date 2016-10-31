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
 * interact with Piwik core after download and unzip.
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
class tx_piwikintegration_config
{
    private static $configObject = null;
    private $installer = null;
    private $initPiwikFramework = false;
    private $initPiwikDb = false;


    private function __construct()
    {
        $this->installer = tx_piwikintegration_install::getInstaller();
        $this->initPiwikFrameWork();
    }

    /**
     * @return tx_piwikintegration_config
     */
    public static function getConfigObject()
    {
        if (self::$configObject == null) {
            self::$configObject = new self();
        }

        return self::$configObject;
    }

    /**
     * @return void
     */
    public function initPiwikFrameWork()
    {
        if ($this->initPiwikFramework) {
            $this->initPiwikFramework = true;

            return;
        }

        //load files from piwik
        if (!defined('PIWIK_INCLUDE_PATH')) {
            define('PIWIK_INCLUDE_PATH', PATH_site.'typo3conf/piwik/piwik/');
            define('PIWIK_USER_PATH', PATH_site.'typo3conf/piwik/piwik/');
        }
        if (!defined('PIWIK_INCLUDE_SEARCH_PATH')) {
            define('PIWIK_INCLUDE_SEARCH_PATH',
                PIWIK_INCLUDE_PATH.'/core'.
                PATH_SEPARATOR.PIWIK_INCLUDE_PATH.'/libs'.
                PATH_SEPARATOR.PIWIK_INCLUDE_PATH.'/plugins'.
                PATH_SEPARATOR.get_include_path());
            @ini_set('include_path', PIWIK_INCLUDE_SEARCH_PATH);
            @set_include_path(PIWIK_INCLUDE_SEARCH_PATH);
        }

        set_include_path(PIWIK_INCLUDE_PATH.
                PATH_SEPARATOR.PIWIK_INCLUDE_PATH.'/libs/'.
                PATH_SEPARATOR.PIWIK_INCLUDE_PATH.'/plugins/'.
                PATH_SEPARATOR.get_include_path());

        require_once PIWIK_INCLUDE_PATH.'libs/upgradephp/upgrade.php';
        require_once PIWIK_INCLUDE_PATH.'vendor/autoload.php';

        // create root container
        $environment = new \Piwik\Application\Environment(null);
        $environment->init();

        //create config object
        try {
            $config = \Piwik\Config::getInstance();
            $config->getInstance()->init();
        } catch (\Exception $e) {
        }
    }

    /**
     * @param bool $noLoadConfig
     *
     * @return void
     */
    public function initPiwikDatabase($noLoadConfig = false)
    {
        $this->initPiwikFrameWork();
        if ($this->initPiwikDb) {
            $this->initPiwikDb = true;

            return;
        }
        \Piwik\Db::createDatabaseObject();
    }

    /**
     * @return void
     */
    public function makePiwikConfigured()
    {
        $this->initPiwikFrameWork();
        //userdata
        $this->setOption('superuser', 'login', md5(microtime()));
        $this->setOption('superuser', 'password', md5(microtime()));
        $this->setOption('superuser', 'email', $GLOBALS['BE_USER']->user['email']);

        //Database
        $hostAndPort = explode(':', TYPO3_db_host);
        if (count($hostAndPort) == 2) {
            $this->setOption('database', 'host', $hostAndPort[0]);
            $this->setOption('database', 'port', $hostAndPort[1]);
        } else {
            $this->setOption('database', 'host', TYPO3_db_host);
        }

        $this->setOption('database', 'username', TYPO3_db_username);
        $this->setOption('database', 'password', TYPO3_db_password);
        $this->setOption('database', 'dbname', TYPO3_db);
        $this->setOption('database', 'tables_prefix', 'user_piwikintegration_');
        $this->setOption('database', 'adapter', 'PDO_MYSQL');

        //General
        $this->setOption('General', 'show_website_selector_in_user_interface', 0);
        $this->setOption('General', 'serve_widget_and_data', 0);
        $this->setOption('General', 'piwik_professional_support_ads_enabled', 0);

        //Disable the frame detection of Piwik
        $this->setOption('General', 'enable_framed_pages', 1);
        $this->setOption('General', 'enable_framed_logins', 1);
        $this->setOption('General', 'enable_framed_settings', 1);

        //init all plugins

        //set Plugins
        $this->disablePlugin('ExampleAPI');
        $this->disablePlugin('ExampleCommand');
        $this->disablePlugin('ExamplePlugin');
        $this->disablePlugin('ExampleReport');
        $this->disablePlugin('ExampleRssWidget');
        $this->disablePlugin('ExampleSettingsPlugin');
        $this->disablePlugin('ExampleTheme');
        $this->disablePlugin('ExampleTracker');
        $this->disablePlugin('ExampleUI');
        $this->disablePlugin('ExampleVisualization');
        $this->disablePlugin('Login');
        $this->disablePlugin('ProfessionalServices');
        $this->enableSuggestedPlugins();

        //create PiwikTables, check wether base tables already exist
        $this->installDatabase();
    }

    /**
     * enables the suggested plugins.
     *
     * @return string
     */
    public function enableSuggestedPlugins()
    {
        $pluginsToActivate = [
            'TYPO3Login',
            'Morpheus',
            'Actions',
            'Annotations',
            'Contents',
            'CustomVariables',
            'Dashboard',
            'DevicesDetection',
            'Goals',
            'ImageGraph',
            'Insights',
            'Live',
            'MobileMessaging',
            'MultiSites',
            'Overlay',
            'PrivacyManager',
            'Provider',
            'Referrers',
            'ScheduledReports',
            'SegmentEditor',
            'SEO',
            'Transitions',
            'UserCountry',
            'UserCountryMap',
            'VisitFrequency',
            'VisitorInterest',
            'VisitsSummary',
            'VisitTime',
            'Widgetize',
        ];

        foreach ($pluginsToActivate as $plugin) {
            $this->enablePlugin($plugin);
        }

        return implode(', ', $pluginsToActivate);
    }

    /**
     * inits the Piwik DB.
     *
     * @return void
     */
    public function installDatabase()
    {
        $this->initPiwikDatabase(true);
        $tablesInstalled = \Piwik\DbHelper::getTablesInstalled();
        if (count($tablesInstalled) == 0) {
            \Piwik\DbHelper::createTables();
            \Piwik\DbHelper::createAnonymousUser();
            $updater = new \Piwik\Updater();
            //set Piwikversion
            $updater->recordComponentSuccessfullyUpdated('core', \Piwik\Version::VERSION);
        }
    }

    /**
     * This function makes a page statistics accessable for a user
     * call it with $this->pageinfo['uid'] as param from a backend module.
     *
     * @param int $uid : pid for which the user will get access
     *
     * @throws Exception
     *
     * @return void
     */
    public function correctUserRightsForPid($uid)
    {
        $this->initPiwikFrameWork();
        if (($uid <= 0) || ($uid != intval($uid))) {
            throw new \Exception('Problem with uid in tx_piwikintegration_helper.php::correctUserRightsForPid');
        }
        $beUserName = $GLOBALS['BE_USER']->user['username'];
        /*
         * ensure, that user's right are added to the database
         * tx_piwikintegration_access
         */
        if ($GLOBALS['BE_USER']->user['admin'] != 1) {
            $erg = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    '*',
                    tx_piwikintegration_div::getTblName('access'),
                    'login="'.$beUserName.'" AND idsite='.$this->getPiwikSiteIdForPid($uid),
                    '',
                    '',
                    '0,1'
            );
            if (count($erg) === 0) {
                $GLOBALS['TYPO3_DB']->exec_INSERTquery(
                    tx_piwikintegration_div::getTblName('access'),
                    [
                        'login'  => $beUserName,
                        'idsite' => $this->getPiwikSiteIdForPid($uid),
                        'access' => 'view',
                    ]
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix = $this->getOption('database', 'tables_prefix');
    }

    /**
     * @return string
     */
    public function getDBName()
    {
        return $this->dbName = $this->getOption('database', 'dbname');
    }

    /**
     * @return string
     */
    public function getT3DBName()
    {
        return $this->T3DBName = $this->getOption('database', 't3dbname');
    }

    /**
     * @param $sectionName
     * @param $option
     * @param $value
     *
     * @return void
     */
    public function setOption($sectionName, $option, $value)
    {
        $this->initPiwikFrameWork();
        $piwikConfig = \Piwik\Config::getInstance();
        $section = $piwikConfig->$sectionName;
        $section[$option] = $value;
        $piwikConfig->$sectionName = $section;
        $piwikConfig->forceSave();
    }

    /**
     * @param $sectionName
     * @param $option
     *
     * @return mixed
     */
    public function getOption($sectionName, $option)
    {
        $this->initPiwikFrameWork();
        $piwikConfig = \Piwik\Config::getInstance();
        $section = $piwikConfig->$sectionName;

        return $section[$option];
    }

    /**
     * @param $plugin
     *
     * @return void
     */
    public function enablePlugin($plugin)
    {
        $this->initPiwikFrameWork();
        if (!\Piwik\Plugin\Manager::getInstance()->isPluginLoaded($plugin)) {
            try {
                \Piwik\Plugin\Manager::getInstance()->loadActivatedPlugins();
                \Piwik\Plugin\Manager::getInstance()->activatePlugin($plugin);
                //\Piwik\Plugin\Manager::getInstance()->loadPlugins(\Piwik\Plugin\Manager::getInstance()->getActivatedPlugins());
            } catch (Exception $e) {
            }
        }
    }

    /**
     * @param $plugin
     *
     * @return void
     */
    public function disablePlugin($plugin)
    {
        $this->initPiwikFrameWork();
        if (\Piwik\Plugin\Manager::getInstance()->isPluginActivated($plugin)) {
            try {
                \Piwik\Plugin\Manager::getInstance()->deactivatePlugin($plugin);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @param $uid
     *
     * @return string
     *
     * @deprecated
     */
    public function getJsForUid($uid)
    {
        return '--';
    }
}
