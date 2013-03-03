<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: Tracker.php 2968 2010-08-20 15:26:33Z vipsoft $
 * 
 * @category Piwik_Plugins
 * @package Piwik_VisitorGenerator
 */

/**
 * Fake Piwik_Tracker that:
 * - overwrite the sendHeader method so that no headers are sent.
 * - doesn't print the 1pixel transparent GIF at the end of the visit process
 * - overwrite the Tracker Visit object to use so we use our own Tracker_visit @see Piwik_Tracker_Generator_Visit
 * 
 * @package Piwik_VisitorGenerator
 */
class Piwik_KSVisitorImport_Tracker extends Piwik_Tracker
{
	/**
	 * Does nothing instead of sending headers
	 */
	protected function sendHeader($header)
	{
	}
	
	/**
	 * Does nothing instead of displaying a 1x1 transparent pixel GIF
	 */
	protected function end()
	{
	}
	
	/**
	 * Returns our 'generator home made' Piwik_VisitorGenerator_Visit object.
	 *
	 * @return Piwik_VisitorGenerator_Visit
	 */
	 // if we dont need Piwik_KSVisitorImport_Visit no more, we wont need this method either
	protected function getNewVisitObject()
	{
		$visit = null;
		Piwik_PostEvent('Tracker.getNewVisitObject', $visit);
	
		if(is_null($visit))
		{
			$visit = new Piwik_KSVisitorImport_Visit( self::$forcedIpString, self::$forcedDateTime );
		}
		elseif(!($visit instanceof Piwik_Tracker_Visit_Interface ))
		{
			throw new Exception("The Visit object set in the plugin must implement Piwik_Tracker_Visit_Interface");
		}
		return $visit;
	}	
	
	static function disconnectDatabase()
	{
		return;
	}
}
