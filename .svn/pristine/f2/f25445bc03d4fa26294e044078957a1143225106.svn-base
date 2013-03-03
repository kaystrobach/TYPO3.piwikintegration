<?php

class tx_piwikintegration_Hooks_BeUserProcessing {
	/**
	 * Process changes to a backendusers password to generate a new API key for
	 * piwik
	 */	 	
	function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, &$ref) {
		if($table=='be_users') {
			if(array_key_exists('password', $fieldArray)) {
				$users = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'username,pid',
					'be_users',
					'uid='.intval($id)
				);
				$username = $users[0]['username'];
				$fieldArray['tx_piwikintegration_api_code'] = md5($username.$fieldArray['password']);
			}
		}
	}
}