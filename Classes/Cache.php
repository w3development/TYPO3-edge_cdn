<?php
namespace W3DEVELOPMENT\EdgeCdn;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Elvis Tavasja - W3DEVELOPMENT <support@w3development.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
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
 * @author Elvis Tavasja <elvis@w3development.net>
 * @package EdgeCdn
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Cache implements \TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface {

    /**
     * Adds the option to clear all the caches in the back-end clear cache menu.
     *
     * @param array $a_cacheActions
     * @param array $a_optionValues
     * @return void
     * @see typo3/interfaces/backend_cacheActionsHook#manipulateCacheActions($cacheActions, $optionValues)
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues) {
        // // Clear cache for ALL tables!
        if ($GLOBALS['BE_USER']->isAdmin() || $GLOBALS['BE_USER']->getTSConfigVal('options.clearCache.all')) {
              $cacheActions[] = array(
                 'id' => 'cdn',
                 'title' => $GLOBALS['LANG']->sL('LLL:EXT:edge_cdn/Resources/Private/Language/locallang.xlf:flushEdgeCdnCache', TRUE),
        //         'description' => $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xlf:flushGeneralCachesDescription', TRUE),
                 // 'href' => $this->backPath . 'tce_db.php?vC=' . $backendUser->veriCode() . '&cacheCmd=cdn&ajaxCall=1' . BackendUtility::getUrlToken('tceAction'),
                  'href' => 'ajax.php?ajaxID=Cache::flushCDN',
                  'icon' => \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('actions-system-cache-clear-impact-medium')
              );
             $optionValues[] = 'cdn';
        }

    }


    public function flushCDN(){
        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        
        $configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['edge_cdn']);

        
        $token = trim($configuration['token']);
        $account = trim($configuration['account']);
        $mediaPath = trim($configuration['mediaPath']);
        $mediaType = trim($configuration['mediaType']);
        $debug = trim($configuration['mediaType']);


        /* Do not edit below this line */
        if($debug){
            $logger->debug($token);
            $logger->debug($account);
            $logger->debug($mediaPath);
            $logger->debug($mediaType);
        }

        if($token != "" && $account != "" && $mediaPath != "" && $mediaType != "") {
            //Setup variables
            $token = trim($token);
            $mediaPath = trim($mediaPath);
            $mediaType = trim($mediaType);
            $purgeURL = "https://api.edgecast.com/v2/mcc/customers/" . trim($account) . "/edge/purge";

            //Create send data
            $request_params = (object) array(
            'MediaPath' => $mediaPath,
            'MediaType' => $mediaType
            );

            $data = json_encode($request_params);

            //Send the request to Edgecast
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $purgeURL);
            curl_setopt($ch, CURLOPT_PORT , 443);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: tok:'.$token,
                'Content-Type: application/json',
                'Accept: application/json',
                'Content-length: '.strlen($data))
            );

            $head = curl_exec($ch);
            $httpCode = curl_getinfo($ch);
            curl_close($ch);

            //## check if error
            if($debug){

                 if ($httpCode['http_code'] != 200){
                    $logger->debug("Error: <br />" . $head);
                 }
                 else {
                    $logger->debug("Success.  Cache for path ".$mediaPath." has been purged.<br />This message was last updated " . date(DATE_RFC2822) . ".");
                 }
                 
            }
        }
        // else {
        //     print("Error: <br />Not all 4 variables were configured.");
        // }

    }

}