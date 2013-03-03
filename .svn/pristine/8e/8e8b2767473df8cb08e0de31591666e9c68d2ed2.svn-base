<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: Visit.php 2968 2010-08-20 15:26:33Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_VisitorGenerator
 */

/**
 * Fake Piwik_Tracker_Visit class that overwrite all the Time related method to be able
 * to setup a given timestamp for the generated visitor and actions.
 * 
 * @package Piwik_VisitorGenerator
 */
class Piwik_KSVisitorImport_Visit extends Piwik_Tracker_Visit
{
	// since we force the timestamp to the Tracker class there is no more need for the time related stuff here
	
	public function __construct($forcedIpString = null, $forcedDateTime = null)
	{
		$this->timestamp = time();
		if(!empty($forcedDateTime))
		{
			//$this->timestamp = strtotime($forcedDateTime); // only change compared to the Visit class is here ($forcedDateTime is a unix timestamp)
															 // so it would make sense to use fullDateTime and get rid of Piwik_KSVisitorImport_Visit
			$this->timestamp = $forcedDateTime;
		}
		$ipString = $forcedIpString;
		if(empty($ipString))
		{
			$ipString = Piwik_Common::getIpString();
		}
		
		$this->ipString = Piwik_Common::getIp($ipString);
	}
	
	protected function updateCookie()
	{
		@parent::updateCookie();
	}	
}
