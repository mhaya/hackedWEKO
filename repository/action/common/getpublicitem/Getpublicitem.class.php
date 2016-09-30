<?php

/**
 * Item registration number acquiring action of every public situation
 * 公開状況毎のアイテム登録数取得アクション
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Getpublicitem.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * DB object wrapper Class
 * DBオブジェクトラッパークラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDbAccess.class.php';
/**
 * The number of registered items aggregate common classes
 * 登録アイテム数集計共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAggregateCalculation.class.php';

/**
 * Item registration number acquiring action of every public situation
 * 公開状況毎のアイテム登録数取得アクション
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Common_Getpublicitem extends RepositoryAction
{
    //----------------------------
    // Request parameters
    //----------------------------
    /**
     * Administrator login ID
     * 管理者ログインID
     *
     * @var string
     */
    public $login_id = null;
    /**
     * Administrator password
     * 管理者パスワード
     *
     * @var string
     */
    public $password = null;
    /**
     * The output format of the item registration status
     * アイテム登録状況の出力形式
     *
     * @var string "xml" xml format xml形式
     *             "JSON" JSON format JSON形式
     */
    public $format = null;
    /**
     * User of the base level of authority
     * ユーザのベース権限レベル
     *
     * @var string
     */
    public $user_authority_id = 0;
    /**
     * User of room privilege level
     * ユーザのルーム権限レベル
     *
     * @var string
     */
    public $authority_id = 0;
    
    /**
     * And outputs the number of items registered every public situation in the response
     * 公開状況毎のアイテム登録数をレスポンスに出力する
     *
     * @return boolean Unauthorized access 不正アクセス
     */
    function executeApp() {
        // login check
        $result = null;
        $error_msg = null;
        
        $return = $this->checkLogin($this->login_id, $this->password, $result, $error_msg);
        
        if($return == false || $this->user_authority_id < $this->repository_admin_base || $this->authority_id < $this->repository_admin_room){
            print("Incorrect Login!\n");
            return false;
        }
        
        // get number of the items
        $repositoryAggregateCalculation = new RepositoryAggregateCalculation($this->Session, $this->dbAccess, $this->TransStartDate);
        
        $items = $repositoryAggregateCalculation->countItem();
        
        // output xml, json or error
        if ( strcasecmp($this->format, "xml") == 0) {
            header("Content-Type: text/xml; charset=utf-8");
            
            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".
                   "<items>".
                   "<total>".$items["total"]."</total>".
                   "<public>".$items["public"]."</public>".
                   "<private>".$items["private"]."</private>".
                   "<includeFulltext>".$items["includeFulltext"]."</includeFulltext>".
                   "<excludeFulltext>".$items["excludeFulltext"]."</excludeFulltext>".
                   "</items>";
            
            echo $xml;
        } else if ( strcasecmp($this->format, "JSON") == 0 ) {
            $json = "{ ".
                    "\"items\" : { ".
                    "\"total\" : ".$items["total"].", ".
                    "\"public\" : ".$items["public"].", ".
                    "\"private\" : ".$items["private"].", ".
                    "\"includeFulltext\" : ".$items["includeFulltext"].", ".
                    "\"excludeFulltext\" : ".$items["excludeFulltext"].
                    "} ".
                    "}";
            
            echo $json;
        } else {
            print("Incorrect Request Parameter!\n");
            return false;
        }
        
        $this->exitAction();
        exit();
    }
}
?>
