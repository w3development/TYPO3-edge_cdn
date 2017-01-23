<?php
if(!defined('TYPO3_MODE')){
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = 'W3DEVELOPMENT\EdgeCdn\Cache';
$TYPO3_CONF_VARS['BE']['AJAX']['Cache::flushCDN'] = 'W3DEVELOPMENT\EdgeCdn\Cache->flushCDN';