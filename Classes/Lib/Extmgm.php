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

/**
 * functions for the extmgm render forms and react on changes.
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
class Extmgm
{
    /**
     * @param $params
     *
     * @return string
     */
    public function emMakeDBList($params)
    {
        if (!\KayStrobach\Piwikintegration\Lib\Install::getInstaller()->checkInstallation()) {
            return 'Piwik is not installed yet ;) - option is unavailable';
        }
        /* Pull the current fieldname and value from constants */
        $fieldName = $params['fieldName'];
        $fieldValue = $params['fieldValue'];
        $dbs = $GLOBALS['TYPO3_DB']->admin_get_dbs();
        $buffer = '<select name="'.$fieldName.'">';
        $buffer .= '<option value="'.TYPO3_db.'">---TYPO3DB---</option>';
        foreach ($dbs as $db) {
            $buffer .= '<option value="'.htmlspecialchars($db).'"';
            if ($db == $fieldValue) {
                $buffer .= ' selected="selected"';
            }
            $buffer .= '>'.htmlspecialchars($db).'</option>';
        }
        $buffer .= '</select>';

        return $buffer;
    }

    /**
     * @param $par
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function emSaveConstants($par)
    {
        if ($par['extKey'] == 'piwikintegration' && \TYPO3\CMS\Core\Utility\GeneralUtility::_POST('submit')) {
            $newconf = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST();
            $newconf = $newconf['data'];
            //init Matomo to get table prefix
            //$this->initPiwik();

            if (!\KayStrobach\Piwikintegration\Lib\Install::getInstaller()->checkInstallation()) {
                return 'Problem moving database, Piwik is not installed ...';
            }

            $old_database = Install::getInstaller()->getConfigObject()->getOption('database', 'dbname');
            $new_database = $newconf['databaseTablePrefix'];
            $this->table_prefix = Install::getInstaller()->getConfigObject()->getOption('database', 'table_prefix');
            //walk through changes
            if ($old_database !== $new_database) {
                //create shortVars
                if ($new_database == '') {
                    $new_database = TYPO3_db;
                }
                //get tablenames and rename tables
                $suffix = '';
                if ($old_database != '') {
                    $suffix = ' FROM `'.$old_database.'`';
                }
                $erg = $GLOBALS['TYPO3_DB']->admin_query('SHOW TABLES'.$suffix);
                while (false !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($erg))) {
                    if (substr($row[0], 0, 20) == 'tx_piwikintegration_') {
                        $GLOBALS['TYPO3_DB']->admin_query(
                                'RENAME TABLE `'.$old_database.'`.`'.$row[0].'`
								 TO `'.$new_database.'`.`'.$row[0].'`');
                    }
                }
                //change config
                $conf = \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getConfigObject();
                $conf->setOption('database', 'tables_prefix', 'tx_piwikintegration_');
                $conf->setOption('database', 'dbname', $newconf['databaseTablePrefix']);
                $conf->setOption('database', 't3dbname', TYPO3_db);
            }
        }
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function emMakeHeader($params)
    {
        $GLOBALS['LANG']->includeLLFile('EXT:piwikintegration/Resources/Private/Language/locallang.xml');
        $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Core\Messaging\\FlashMessage',
            $GLOBALS['LANG']->getLL('extmgm.noticeText'),
            $GLOBALS['LANG']->getLL('extmgm.noticeHeader'),
            \TYPO3\CMS\Core\Messaging\FlashMessage::INFO
        );

        return $flashMessage->render();
    }
}
