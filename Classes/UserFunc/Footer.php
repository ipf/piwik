<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Kay Strobach (typo3@kay-strobach.de)
*  (c) 2009 Ulrich Wuensche (wuensche@drwuensche.de),
*
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Based on B-Net1 Piwik plugin implementation, old piwik plugin and piwik2a
 * Provides Interface to get the new piwiktrackingcode 
 * 
 * Hooks for the 'piwik' extension.
 *
 * @author Ulrich Wuensche <wuensche@drwuensche.de>
 * @author Joerg Winter <winter@b-net1.de>
 * @author Kay Strobach <typo3@kay-strobach.de>
 * @author Ingo Pfennigstorf <i.pfennigstorf@gmail.com>
 */
class tx_Piwik_UserFunc_Footer {
	public $cObj;
	private $tc = array();
	/**
	 * write piwik javascript right before </body> tag
	 * JS Documentation on http://piwik.org/docs/javascript-tracking/	 
	 * 
	 * Idea piwikTracker.setDownloadClasses( "download" ); should be set to the default download class of TYPO3
	 * Idea Track TYPO3 404 Errors ... http://piwik.org/faq/how-to/#faq_60	 
	 *
	 * @param string $content
	 * @param array $conf Plugin configuration
	 * @return string
	 */
	function contentPostProc_output($content, $conf){
		// process the page with these options
		$conf = $GLOBALS['TSFE']->tmpl->setup['config.']['tx_piwik.'];
		$beUserLogin = $GLOBALS['TSFE']->beUserLogin;
		
			//check wether there is a BE User loggged in, if yes avoid to display the tracking code!
			//check wether needed parameters are set properly
		if ((!$conf['piwik_idsite']) || (!$conf['piwik_host'])) {
				//fetch the js template file, makes editing easier ;)
			$extConf = unserialize($GLOBALS['$TYPO3_CONF_VARS']['EXT']['extConf']['piwik']);
			if($extConf['showFaultyConfigHelp']) {
				$template = t3lib_div::getURL(t3lib_extMgm::extPath('piwik') . 'Resources/Private/Templates/Piwik/notracker.html');
			} else {
				return '';
			}
		} elseif($beUserLogin == 1) {
			$template = t3lib_div::getURL(t3lib_extMgm::extPath('piwik') . 'Resources/Private/Templates/Piwik/notracker_beuser.html');
		}else {
				//fetch the js template file, makes editing easier ;)
			$template = t3lib_div::getURL(t3lib_extMgm::extPath('piwik') . 'Resources/Private/Templates/Piwik/tracker.html');
		}
		
			//make options accessable in the whole class
		$this->piwikOptions = $conf;

		$trackingCode = '';

			//build trackingCode
		$trackingCode .= $this->getPiwikEnableLinkTracking();
		$trackingCode .= $this->getPiwikDomains();
		$trackingCode .= $this->getLinkTrackingTimer();
		$trackingCode .= $this->getPiwikSetDownloadExtensions();
		$trackingCode .= $this->getPiwikAddDownloadExtensions();
		$trackingCode .= $this->getPiwikActionName();
		$trackingCode .= $this->getPiwikTrackGoal();
		$trackingCode .= $this->getPiwikSetIgnoreClasses();
		$trackingCode .= $this->getPiwikSetDownloadClasses();
		$trackingCode .= $this->getPiwikSetLinkClasses();
		$trackingCode .= "\t\t" . 'piwikTracker.trackPageView();';

			//replace placeholders
			//currently the function $this->getPiwikHost() is not called, because of piwikintegration?!
		$template = str_replace('###TRACKEROPTIONS###', $trackingCode, $template);
		$template = str_replace('###HOST###', $this->getPiwikHost(), $template);
		$template = str_replace('###IDSITE###', $this->getPiwikIDSite(), $template);
		$template = str_replace('###BEUSER###', $beUserLogin, $template);

			//add complete piwikcode to frontend
		return $template;
	}

	/**
	 * a stub for backwards compatibility with extending classes that might use it
	 *
	 * @return bool always false
	 */
	function is_backend() {
		return false;
	}

	/**
	 * Generates piwikTracker.trackGoal javascript code
	 *
	 * @return string piwikTracker.trackGoal javascript code
	 */
	function getPiwikTrackGoal() {
		$trackGoal = '';
		if (strlen($this->piwikOptions['trackGoal'])) {
			$trackGoal = 'piwikTracker.trackGoal(' . $this->piwikOptions['trackGoal'] . ');' . "\n";
		}
		return $trackGoal;
	}

	/**
	 * Generates piwikTracker.setDocumentTitle javascript code
	 *
	 * @return string piwikTracker.setDocumentTitle javascript code
	 */
	function getPiwikActionName() {
		$piwikActionName = '';
		if ((strtoupper($this->piwikOptions['actionName']) == 'TYPO3') && !($this->piwikOptions['actionName.'])) {
			$piwikActionName = 'piwikTracker.setDocumentTitle("' . $GLOBALS['TSFE']->cObj->data['title'] . '");' . "\n";
		}

		if (strlen($this->piwikOptions['actionName'])) {
			$cObject = t3lib_div::makeInstance('tslib_cObj');
			$actionName = $cObject->stdWrap($this->piwikOptions['actionName'], $this->piwikOptions['actionName.']);
			$piwikActionName = 'piwikTracker.setDocumentTitle("' . $actionName . '");' . "\n";
		}
		return $piwikActionName;
	}

	/**
	 * Generates piwikTracker.setDownloadExtensions javascript code
	 *
	 * @return string piwikTracker.setDownloadExtensions javascript code
	 */
	function getPiwikSetDownloadExtensions() {
		$downloadExtensions = '';
		if (strlen($this->piwikOptions['setDownloadExtensions'])) {
			$downloadExtensions =  'piwikTracker.setDownloadExtensions( "' . $this->piwikOptions['setDownloadExtensions'] . '" );' . "\n";
		}
		return $downloadExtensions;
	}

	/**
	 * Generates piwikTracker.addDownloadExtensions javascript code
	 *
	 * @return string piwikTracker.addDownloadExtensions javascript code
	 */
	function getPiwikAddDownloadExtensions() {
		$downloadExtensions = '';
		if (strlen($this->piwikOptions['addDownloadExtensions'])) {
			$downloadExtensions = 'piwikTracker.addDownloadExtensions( "' . $this->piwikOptions['addDownloadExtensions'] . '" );' . "\n";
		}
		return $downloadExtensions;
	}

	/**
	 * Generates piwikTracker.setDomains javascript code
	 *
	 * @return string piwikTracker.setDomains javascript code
	 */
	function getPiwikDomains() {
		$domains = '';
		if (strlen($this->piwikOptions['setDomains'])) {
			$hosts = t3lib_div::trimExplode(',', $this->piwikOptions['setDomains']);
			for ($i = 0; $i < count($hosts); $i++) {
				$hosts[$i] = '"' . $hosts[$i] . '"';
			}
			$domains = 'piwikTracker.setDomains([' . implode(', ', $hosts) . ']);' . "\n";
		}
		return $domains;
	}

	/**
	 * Generates piwikTracker.setLinkTrackingTimer javascript code
	 *
	 * @return string piwikTracker.setLinkTrackingTimer javascript code
	 */
	function getLinkTrackingTimer() {
		$trackingTimer = '';
		if (strlen($this->piwikOptions['setLinkTrackingTimer'])) {
			$trackingTimer = 'piwikTracker.setLinkTrackingTimer(' . $this->piwikOptions['setLinkTrackingTimer'] . ');' . "\n";
		}
		return $trackingTimer;
	}

	/**
	 * Generates piwikTracker.enableLinkTracking javascript code
	 *
	 * @return string piwikTracker.enableLinkTracking javascript code
	 */
	function getPiwikEnableLinkTracking() {
		$linkTracking = '';
		if ($this->piwikOptions['enableLinkTracking'] != 0) {
			$linkTracking = 'piwikTracker.enableLinkTracking();' . "\n";
		}
		return $linkTracking;
	}

	/**
	 * Generates piwikTracker.setIgnoreClasses javascript code
	 *
	 * @return string piwikTracker.setIgnoreClasses javascript code
	 */
	function getPiwikSetIgnoreClasses() {
		$ignoreClasses = '';
		if (strlen($this->piwikOptions['setIgnoreClasses'])) {
			$ignoreClasses = 'piwikTracker.setIgnoreClasses("' . $this->piwikOptions['setIgnoreClasses'] . '");' . "\n";
		}
		return $ignoreClasses;
	}

	/**
	 * Generates piwikTracker.setDownloadClasses javascript code
	 *
	 * @return string piwikTracker.setDownloadClasses javascript code
	 */
	function getPiwikSetDownloadClasses() {
		$downloadClasses = '';
		if (strlen($this->piwikOptions['setDownloadClasses'])) {
			$downloadClasses = 'piwikTracker.setDownloadClasses("' . $this->piwikOptions['setDownloadClasses'] . '");' . "\n";
		}
		return $downloadClasses;
	}

	/**
	 * Generates piwikTracker.setLinkClasses javascript code
	 *
	 * @return string piwikTracker.setLinkClasses javascript code
	 */
	function getPiwikSetLinkClasses() {
		$linkClasses = '';
		if (strlen($this->piwikOptions['setLinkClasses'])) {
			$linkClasses =  'piwikTracker.setLinkClasses("' . $this->piwikOptions['setLinkClasses'] . '");' . "\n";
		}
		return $linkClasses;
	}

	/**
	 * Gets Piwik SiteID
	 *
	 * @return int Piwik SiteID
	 */
	function getPiwikIDSite() {
		return intval($this->piwikOptions['piwik_idsite']);
	}

	/**
	 * Gets Piwik Host-URL
	 *
	 * @return string Piwik Host-URL
	 */
	function getPiwikHost() {
		$piwikHost = trim($this->piwikOptions['piwik_host']);
		$match = '|^http[s]*:\/\/|';
		$piwikHost = preg_replace($match, '', $piwikHost);
		return '//' . $piwikHost;
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwik/class.tx_piwik.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/piwik/class.tx_piwik.php"]);
}

?>