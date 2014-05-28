<?php


class Tx_Piwikintegration_Controller_PiwikInstallationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	public function initializeAction() {

	}


	public function indexAction() {
		$this->showAndRedirect('download', 'Downloaded');
	}

	public function downloadAction() {
		$this->showAndRedirect('patch', 'Patched');
	}

	public function patchAction() {
		$this->showAndRedirect('configure', 'Configured');
	}

	public function configureAction() {
		$this->redirect('apiCode', 'Piwik');
	}

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