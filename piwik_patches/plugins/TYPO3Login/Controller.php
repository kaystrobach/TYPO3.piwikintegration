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

namespace Piwik\Plugins\TYPO3Login;

/**
 * piwik_patches/plugins/TYPO3Login/Controller.php.
 *
 * Authentification controller class
 *
 * @author Kay Strobach <typo3@kay-strobach.de>
 */
/**
 * Controller for TYPO3Login.
 *
 * @author  Kay Strobach <typo3@kay-strobach.de>
 *
 * @link http://kay-strobach.de
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 */
class Controller extends \Piwik\Plugins\Login\Controller
{
    /**
     * redirect to dashboard as we have no view.
     *
     * @return void
     */
    public function index()
    {
        $this->login();
    }

    /**
     * @param null $messageNoAccess
     * @param bool $infoMessage
     */
    public function login($messageNoAccess = null, $infoMessage = false)
    {
        // Login function
    }

    /**
     * @param null $errorMessage
     */
    public function ajaxNoAccess($errorMessage)
    {
        // Ajax function
    }
}
