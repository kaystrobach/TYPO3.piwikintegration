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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * pi1/tx_piwikintegration_flexform.
 *
 * helper for the flexform
 *
 * $Id: class.tx_piwikintegration_flexform.php 57008 2012-01-30 14:56:56Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
class tx_piwikintegration_flexform
{
    public $tablePrefix = null;

    public function init()
    {
        $this->tablePrefix = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->getTablePrefix();
    }

    public function getSitesForFlexForm(&$PA, &$fobj)
    {
        $this->init();
        //fetch anonymous accessable idsites
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(\KayStrobach\Piwikintegration\Lib\Div::getDBandTableName('access'));
        $erg = $queryBuilder
            ->select('idsite')
            ->from(\KayStrobach\Piwikintegration\Lib\Div::getDBandTableName('access'))
            ->where(
                $queryBuilder->expr()->eq('login', $queryBuilder->createNamedParameter('anonymous'))
            )
            ->execute()
            ->fetchAll();

        //build array for selecting more information
        $sites = [];
        foreach ($erg as $site) {
            $sites[] = $site['idsite'];
        }
        $accessableSites = implode(',', $sites);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(\KayStrobach\Piwikintegration\Lib\Div::getDBandTableName('site'))->createQueryBuilder();
        $erg = $queryBuilder
            ->select('idsite', 'name', 'main_url')
            ->from(\KayStrobach\Piwikintegration\Lib\Div::getDBandTableName('site'))
            ->where(
                'idsite IN ('.$queryBuilder->createNamedParameter($accessableSites).')'
            )
            ->orderBy('name', 'main_url', 'idsite')
            ->execute();
        $PA['items'] = [];

        //render items
        while ($site = $erg->fetch()) {
            $PA['items'][] = [
                $site['idsite'].' : '.($site['name'] ? $site['name'].' : '.$site['main_url'] : $site['main_url']),
                $site['idsite'],
                'i/domain.gif',
            ];
        }
    }

    public static function getWidgetsForFlexForm(&$PA, &$fobj)
    {
        $PA['items'] = [];

        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->initPiwikDatabase();
        $controller = Piwik_FrontController::getInstance()->init();
        $_GET['idSite'] = 1;
        $widgets = Piwik_GetWidgetsList();

        foreach ($widgets as $pluginCat => $plugin) {
            foreach ($plugin as $widget) {
                $PA['items'][] = [
                    $pluginCat.' : '.$widget['name'],
                    base64_encode(json_encode($widget['parameters'])),
                    'i/catalog.gif',
                ];
            }
        }
    }
}
