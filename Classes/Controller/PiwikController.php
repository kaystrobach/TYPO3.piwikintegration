<?php


class Tx_Piwikintegration_Controller_PiwikController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	/**
	 * @var tx_piwikintegration_div
	 */
	protected $piwikHelper = NULL;

	public function initializeAction() {
		$this->piwikHelper = t3lib_div::makeInstance('tx_piwikintegration_div');
	}

	public function indexAction() {
		if($this->content = $this->checkPiwikEnvironment()) {

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
		if(!$this->pageinfo['uid'] || !$this->piwikHelper->getPiwikSiteIdForPid($this->pageinfo['uid'])) {
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$LANG->getLL('selectpage_description'),
				$LANG->getLL('selectpage_tip'),
				t3lib_FlashMessage::NOTICE
			);
			t3lib_FlashMessageQueue::addMessage($flashMessage);
			return false;
		}
		// check wether piwik_host is correct
		$t = $this->piwikHelper->getPiwikConfigArray($this->pageinfo['uid']);
		if(($t['piwik_host'] !== 'typo3conf/piwik/piwik/') && ($t['piwik_host'] !== '/typo3conf/piwik/piwik/')) {
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				$LANG->getLL('config_piwik_host_description'),
				$LANG->getLL('config_piwik_host_tip'),
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