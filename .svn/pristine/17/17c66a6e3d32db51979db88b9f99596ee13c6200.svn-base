<?php

abstract class Piwik_KSVisitorImport_Import_Abstract {
	
	public $rows = 0;
	
	/**
	 * GET parameters array of values to be used for the current visit
	 */
	protected $currentget = array();
	
	/**
	 * Unix timestamp to use for the generated visitor 
	 *
	 * @var int Unix timestamp
	 */
	protected $timestampToUse;
	
	/**
	* IdSite to generate visits for (@see setIdSite())
	*
	* @var int
	*/
	public $idSite = 1;
	
	/**
	* Path to read logfile from (@see setPath())
	*
	* @var string
	*/
	public $path = 1;
	
	/**
	 * clientIP
	 *
	 * @var string
	 */
	protected $clientIP = '';

	/**
	 * Number of lines read which weren't page views, because they were 
     * other web assets (images, stylesheets, javascript files) or had
     * non-pageview status codes
	 */
	public $numSkippedRows = 0;
	
	/**
	 * Number of lines read which didn't match expected format.
	 */
	public $numInvalidRows = 0;

	/**
	 * Rows read which didn't match expected format.
     * We only keep the first 20 invalid rows (as examples to help debugging).
	 */
	public $invalidRows = array();

	/**
	 * Parsed records for preview.
	 */
	public $previewRecords = array();

	/**
	 * Set the idsite to generate the visits for
	 * 
	 * @param int idSite
	 */
	public function setIdSite($idSite)
	{
		$this->idSite = $idSite;
	}
	
	/**
	 * Set path to read logfile from
	 * 
	 * @param string path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
	
	public function __construct()
	{
		$_COOKIE = $_GET = $_POST = array();
		
		// init GET and REQUEST to the empty array
		$this->setFakeRequest();
		
		// I am not sure weather we need this here
		Piwik::createConfigObject(PIWIK_USER_PATH . '/config/config.ini.php');
		Zend_Registry::get('config')->disableSavingConfigurationFileUpdates();
		
		$this->timestampToUse = time();
	}
	
	public function getRows()
	{
		return $this->rows;
	}
	
	public function import($mode="import")
	{
		if ($mode == "preview" ) {
			$method = "previewEntry";
		} else {
			$method = "makeEntry"; 
		}
		if(!file_exists($this->path) || !is_file($this->path)) {
			throw new Exception('File doesn´t exist.');
		}
		$this->fileHandle = fopen($this->path,'r');
		while (!feof($this->fileHandle)) {
			$line = fgets($this->fileHandle);
			if($this->lineHandler($line, $method)) {
				$this->rows++;
			} else {
                // could be an invalid row, or end of file.
                if (!feof($this->fileHandle)) {
                    $this->numInvalidRows += 1;
                    if (count($this->invalidRows) < 20) {
                        $this->invalidRows[] = $line;
                    }
                }
			}

            // in preview mode, stop after 10 total rows
			if ($mode == "preview") {
				if (($this->rows + $this->numInvalidRows) >= 10) {
					break;
				}
			}
		}
		fclose ($this->fileHandle);	

        if($mode == "import") {
            $this->deleteArchives();
        }
	}

    
	/**
     * Skip hits which should not be imported into Piwik.
     *
	 * Apache log files contain a row for every resource, including 
     * pages, images, etc.  So a page view might be made up of 1 or more hits
     * depending on how many images and other resources are part of the page.
     *
     * The Piwik tracker only generates one "action" view per page view.
     * So when we are importing records from a different system, we should
     * filter out all non-page hits (images, stylesheets, javascript files).
     *
     * Also, Piwik tracker only sees an action if the page was delivered 
     * succesfully to the browser (otherwise the javascript tracker will 
     * not run).  So when importing rows we should also filter out hits
     * whose HTTP status code shows there was no page viewed.
	 */
    protected function skipEntry(array $entry)
    {
        // skip images, css, javascript
		preg_match('/^\/.*\.(jpg|jpeg|gif|png|css|js)$/i', $entry['url'], $matches);
		if(is_array($matches) && array_key_exists(0, $matches)) {
            return true;
        }

        // skip rows based on HTTP status code.
        $status = (int)$entry['status'];
        if ($status >= 200 && $status <= 299) {
            // these are success codes, don't skip.
            return false;
        } else if ($status >= 300 && $status <= 399) {
            switch ($status) {
                case 301:
                case 302:
                case 303:
                case 305:
                case 307:
                    // skip redirects, etc.
                    return true;
                    break;

                default:
                    return false;
            }
        } else if ($status >= 400 && $status <= 599) {
            // 4XX are client errors; 5XX are server errors
            return true;
        }
        return false;
    }

	protected function makeEntry(array $entry)
	{
        if ($this->skipEntry($entry)) {
            $this->numSkippedRows += 1;
            return;
        }
		$_SERVER['HTTP_USER_AGENT'] = $entry['userAgent'];
		$this->clientIP = $entry['remoteHost'];
		$this->timestampToUse = $entry['unixTimestamp'];
		$this->setCurrentRequest( 'idsite', $this->idSite);
		$this->setCurrentRequest('rec', 1);
		$this->saveVisit();
	}

	protected function previewEntry(array $entry)
	{
		$this->previewRecords[] = $entry;
	}
	
	/**
	 * Saves the visit 
	 * - replaces GET and REQUEST by the fake generated request
	 * - load the Tracker class and call the method to launch the recording
	 * 
	 * This will save the visit in the database
	 */
	protected function saveVisit()
	{
		$this->setFakeRequest();
		$process = new Piwik_KSVisitorImport_Tracker();
		$process->setForceIp($this->clientIP);
		$process->setForceDateTime($this->timestampToUse);
		$process->main();
		unset($process);
	}	
	
	/**
	 * Sets the _GET and _REQUEST superglobal to the current generated array of values.
	 * @see setCurrentRequest()
	 * This method is called once the current action parameters array has been generated from 
	 * the global parameters array
	 */
	protected function setFakeRequest()
	{
		$_GET = $this->currentget;
	}
	
	/**
	 * Sets a value in the current action request array.
	 * 
	 * @param string Name of the parameter to set
	 * @param string Value of the parameter
	 */
	protected function setCurrentRequest($name,$value)
	{
		$this->currentget[$name] = $value;
	}
	
	public function emptyLogTables()
	{
		$db = Zend_Registry::get('db');
        $piwik_log_action = Piwik_Common::prefixTable('log_action');
        $piwik_log_link_visit_action = Piwik_Common::prefixTable('log_link_visit_action');
        $piwik_log_visit = Piwik_Common::prefixTable('log_visit');

        # drop temporary table if it exists -- this might be necessary if
        # mysql_pconnect (persistent connect) is used since in this case temporary
        # tables won't get dropped automatically.
        $db->query("drop temporary table if exists ksvi_delete_actions;");

        # store idactions of all actions linked to the current site's visitors
        $db->query("create temporary table ksvi_delete_actions select idaction from $piwik_log_action where idaction in (select idaction_url from $piwik_log_link_visit_action lva, $piwik_log_visit v where lva.idvisit = v.idvisit and v.idsite = " . $this->idSite . ") or idaction in (select idaction_name from $piwik_log_link_visit_action lva, $piwik_log_visit v where lva.idvisit = v.idvisit and v.idsite = " . $this->idSite . ")");

        # delete visits and visit_actions
        $db->query("delete from $piwik_log_link_visit_action where idvisit in (select idvisit from $piwik_log_visit where idsite = " . $this->idSite . ")");
        $db->query("delete from $piwik_log_visit where idsite = " . $this->idSite);

        # now drop all actions which were referenced by the current site's visitors but
        # which are not referenced by any other visitors.
        $db->query("delete from $piwik_log_action where idaction in (select idaction from ksvi_delete_actions) and idaction not in (select idaction_url from $piwik_log_link_visit_action) and idaction not in (select idaction_name from $piwik_log_link_visit_action)");
	}

	/**
	 * Delete all archives from the current site (this->idSite), since 
     * otherwise the new visits we have imported will not be reflected
     * in the reports.
	 */
    public function deleteArchives()
    {
		$db = Zend_Registry::get('db');
        $oldmode = $db->getFetchMode();
        $db->setFetchMode(Zend_Db::FETCH_NUM);
		$query = $db->query('SHOW TABLES');

        $prefix = Piwik_Common::prefixTable('archive_');
		while($row = $query->fetch() )
		{
            $table = $row[0];
            if (strpos($table, $prefix) !== false) {
                $db->query("delete from $table where idsite = " . $this->idSite);
            }
        }
        $db->setFetchMode($oldmode); // restore the mode
    }
}
?>