<?php

/**
 * Asynchronous processing run common classes
 * 非同期処理実行共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryProcessUtility.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * Asynchronous processing run common classes
 * 非同期処理実行共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryProcessUtility.class.php';

/**
 * Asynchronous processing run common classes
 * 非同期処理実行共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class RepositoryProcessUtility
{
    /**
     * To perform the asynchronous processing
     * 非同期処理を実行する
     *
     * @param string $nextRequest Request URL to perform the asynchronous processing 非同期処理を実行するためのリクエストURL
     * @return boolean Execution result 実行結果
     */
    public static function callAsyncProcess($nextRequest)
    {
        $url = parse_url($nextRequest);
        $nextRequest = str_replace($url["scheme"]."://".$url["host"], "",  $nextRequest);
        
        // Call oneself by async
        $host = array();
        preg_match("/^https?:\/\/(([^\/]+)).*$/", BASE_URL, $host);
        $hostName = $host[1];
        if($hostName == "localhost"){
            $hostName = gethostbyname($_SERVER['SERVER_NAME']);
        }
        $hostSock = $hostName;
        if($_SERVER["SERVER_PORT"] == 443)
        {
            $hostSock = "ssl://".$hostName;
        }
        
        $handle = fsockopen($hostSock, $_SERVER["SERVER_PORT"]);
        if (!$handle)
        {
            return false;
        }
        
        stream_set_blocking($handle, false);
        fwrite($handle, "GET ".$nextRequest." HTTP/1.1\r\nHost: ". $hostName."\r\n\r\n");
        fclose ($handle);
        
        return true;
    }
    
}

?>
