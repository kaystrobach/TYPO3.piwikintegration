<?php

namespace KayStrobach\Piwikintegration\Controller;

/**
 * Class Tx_Piwikintegration_Controller_PiwikInstallationController
 *
 * controller to run the installation of piwik in several seperated steps to avoid timeouts
 */
class PiwikInstallationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->id = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
	}

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->showAndRedirect('download', 'Downloaded');
	}

	/**
	 * @return void
	 */
	public function downloadAction() {
		$this->showAndRedirect('patch', 'Patched');
	}

	/**
	 * @return void
	 */
	public function patchAction() {
		$this->showAndRedirect('configure', 'Configured');
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @return void
	 */
	public function configureAction() {
		$this->redirect('apiCode', 'Piwik');
	}

	/**
	 * @param $action
	 * @param $message
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @return void
	 */
	protected function showAndRedirect($action, $message) {
		$this->flashMessageContainer->add(
			$message
		);
		$this->redirect(
			$action,
			NULL,
			NULL,
			NULL,
			$this->id,
			10
		);
	}
}