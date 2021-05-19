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
 * class.ext_update.php.
 *
 * extmgm update script
 *
 * $Id: class.ext_update.php 66302 2012-09-24 12:06:02Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
class ext_update
{
    public function access($what = 'all')
    {
        return true;
    }

    public function main()
    {
        global $LANG;
        $LANG->includeLLFile('EXT:piwikintegration/Resources/Private/Language/locallang.xml');
        $func = trim(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('func'));
        $buffer = '';
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('do_update')) {
            if (method_exists($this, $func)) {
                try {
                    $result = $this->$func();
                    $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $result, '', \TYPO3\CMS\Core\Messaging\FlashMessage::OK);
                } catch (Exception $e) {
                    $result = $e->getMessage();
                    $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $result, '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
                }
                $buffer .= $flashMessage->render();
            } else {
                $buffer .= $LANG->getLL('methodNotFound');
            }
        }

        try {
            \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject();
        } catch (Exception $e) {
            $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
                $LANG->getLL('installedPiwikNeeded'),
                '',
                \TYPO3\CMS\Core\Messaging\FlashMessage::INFO
            );
            $buffer .= $flashMessage->render();
        }

        $buffer .= $this->getHeader($LANG->getLL('header.installation'));
        $buffer .= $this->getButton('installPiwik', false);
        $buffer .= $this->getButton('updatePiwik');
        $buffer .= $this->getButton('removePiwik');
        $buffer .= $this->getFooter();

        $buffer .= $this->getHeader($LANG->getLL('header.configuration'));
        $buffer .= $this->getButton('patchPiwik');
        $buffer .= $this->getButton('configurePiwik');
        $buffer .= $this->getButton('enableSuggestedPlugins');
        $buffer .= $this->getButton('respectGermanDataPrivacyAct');
        $buffer .= $this->getButton('showPiwikConfig');
        $buffer .= $this->getFooter();

        $buffer .= $this->getHeader($LANG->getLL('header.database'));
        $buffer .= $this->getButton('renameTables');
        $buffer .= $this->getButton('resetUserRights');
        $buffer .= $this->getButton('deletePiwikTables');
        $buffer .= $this->getButton('createPiwikTables');
        $buffer .= $this->getFooter();

        return $buffer;
    }

    public function getHeader($text)
    {
        $buffer = '';
        $buffer .= '<table class="typo3-dblist">';
        $buffer .= '<cols><col width="80%"><col width="20%"></cols>';
        $buffer .= '<tr class="t3-row-header"><td colspan="2">'.$text.'</td></tr>';

        return $buffer;
    }

    public function getFooter()
    {
        return '</table>';
    }

    public function getButton($func, $piwikNeeded = true)
    {
        global $LANG;
        $params = ['do_update' => 1, 'func' => $func];
        $onClick = "document.location='".\TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript($params)."'; return false;";

        $button = '<tr class="db_list_normal">';
        $button .= '<td>';
        $button .= '<span class="typo3-dimmed" style="float:right;">['.$func.']</span>';
        $button .= '<b style="float:left;">'.$LANG->getLL('action.'.$func).'</b><br>';
        $button .= '<p>'.$LANG->getLL('desc.'.$func).'</p>';
        $button .= '</td><td>';

        try {
            if ($piwikNeeded) {
                \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject();
            }
            if (method_exists($this, $func)) {
                $button .= '<input type="submit" value="'.$LANG->getLL('button.DoIt').'" onclick="'.htmlspecialchars($onClick).'">';
            } else {
                $button .= '<input type="submit" value="'.$LANG->getLL('button.DoIt').'" onclick="'.htmlspecialchars($onClick).'" disabled="disabled" title="Method not implemented!">';
            }
        } catch (Exception $e) {
            $button .= '<input type="submit" value="'.$LANG->getLL('button.DoIt').'" onclick="'.htmlspecialchars($onClick).'" disabled="disabled" title="Matomo not installed!">';
        }

        $button .= '</td>';
        $button .= '</tr>';

        return $button;
    }

    public function installPiwik()
    {
        $installer = \KayStrobach\Piwikintegration\Lib\Install::getInstaller();
        $installer->installPiwik();

        return $GLOBALS['LANG']->getLL('action.installPiwik.success');
    }

    public function updatePiwik()
    {
        throw new Exception($GLOBALS['LANG']->getLL('action.updatePiwik.error'));
    }

    public function removePiwik()
    {
        $installer = \KayStrobach\Piwikintegration\Lib\Install::getInstaller();
        if (!$installer->removePiwik()) {
            throw new Exception($GLOBALS['LANG']->getLL('action.removePiwik.error'));
        }

        return $GLOBALS['LANG']->getLL('action.removePiwik.success');
    }

    public function patchPiwik()
    {
        $installer = \KayStrobach\Piwikintegration\Lib\Install::getInstaller();
        $exclude = [
            'config/config.ini.php',
        ];
        $installer->patchPiwik($exclude);

        return $GLOBALS['LANG']->getLL('action.patchPiwik.success');
    }

    public function configurePiwik()
    {
        $installer = \KayStrobach\Piwikintegration\Lib\Install::getInstaller();
        $installer->getConfigObject()->makePiwikConfigured();

        return $GLOBALS['LANG']->getLL('action.configurePiwik.success');
    }

    public function resetUserRights()
    {
        $installer = \KayStrobach\Piwikintegration\Lib\Install::getInstaller();
        $installer->getConfigObject();
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(\KayStrobach\Piwikintegration\Lib\Div::getDBandTableName('access'));
        $connection->truncate(\KayStrobach\Piwikintegration\Lib\Div::getDBandTableName('access'));

        return $GLOBALS['LANG']->getLL('action.resetUserRights.success');
    }

    public function deletePiwikTables()
    {
        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->initPiwikDatabase();
        $tablesInstalled = \Piwik\DbHelper::getTablesInstalled();
        $buffer = $GLOBALS['LANG']->getLL('action.deletePiwikTables.success');
        foreach ($tablesInstalled as $table) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
            $connection->prepare('DROP TABLE `'.$table.'`')
                ->execute();
            $buffer .= $table.', ';
        }

        return $buffer;
    }

    public function createPiwikTables()
    {
        $this->deletePiwikTables();
        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->installDatabase();

        return $GLOBALS['LANG']->getLL('action.createPiwikTables.success');
    }

    public function showPiwikConfig()
    {
        $path = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getAbsInstallPath().'piwik/config/config.ini.php';
        $button = $path;
        $button .= '</b><pre style="width:80%;height:300px;overflow-y:scroll;border:1px solid silver;padding:10px;">';
        $button .= file_get_contents($path);
        $button .= '</pre><b>';

        return $button;
    }

    public function enableSuggestedPlugins()
    {
        $config = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject();
        $suggestedPlugins = $config->enableSuggestedPlugins();
        $config->disablePlugin('Login');

        return 'Installed: '.$suggestedPlugins.'<br />Removed: Login';
    }

    public function respectGermanDataPrivacyAct()
    {
        $config = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject();
        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->enablePlugin('AnonymizeIP');
        $config->setOption('Tracker', 'ip_address_mask_length', 2);
        $config->setOption('Tracker', 'cookie_expire', 604800);

        return 'Installed: AnonymizeIP<br />Set: Tracker.ip_address_mask_length=2<br />Set: Tracker.cookie_expire=604800';
    }

    public function renameTables()
    {
        $buffer = 'Renamed all tables prepended with tx_piwikintegration to user_piwikintegration:';
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('be_users')->getSchemaManager();
        $tablesInstalled = $connection->listTableNames();
        foreach ($tablesInstalled as $table) {
            if (substr($table, 0, 20) === 'tx_piwikintegration_') {
                $newTableName = str_replace('tx_piwikintegration_', 'user_piwikintegration_', $table);
                $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
                $connection->prepare('RENAME TABLE `'.$table.'` to `'.$newTableName.'`')
                    ->execute();
                $buffer .= $table.', ';
            }
        }
        \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject()->setOption('database', 'tables_prefix', 'user_piwikintegration_');

        return $buffer;
    }
}
