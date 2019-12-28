<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2015-2019 Christopher Stelmaszyk
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

use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuTop;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureTopMenu(MenuTop $menu)
    {
        // Remove logout link from top menu
        $menu->remove('General_Logout');
    }

    public function configureAdminMenu(MenuAdmin $menu)
    {
        if (\Piwik\Plugin\Manager::getInstance()->isPluginActivated('Login')) {
            if (Piwik::hasUserSuperUserAccess()) {
                $systemSettings = new SystemSettings();
                if ($systemSettings->enableBruteForceDetection->getValue()) {
                    $menu->addDiagnosticItem('Login_BruteForceLog', $this->urlForAction('bruteForceLog'), $orderId = 30);
                }
            }
        }
    }
}
