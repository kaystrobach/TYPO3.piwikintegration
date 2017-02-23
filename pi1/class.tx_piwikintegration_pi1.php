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
 * pi1/tx_piwikintegration_pi1.php.
 *
 * fe module1
 *
 * $Id: class.tx_piwikintegration_pi1.php 57008 2012-01-30 14:56:56Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */

/**
 * Frontend plugin for piwikintegration.
 *
 * @author  Kay Strobach <typo3@kay-strobach.de>
 *
 * @link http://kay-strobach.de
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */

use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;

class tx_piwikintegration_pi1 extends AbstractPlugin
{
    public $prefixId = 'tx_piwikintegration_pi1';        // Same as class name
    public $scriptRelPath = 'pi1/class.tx_piwikintegration_pi1.php';    // Path to this script relative to the extension dir.
    public $extKey = 'tx_piwikintegration_pi1';    // The extension key.
    public $pi_checkCHash = true;

    /**
     * Main function of the module. Write the content to $this->content.
     *
     * @param string $content: string with current content
     * @param array  $conf:    array with configuration
     *
     * @return void
     */
    public function main($content, $conf)
    {
        $content = $this->init($conf);

        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * initializes the configuration for the plugin and gets the settings from
     * the flexform.
     *
     * @param array $conf: array with TS configuration
     *
     * @return string $content
     */
    public function init($conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();


        $this->extConf['widget'] = json_decode(base64_decode($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'widget')), true);
        $this->extConf['widget']['period'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'period');
        $this->extConf['widget']['moduleToWidgetize'] = $this->extConf['widget']['module'];
        $this->extConf['widget']['actionToWidgetize'] = $this->extConf['widget']['action'];
        $this->extConf['widget']['idSite'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'idsite');
        $this->extConf['height'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'div_height');
        $this->extConf['widget']['date'] = 'yesterday';
        $this->extConf['widget']['viewDataTable'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'viewDataTable');
        unset($this->extConf['widget']['module']);
        unset($this->extConf['widget']['action']);

        $content = '<div id="widgetIframe"><iframe width="100%" height="'.intval($this->extConf['height']).'" src="';
        $content .= \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->getBaseUrl().'index.php?module=Widgetize&action=iframe'.\TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl('', $this->extConf['widget']);
        $content .= '&disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe></div>';

        return $content;
    }
}


