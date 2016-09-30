<?php

/**
 * References acquisition action class
 * 参考文献取得アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Crossdomainproxy.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * References acquisition action class
 * 参考文献取得アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Common_Crossdomainproxy extends RepositoryAction
{
    /**
     * Access to the URL of the specified reference in the request, and outputs a response
     * リクエストで指定された参考文献のURLにアクセスし、レスポンスを出力する
     */
    function executeApp() {
        $url = html_entity_decode($_GET["ajaxRequest"]);
        
        $option = array("timeout" => 10, 
                        "allowRedirects" => "true", 
                        "maxRedirects" => 3);
        
        $request = new HTTP_Request($url, $option);
        $request->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']);
        
        $response = $request->sendRequest(); 
        
        if (!PEAR::isError($response)) { 
            echo $request->getResponseBody();
        } else {
            echo $request->getResponseCode();
        }
    }
}
?>
