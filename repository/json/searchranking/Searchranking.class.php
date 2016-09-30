<?php
/**
 * Ranking JSON format output action class
 * ランキングJSON型式出力アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Detail.class.php 36217 2014-05-26 04:22:11Z satoshi_arata $
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
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * String format conversion common classes
 * 文字列形式変換共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputFilter.class.php';

/**
 * Ranking JSON format output action class
 * ランキングJSON型式出力アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Json_Searchranking extends RepositoryAction
{
    /**
     * Ranking JSON format output
     * ランキングJSON型式出力
     *
     * @access  public
     */
    function executeApp()
    {
        $this->exitFlag = true;
        
        // ランキング設定を取得する
        $rankingDisplay = 0;
        $rankingNum = 0;
        $error_msg = "";
        // ランキング取得方式
        $result = $this->getAdminParam('ranking_disp_setting', $rankingDisplay, $error_msg);
        if ( $result == false ){
            $exception = new RepositoryException( "ERR_MSG_xxx-xxx1", "xxx-xxx1" );    //主メッセージとログIDを指定して例外を作成
            $exception->setDetailMsg( $error_msg );         //詳細メッセージ設定
            throw $exception;
        }
        // ランキング表示数
        $result = $this->getAdminParam('ranking_disp_num', $rankingNum, $error_msg);
        if ( $result == false ){
            $exception = new RepositoryException( "ERR_MSG_xxx-xxx1", "xxx-xxx1" );    //主メッセージとログIDを指定して例外を作成
            $exception->setDetailMsg( $error_msg );         //詳細メッセージ設定
            throw $exception;
        }
        
        // 検索ワードランキングを取得する
        if($rankingDisplay == 0){
            // リアルタイムに更新するの時
            $this->infoLog("businessRanking", __FILE__, __CLASS__, __LINE__);
            $ranking = BusinessFactory::getFactory()->getBusiness("businessRanking");
            $ranking->execute();
            $result = $ranking->getKeywordRanking();
        } else {
            // DB保存情報を表示するの時
            // get ranking data from repository_ranking
            $query = "SELECT * ".
                     "FROM " .DATABASE_PREFIX ."repository_ranking ".
                     "WHERE rank_type = ? ".
                     " AND is_delete = ? ".
                     "ORDER BY rank;";
                     
            $params = array();
            $params[] = 'keywordRanking';
            $params[] = 0;
            $result = $this->dbAccess->executeQuery($query, $params);
            if($result === false){
                return 'error';
            }
        }
        $this->outputJSON($rankingNum, $rankingDisplay, $result);
        return 'success';
    }
    
    /**
     * output keyword ranking by JSON
     * JSON形式で検索ワードランキングを出力する
     *
     * @param int $rankingNum number of ranking show
     *                        ランキング表示数
     * @param int $rankingDisplay type of getting ranking 
     *                            ランキング取得方式
     * @param array $keywordRanking result of keyword ranking 
     *                              検索ワードランキング結果
     *                              array[$ii]["search_keyword"|"count(*)"]
     *                              or array[$ii]["rank"|"disp_name"|"disp_value"]
     */
    private function outputJSON($rankingNum, $rankingDisplay, $keywordRanking)
    {
        $outputJSON = "{";
        for($ii = 0; $ii < $rankingNum && $ii < count($keywordRanking); $ii++){
            if($ii != 0){
                $outputJSON .= ",";
            }
            $rank = $ii+1;
            $word = "";
            $num = "";
            if($rankingDisplay == 0){
                $word = RepositoryOutputFilter::escapeJSON($keywordRanking[$ii]["search_keyword"]);
                $num = RepositoryOutputFilter::escapeJSON($keywordRanking[$ii]["CNT"]);
            }else {
                $rank = RepositoryOutputFilter::escapeJSON($keywordRanking[$ii]["rank"]);
                $word = RepositoryOutputFilter::escapeJSON($keywordRanking[$ii]["disp_name"]);
                $num = RepositoryOutputFilter::escapeJSON($keywordRanking[$ii]["disp_value"]);
            }
            // "0":{"rank":1,"word":"test","count":25} のような形で出力
            $outputJSON .= "\"" . $ii . "\":".
                           "{\"rank\":\"" . $rank ."\",".
                           "\"word\":\"".$word ."\",".
                           "\"count\":\"".$num ."\"}";
        }
        $outputJSON .= "}";
        echo $outputJSON;
    }

}
?>
