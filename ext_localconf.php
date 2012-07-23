<?php
if (!defined ("TYPO3_MODE")) {
	die ("Access denied.");
}
if(TYPO3_MODE=='FE') {
	require_once(t3lib_extMgm::extPath('piwik').'Classes/User/Func/Footer.php');
} 
?>