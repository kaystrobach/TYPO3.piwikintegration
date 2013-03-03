<?php

class tx_piwikintegration_Hooks_BeUserProcessing {
	/**
	 * Process changes to a backendusers password to generate a new API key for
	 * piwik
	 */	 	
	function processDatamap_preProcessFieldArray (&$status, $table, $id, &$fieldArray) {
		if($table=='be_users') {
			if(array_key_exists('password', $status)) {
				$users = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'username,pid',
					'be_users',
					'uid='.intval($id)
				);
				$username = $users[0]['username'];
				$status['tx_piwikintegration_api_code'] = md5($username.$status['password']);
			}
		}
	}
}