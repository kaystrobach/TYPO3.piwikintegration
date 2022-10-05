<?php

namespace KayStrobach\Piwikintegration\Controller;

/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  (c) 2014 - 2022 Christopher Stelmaszyk
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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tx_Piwikintegration_Controller_PiwikController.
 *
 * is the backend controller
 */
class PiwikController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var \KayStrobach\Piwikintegration\Lib\
     */
    protected $piwikHelper = null;

    /**
     * @var int
     */
    protected $id = 0;

    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected IconFactory $iconFactory;
    protected PageRenderer $pageRenderer;

    public function __construct(
        IconFactory $iconFactory,
        PageRenderer $pageRenderer,
        ModuleTemplateFactory $moduleTemplateFactory
    ) {
        $this->iconFactory = $iconFactory;
        $this->pageRenderer = $pageRenderer;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * Generates the action menu.
     */
    protected function initializeModuleTemplate(ServerRequestInterface $request): ModuleTemplate
    {
        $menuItems = [
            'index' => [
                'controller' => 'Piwik',
                'action'     => 'index',
                'label'      => 'Dashboard', // @ToDo: Use Language Labels!
            ],
            /* Comment out; gives PHP error
               Call to a member function getPluginName() on bool in core/Theme.php line 39
               and is not needed.
            'apiCode' => [
                'controller' => 'Piwik',
                'action' => 'apiCode',
                'label' => 'Trackingcode',
            ],*/
            'help' => [
                'controller' => 'Piwik',
                'action'     => 'help',
                'label'      => 'Help',
            ],
        ];

        $moduleTemplate = $this->moduleTemplateFactory->create($request);

        $menu = $moduleTemplate->getDocHeaderComponent()
            ->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('PiwikintegrationModuleMenu');

        $context = '';
        foreach ($menuItems as $menuItemConfig) {
            $isActive = $this->request->getControllerActionName() === $menuItemConfig['action'];
            $menuItem = $menu->makeMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($this->uriBuilder->reset()->uriFor($menuItemConfig['action'], [], $menuItemConfig['controller']))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
            if ($isActive) {
                $context = $menuItemConfig['label'];
            }
        }

        $moduleTemplate->getDocHeaderComponent()
        ->getMenuRegistry()->addMenu($menu);
        $moduleTemplate->setTitle(
            'Matomo',
            $context
        );

        return $moduleTemplate;
    }

    /**
     * @return void
     */
    public function initializeAction()
    {
        $GLOBALS['LANG']->includeLLFile('EXT:piwikintegration/Resources/Private/Language/locallang.xlf');
        $this->id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
        $this->piwikHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'KayStrobach\\Piwikintegration\\Lib\\Div'
        );
    }

    /**
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        if ($this->checkPiwikEnvironment()) {
            $piwikSiteId = $this->piwikHelper->getPiwikSiteIdForPid($this->id);
            $this->view->assign('mainURL', '//'.$_SERVER['SERVER_NAME']);
            $this->view->assign('piwikSiteId', $piwikSiteId);
            $this->piwikHelper->correctUserRightsForSiteId($piwikSiteId);
            $this->piwikHelper->correctTitle($this->id, $piwikSiteId, $this->piwikHelper->getPiwikConfigArray($this->id));
        }

        $moduleTemplate = $this->initializeModuleTemplate($this->request);
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    /**
     * shows the api code.
     *
     * @return ResponseInterface
     */
    public function apiCodeAction(): ResponseInterface
    {
        $this->view->assign('piwikApiCode', $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code']);

        $tracker = GeneralUtility::makeInstance(
            'KayStrobach\\Piwikintegration\\Tracking\\Tracking'
        );

        $this->view->assign('piwikBaseUri', $tracker->getPiwikBaseURL());
        $this->view->assign('piwikTrackingCode', $tracker->getPiwikJavaScriptCodeForPid($this->id));

        $moduleTemplate = $this->initializeModuleTemplate($this->request);
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    /**
     * shows some help information.
     *
     * @return ResponseInterface
     */
    public function helpAction(): ResponseInterface
    {
        $moduleTemplate = $this->initializeModuleTemplate($this->request);
        $moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    /**
     * checks the Matomo environment.
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function checkPiwikEnvironment()
    {
        // check if Matomo is installed
        if (!\KayStrobach\Piwikintegration\Lib\Install::getInstaller()->checkInstallation()) {
            \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->installPiwik();
            if (\KayStrobach\Piwikintegration\Lib\Install::getInstaller()->checkInstallation()) {
                $this->addFlashMessage(
                    'Matomo installed',
                    'Matomo is now installed / upgraded, wait a moment, reload the page ;) <meta http-equiv="refresh" content="2; URL=mod.php?M=web_txpiwikintegrationM1&uid='.$this->id.'#reload">',
                    \TYPO3\CMS\Core\Messaging\FlashMessage::OK
                );
            }

            return false;
        }
        // check whether a page is selected
        if (!$this->id) {
            $this->addFlashMessage(
                $GLOBALS['LANG']->getLL('desc.selectPage'),
                '',
                \TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE
            );

            return false;
        }
        $t = $this->piwikHelper->getPiwikConfigArray($this->id);
        // check whether a configured page is selected
        if (!isset($t['piwik_idsite']) || !$this->piwikHelper->getPiwikSiteIdForPid($this->id)) {
            $this->addFlashMessage(
                'Page is not configured. Did you include the Typoscript template?',
                '',
                \TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE
            );

            return false;
        }
        // check whether piwik_host is correct
        if (($t['piwik_host'] !== 'typo3conf/piwik/piwik/') && ($t['piwik_host'] !== '/typo3conf/piwik/piwik/')) {
            $this->addFlashMessage(
                'Matomo host is not set correctly',
                '',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );

            return false;
        }
        unset($t);
        // check if patch level is correct
        if (!\KayStrobach\Piwikintegration\Lib\Install::getInstaller()->checkPiwikPatched()) {
            //prevent lost configuration and so the forced repair.
            $exclude = [
                'config/config.ini.php',
            ];
            \KayStrobach\Piwikintegration\Lib\Install::getInstaller()->patchPiwik($exclude);
        }

        return true;
    }
}
