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
 * piwik_patches/plugins/TYPO3Login/Controller.php
 *
 * Authentification controller class
 *
 * $Id: Controller.php 40947 2010-12-08 07:05:46Z kaystrobach $
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
/**
 * Controller for TYPO3Login
 * 
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @package Piwik_TYPO3Login
 */
class Piwik_TYPO3Login_Controller extends Piwik_Login_Controller
{
	/**
	 * redirect to dashboard as we have no view
	 *
	 * @return	void
	 */
	function index()
	{
		$this->login();
	}
}
//XClass to avoid errors in extmanager of TYPO3 - senseless so far
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/piwik_patches/plugins/TYPO3Login/Controller.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/piwikintegration/piwik_patches/plugins/TYPO3Login/Controller.php']);
}

?>