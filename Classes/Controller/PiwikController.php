<?php

namespace KayStrobach\Piwikintegration\Controller;

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

    /**
     * @return void
     */
    public function initializeAction()
    {
        $GLOBALS['LANG']->includeLLFile('EXT:piwikintegration/Resources/Private/Language/locallang.xml');
        $this->id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
        $this->piwikHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'KayStrobach\\Piwikintegration\\Lib\\Div'
        );
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->checkPiwikEnvironment()) {
            $piwikSiteId = $this->piwikHelper->getPiwikSiteIdForPid($this->id);
            $this->view->assign('piwikSiteId', $piwikSiteId);
            $this->piwikHelper->correctUserRightsForSiteId($piwikSiteId);
            $this->piwikHelper->correctTitle($this->id, $piwikSiteId, $this->piwikHelper->getPiwikConfigArray($this->id));
        }
    }

    /**
     * shows the api code.
     *
     * @return void
     */
    public function apiCodeAction()
    {
        $this->view->assign('piwikApiCode', $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code']);

        $tracker = GeneralUtility::makeInstance(
            'KayStrobach\\Piwikintegration\\Tracking\\Tracking'
        );

        $this->view->assign('piwikBaseUri', $tracker->getPiwikBaseURL());
        $this->view->assign('piwikTrackingCode', $tracker->getPiwikJavaScriptCodeForPid($this->id));
    }

    public function helpAction()
    {
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
                'Piwik host is not set correctly',
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
