<?php

/**
 * Class Tx_Piwikintegration_Controller_PiwikController
 *
 * is the backend controller
 */
class Tx_Piwikintegration_Controller_PiwikController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	/**
	 * @var tx_piwikintegration_div
	 */
	protected $piwikHelper = NULL;

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->id = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
		$this->piwikHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_piwikintegration_div');
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function indexAction() {
		if ($this->checkPiwikEnvironment()) {
			$piwikSiteId   = $this->piwikHelper->getPiwikSiteIdForPid($this->id);
			$this->view->assign('piwikSiteId', $piwikSiteId);
			$this->piwikHelper->correctUserRightsForSiteId($piwikSiteId);
			$this->piwikHelper->correctTitle($this->id, $piwikSiteId, $this->piwikHelper->getPiwikConfigArray($this->id));
		}
	}

	/**
	 * shows the api code
	 * @return void
	 */
	public function apiCodeAction() {
		$this->view->assign('piwikApiCode', $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code']);
		$tracker = new tx_piwikintegration_tracking();
		$this->view->assign('piwikBaseUri', $tracker->getPiwikBaseURL());
		$this->view->assign('piwikTrackingCode', $tracker->getPiwikJavaScriptCodeForPid($this->id));
	}

	public function helpAction() {

	}
	/**
	 * checks the piwik environment
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function checkPiwikEnvironment() {
		// check if piwik is installed
		if (!tx_piwikintegration_install::getInstaller()->checkInstallation()) {
			tx_piwikintegration_install::getInstaller()->installPiwik();
			if (tx_piwikintegration_install::getInstaller()->checkInstallation()) {
				$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					't3lib_FlashMessage',
					'Piwik installed',
					'Piwik is now installed / upgraded, wait a moment, reload the page ;) <meta http-equiv="refresh" content="2; URL=mod.php?M=web_txpiwikintegrationM1&uid=' . $this->id  . '#reload">',
					\TYPO3\CMS\Core\Messaging\FlashMessage::OK
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($flashMessage);
			}
			return FALSE;
		}
		// check whether a page is selected
		if (!$this->id) {
			$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				't3lib_FlashMessage',
				'Please select a page in the pagetree',
				'',
				\TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE
			);
			\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($flashMessage);
			return FALSE;
		}
		$t = $this->piwikHelper->getPiwikConfigArray($this->id);
		// check whether a configured page is selected
		if (!isset($t['piwik_idsite']) || !$this->piwikHelper->getPiwikSiteIdForPid($this->id)) {
			$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				't3lib_FlashMessage',
				'Page is not configured. Did you include the Typoscript template?',
				'',
				\TYPO3\CMS\Core\Messaging\FlashMessage::NOTICE
			);
			\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($flashMessage);
			return FALSE;
		}
		// check whether piwik_host is correct
		if (($t['piwik_host'] !== 'typo3conf/piwik/piwik/') && ($t['piwik_host'] !== '/typo3conf/piwik/piwik/')) {
			$flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				't3lib_FlashMessage',
				'Piwik host is not set correctly',
				'',
				\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
			);
			\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($flashMessage);
			return FALSE;
		}
		unset($t);
		// check if patch level is correct
		if (!tx_piwikintegration_install::getInstaller()->checkPiwikPatched()) {
			//prevent lost configuration and so the forced repair.
			$exclude = array(
				'config/config.ini.php',
			);
			tx_piwikintegration_install::getInstaller()->patchPiwik($exclude);
		}
		return TRUE;
	}
}