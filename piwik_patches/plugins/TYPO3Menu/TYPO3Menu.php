<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 	Kay Strobach (typo3@kay-strobach.de),
*
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class for adding css and js code to piwik
 *
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @package Piwik_TYPO3Menu
 */
class Piwik_TYPO3Menu extends Piwik_Plugin
{
	/**
	 * get extension information
	 *
	 * @return	array		with information
	 */
	public function getInformation()
	{
		$piwikPatchVersion = '0.0.0';
		include(PIWIK_INCLUDE_PATH.'/piwikintegration.php');
		return array(
			'name' => 'TYPO3NMenu',
			'description' => 'Style Piwik TYPO3 Link.',
			'author' => 'Kay Strobach',
			'homepage' => 'http://kay-strobach.de/',
		    'version' => $piwikPatchVersion,
			);
	}

	/**
	 * returns registered hooks
	 *
	 * @return	array		array of hooks
	 */
	public function getListHooksRegistered()
	{
		return array(
			'AssetManager.getCssFiles' => 'getCssFiles',
			'AssetManager.getJsFiles' => 'getJsFiles'
		);
	}
	function getCssFiles( $notification )
	{		
		$cssFiles = &$notification->getNotificationObject();
		$cssFiles[] = "plugins/TYPO3Menu/css/main.css";
		$cssFiles[] = "plugins/TYPO3Menu/css/typo3.css";
	}
	function getJsFiles( $notification )
	{
		$jsFiles = &$notification->getNotificationObject();
		if($_GET['module']=='CoreHome') {
			$jsFiles[] = "plugins/TYPO3Menu/js/main.js";
		}
	}
	/**
	 * echo the needed javascript
	 *
	 * @return	void
	 */
	function js()
	{
		if($_GET['module']=='CoreHome') {
			echo '<script type="text/javascript" src="plugins/TYPO3Menu/js/main.js"></script>';
		}
	}

	/**
	 * echo the needed stylesheets
	 *
	 * @return	void
	 */
	function css()
	{
		echo "<link rel='stylesheet' type='text/css' href='plugins/TYPO3Menu/css/main.css'>\n";
		echo "<link rel='stylesheet' type='text/css' href='plugins/TYPO3Menu/css/typo3.css'>\n";
	}
}
?>