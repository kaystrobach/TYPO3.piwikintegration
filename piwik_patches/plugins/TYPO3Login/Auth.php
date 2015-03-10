<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 	Kay Strobach (typo3@kay-strobach.de),
 *
 *  All rights reserved
 *
 *  This script is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; version 2 of the License.
 * 
 *   The GNU General Public License can be found at
 *   http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
namespace Piwik\Plugins\TYPO3Login;

	/**
 *  Fix some problems with external DB usage  
 */ 
/**
 * for version 3.0.9
 */

	if(!defined('TYPO3_MODE')) {
		define('TYPO3_MODE', 'BE');
	}
	if(file_exists(PIWIK_INCLUDE_PATH.'/../../LocalConfiguration.php')) {
		$TYPO3config = include(PIWIK_INCLUDE_PATH.'/../../LocalConfiguration.php');
		define('TYPO3DB', $TYPO3config['DB']['database']);
	} elseif(file_exists(PIWIK_INCLUDE_PATH.'/../../localconf.php')) {
		include(PIWIK_INCLUDE_PATH.'/../../localconf.php');
		define('TYPO3DB',$typo_db);
	} else {
		throw new \Exception('Can´t include TYPO3-config file');
	}

/**
 * Provide authentification service against TYPO3 for piwik
 *
 * @author  Kay Strobach <typo3@kay-strobach.de>
 * @link http://kay-strobach.de
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @package Piwik_TYPO3Login
 */
class Auth implements \Piwik\Auth
{
    /**
     * The login used to authenticate.
     *
     * @var string
     */
	protected $login = null;

    /**
     * token_auth parameter used to authenticate in the API
     *
     * @var string
     */
	protected $token_auth = null;

	/**
	 * returns extension name
	 *
	 * @return string extensionname
	 */
	public function getName()
	{
		return 'TYPO3Login';
	}
	protected function getTableName($table, $isT3Table=true) {
		if(array_key_exists('t3dbname', \Piwik\Config::getInstance()->database)) {
			$t3database = \Piwik\Config::getInstance()->database['t3dbname'];
		} else {
			$t3database = \Piwik\Config::getInstance()->database['dbname'];
		}
		
		$prefix = \Piwik\Config::getInstance()->database['tables_prefix'];
		if(!$isT3Table) {
			$table = '`'.$prefix.$table.'`';
		} elseif($t3database!='') {
			$table = '`'.$t3database.'`.`'.$table.'`';
		} else {
			$table = '`'.$table.'`';
		}
		return $table;
	}

	/**
     * Authenticates a user using the login and password set using the setters. Can also authenticate
     * via token auth if one is set and no password is set.
	 *
	 * @return AuthResult
	 */
	public function authenticate() {
		/***********************************************************************
		 * authenticate against the piwik configuration file for emergency access or installer or cronjob!
		 */		 		
			$rootLogin	= \Piwik\Config::getInstance()->superuser['login'];
			$rootPassword = \Piwik\Config::getInstance()->superuser['password'];
		/**
		 * Fix http://forge.typo3.org/issues/37167
		 */
			$userManager = \Piwik\Plugins\UsersManager\API::getInstance();
			$rootToken = $userManager->getTokenAuth($rootLogin, $rootPassword);
	
			if($this->login == $rootLogin
				&& $this->token_auth == $rootToken)
			{
				return new \Piwik\AuthResult(\Piwik\AuthResult::SUCCESS_SUPERUSER_AUTH_CODE, $this->login, $this->token_auth );
			}
	
			if($this->token_auth === $rootToken)
			{
				return new \Piwik\AuthResult(\Piwik\AuthResult::SUCCESS_SUPERUSER_AUTH_CODE, $rootLogin, $rootToken );
			}
		/***********************************************************************
		 * Handle login types
		 */
			$beUserId = false;
			//catch normal logins (login form)
			if((array_key_exists('token_auth',$_REQUEST)) &&($_REQUEST['token_auth']!='')) {
				// fetch UserId, if token is set
				$beUserId = \Piwik\Db::get()->fetchOne(
							'SELECT uid FROM '.$this->getTableName('be_users').' WHERE tx_piwikintegration_api_code = ?',
							array($_REQUEST['token_auth'])
				);
				#print_r($beUserId);
			//catch typo3 logins
			} elseif(array_key_exists('be_typo_user',$_COOKIE)) {
				$beUserCookie = $_COOKIE['be_typo_user'];
				$beUserId = \Piwik\Db::get()->fetchOne(
							'SELECT ses_userid FROM '.$this->getTableName('be_sessions').' WHERE ses_id = ?',
							array($beUserCookie)
				);
			//catch apikey logins
			} elseif($this->token_auth && $this->token_auth!='anonymous') {
				$beUserId = \Piwik\Db::get()->fetchOne(
						'SELECT uid FROM '.$this->getTableName('be_users').' WHERE tx_piwikintegration_api_code = ?',
						array($this->token_auth)
				);
			} else {
				$beUserId=false;
			}
		/***********************************************************************
		 * init user from db
		 */		 		
			if($beUserId!==false) {
				// getUserName
				$beUserName = \Piwik\Db::get()->fetchOne(
							'SELECT username FROM '.$this->getTableName('be_users').' WHERE uid = ?',
							array($beUserId)
				);
				// get isAdmin
				$beUserIsAdmin = \Piwik\Db::get()->fetchOne(
							'SELECT admin FROM '.$this->getTableName('be_users').' WHERE uid = ?',
							array($beUserId)
				);
				// is superuser?
				if($beUserIsAdmin == 1) {
					return new \Piwik\AuthResult(\Piwik\AuthResult::SUCCESS_SUPERUSER_AUTH_CODE, $beUserName, NULL );
				}
				//normal user?
				return new \Piwik\AuthResult(\Piwik\AuthResult::SUCCESS, $beUserName, NULL );
			}

		/***********************************************************************
		 * authenticate anonymous user
		 */
			if($this->login == 'anonymous') {
				return new \Piwik\AuthResult(\Piwik\AuthResult::SUCCESS, 'anonymous', NULL );
			}		 		
		/***********************************************************************
		 * no valid user
		 */		 		
			return new \Piwik\AuthResult(\Piwik\AuthResult::FAILURE, $this->login, $this->token_auth );
	}

	/**
	 * Returns the login of the user being authenticated.
	 *
	 * @return string
	 */
	public function getLogin() {
		return $this->login;
	}

    /**
     * Sets the login name to authenticate with.
     *
     * @param string $login The username.
     */
	public function setLogin($login) {
		$this->login = $login;
	}

    /**
     * Returns the secret used to calculate a user's token auth.
     *
     * A users token auth is generated using the user's login and this secret. The secret
     * should be specific to the user and not easily guessed. Piwik's default Auth implementation
     * uses an MD5 hash of a user's password.
     *
     * @return string
     * @throws Exception if the token auth secret does not exist or cannot be obtained.
     */
	public function getTokenAuthSecret() {
		return $this->md5Password;
	}

    /**
     * Sets the authentication token to authenticate with.
     *
     * @param string $token_auth authentication token
     */
	public function setTokenAuth($token_auth) {
		$this->token_auth = $token_auth;
	}

    /**
     * Sets the password to authenticate with.
     *
     * @param string $password Password (not hashed).
     */
	public function setPassword($password) {
		$this->md5Password = md5($password);
	}

    /**
     * Sets the hash of the password to authenticate with. The hash will be an MD5 hash.
     *
     * @param string $passwordHash The hashed password.
     * @throws Exception if authentication by hashed password is not supported.
     */
	public function setPasswordHash($passwordHash) {
		if (strlen($passwordHash) != 32) {
			throw new Exception("Invalid hash: incorrect length " . strlen($passwordHash));
		}

		$this->md5Password = $passwordHash;
	}

	static function getTokenAuth($login, $md5Password) {
		$token = \Piwik\Db::get()->fetchOne(
						'SELECT ' . self::getTableName('api_code') . ' FROM `be_users` WHERE username = ?',
						array($login)
			);
		if(md5(substr($token,0,6)) == $md5Password) {
			return $token;
		} else {
			return '';
		}
	}

	/**
	 * Authenticates the user and initializes the session.
	 */
	public function initSession($login, $md5Password, $rememberMe) {
		$this->authenticate();
		// TODO: Implement initSession() method.
	}
}
