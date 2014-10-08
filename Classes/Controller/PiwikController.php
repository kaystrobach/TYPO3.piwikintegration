<?php


class Tx_Piwikintegration_Controller_PiwikController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	/**
	 * @var tx_piwikintegration_div
	 */
	protected $piwikHelper = NULL;

	/**
	 * @var int
	 */
	protected $id = 0;

	public function initializeAction() {
		$this->id = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
		$this->piwikHelper = t3lib_div::makeInstance('tx_piwikintegration_div');
	}

	public function indexAction() {
		if($this->checkPiwikEnvironment()) {
			$piwikSiteId   = $this->piwikHelper->getPiwikSiteIdForPid($this->id);
			if((int)$piwikSiteId !== 0) {
				$this->view->assign('piwikSiteId', $piwikSiteId);
				$this->piwikHelper->correctUserRightsForSiteId($piwikSiteId);
				$this->piwikHelper->correctTitle($this->id,$piwikSiteId,$this->piwikHelper->getPiwikConfigArray($this->id));
			}
		}
	}

	public function apiCodeAction() {
		$this->view->assign('beuser',             $GLOBALS['BE_USER']->user['tx_piwikintegration_api_code']);
		$this->view->assign('piwikBaseUri',       tx_piwikintegration_install::getInstaller()->getBaseUrl());

		$tracker = new tx_piwikintegration_tracking();
		$this->view->assign('piwikTrackingCode',  $tracker->getPiwikJavaScriptCodeForPid($this->id));
	}

	protected function checkPiwikEnvironment() {
		global $LANG;
		// check if piwik is installed
		if(!tx_piwikintegration_install::getInstaller()->checkInstallation()) {
			tx_piwikintegration_install::getInstaller()->installPiwik();
			if(tx_piwikintegration_install::getInstaller()->checkInstallation()) {
				$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					'Piwik installed',
					'Piwik is now installed / upgraded, wait a moment, reload the page ;) <meta http-equiv="refresh" content="2; URL=mod.php?M=web_txpiwikintegrationM1&uid=' .$this->pageinfo['uid']  . '#reload">',
					t3lib_FlashMessage::OK
				);
				t3lib_FlashMessageQueue::addMessage($flashMessage);
			}
			return false;
		}
		// check wether a configured page is selected
		if(!$this->id || !$this->piwikHelper->getPiwikSiteIdForPid($this->id)) {
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				'Please select a page in the pagetree',
				'',
				t3lib_FlashMessage::WARNING
			);
			t3lib_FlashMessageQueue::addMessage($flashMessage);
			return false;
		}
		// check wether piwik_host is correct

		$t = $this->piwikHelper->getPiwikConfigArray($this->id);
		if(($t['piwik_host'] !== 'typo3conf/piwik/piwik/') && ($t['piwik_host'] !== '/typo3conf/piwik/piwik/')) {
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				'Piwik host is not set correctly',
				'',
				t3lib_FlashMessage::ERROR
			);
			t3lib_FlashMessageQueue::addMessage($flashMessage);
			return false;
		}
		unset($t);
		// check if patch level is correct
		if(!tx_piwikintegration_install::getInstaller()->checkPiwikPatched()) {
			//prevent lost configuration and so the forced repair.
			$exclude = array(
				'config/config.ini.php',
			);
			tx_piwikintegration_install::getInstaller()->patchPiwik($exclude);
		}
		return true;
	}
}