<?php

/**
 * Rankings create common classes
 * ランキング作成共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: deposit.php 58878 2015-10-15 03:23:15Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/business/Logbase.class.php';
/**
 * Index rights management common classes
 * インデックス権限管理共通クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/RepositoryIndexAuthorityManager.class.php';

/**
 * Rankings create common classes
 * ランキング作成共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Business_Ranking extends Repository_Components_Business_Logbase 
{
    /**
     * To create a ranking
     * ランキングを作成する
     *
     * @var int
     */
    const CREATE_RANKING_IS_ON = 1;
    
    /**
     * Does not want to create a ranking
     * ランキングを作成しない
     *
     * @var int
     */
    const CREATE_RANKING_IS_OFF = 0;
    
    /**
     * Detail ranking
     * 詳細画面ランキング
     *
     * @var array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    private $refer_ranking = array();
    /**
     * Download ranking
     * ダウンロード画面ランキング
     *
     * @var array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    private $download_ranking = array();
    /**
     * New item ranking
     * 新着アイテムランキング
     *
     * @var array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    private $newitem_ranking = array();
    /**
     * Search keyword ranking
     * 検索キーワードランキング
     *
     * @var array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    private $keyword_ranking = array();
    /**
     * User ranking
     * ユーザランキング
     *
     * @var array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    private $user_ranking = array();
    
    // date of ranking target
    /**
     * Ranking Period
     * ランキング集計期間
     *
     * @var string
     */
    private $ranking_term_date = '';
    /**
     * Ranking aggregate end date
     * ランキング集計終了日
     *
     * @var string
     */
    private $ranking_end_date = '';
    
    // instance of Index Authoryty Manager
    /**
     * Index authority management object
     * インデックス権限管理オブジェクト
     *
     * @var RepositoryIndexAuthorityManager
     */
    private $repositoryIndexAuthorityManager = null;
    
    // base and room authoryty of this weko
    
    /**
     * Administrator-based authority level
     * 管理者ベース権限レベル
     *
     * @var int
     */
    private $repository_admin_base = 0;
    /**
     * Administrator Room authority level
     * 管理者ルーム権限レベル
     *
     * @var int
     */
    private $repository_admin_room = 0;
    
    // create ranking data flg(default: create all ranking data)
    /**
     * Is create refer ranking
     * 詳細画面ランキングを作成するか
     *
     * @var int
     */
    private $isCreateReferRanking = self::CREATE_RANKING_IS_ON;
    /**
     * Is create download ranking
     * ダウンロード画面ランキングを作成するか
     *
     * @var int
     */
    private $isCreateDownloadRanking = self::CREATE_RANKING_IS_ON;
    /**
     * Is create new item ranking
     * 新着アイテムランキングを作成するか
     *
     * @var int
     */
    private $isCreateNewItemRanking = self::CREATE_RANKING_IS_ON;
    /**
     * Is create search keyword ranking
     * 検索キーワードランキングを作成するか
     *
     * @var int
     */
    private $isCreateKeywordRanking = self::CREATE_RANKING_IS_ON;
    /**
     * Is create user ranking
     * ユーザランキングを作成するか
     *
     * @var int
     */
    private $isCreateUserRanking = self::CREATE_RANKING_IS_ON;
    
    /**
     * abstract each count log process
     * ランキング作成
     */
    protected function executeApp()
    {
        // あとはあれ、逐一ランキング集計のONとOFFを制御するようにするか
        $this->debugLog("executeApp", __FILE__, __CLASS__, __LINE__);
        
        // create instance of index authoryty manager
        $repositoryAction = new RepositoryAction();
        $repositoryAction->setConfigAuthority();
        $this->repository_admin_base = $repositoryAction->repository_admin_base;
        $this->repository_admin_room = $repositoryAction->repository_admin_room;
        
        $this->debugLog("Session", __FILE__, __CLASS__, __LINE__);
        $container = & DIContainerFactory::getContainer();
        $session = $container->getComponent("Session");
        
        $this->repositoryIndexAuthorityManager = new RepositoryIndexAuthorityManager($session, $this->Db, $this->accessDate);
        
        // calclate ranking term
        if(strlen($this->ranking_term_date) == 0)
        {
	        $this->calcRankingTermDate();
        }
        
        // create each ranking data, set private member variable
        if($this->isCreateReferRanking === self::CREATE_RANKING_IS_ON)
        {
            $this->createReferRankingData();
        }
        if($this->isCreateDownloadRanking === self::CREATE_RANKING_IS_ON)
        {
            $this->createDownloadRankingData();
        }
        if($this->isCreateUserRanking === self::CREATE_RANKING_IS_ON)
        {
            $this->createUserRankingData();
        }
        if($this->isCreateKeywordRanking === self::CREATE_RANKING_IS_ON)
        {
            $this->createKeywordRankingData();
        }
        if($this->isCreateNewItemRanking === self::CREATE_RANKING_IS_ON)
        {
            $this->createRecentRankingData();
        }
    }
    
    /**
     * create view detail ranking data
     * アイテム閲覧数のランキング情報を作成する
     */
    private function createReferRankingData()
    {
        $this->debugLog("createReferRankingData", __FILE__, __CLASS__, __LINE__);
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        
        $public_item_query = $this->createPublicItemQuery();        
        $public_index_query = $this->repositoryIndexAuthorityManager->getPublicIndexQuery(false, $this->repository_admin_base, $this->repository_admin_room);
        
        // Make TmpTable 2014/11/07 T.Ichikawa --start--
        $now = date("YmdHis", strtotime($this->accessDate));
        $public_index_query = $this->replaceQueryForTemporaryTable($public_index_query, $now, $cntGroupNum);
        
        // No.1: アイテムが複数のインデックスに所属しているとき、所属インデックス数分、ランキングの値が倍加してしまうのを防ぐための処置
        //       所属インデックス内でアイテムIDをグループ化することも考えたが、非公開インデックスが混ざっているとき値がおかしくなるためこの形となっている
        $query = "SELECT ITEM.item_id, ITEM.item_no, ITEM.title, ITEM.title_english, count(ITEM.item_id) AS CNT ". 
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM]. 
                 " LEFT JOIN ". DATABASE_PREFIX. "repository_item ITEM ON LOG.item_id = ITEM.item_id ". 
                 " INNER JOIN (". // No.1
                    " SELECT POS.item_id AS item_id, POS.item_no AS item_no ".
                    " FROM ". DATABASE_PREFIX. "repository_position_index AS POS ". 
                    " INNER JOIN (". $public_index_query. ") PUB ON POS.index_id = PUB.index_id ".
                    " WHERE POS.is_delete = ? ".
                    " GROUP BY POS.item_id, POS.item_no ". 
                 " ) PUB ON ITEM.item_id = PUB.item_id AND ITEM.item_no = PUB.item_no ".
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.operation_id = ? ". 
                 " AND LOG.record_date >= ? ". 
                 " AND ". $public_item_query.
                 " GROUP BY LOG.item_id ". 
                 " ORDER BY count(ITEM.item_id) DESC ;";
        $params = array();
        $params[] = 0;
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_DETAIL_VIEW;
        $params[] = $this->ranking_term_date;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->dropTemporaryTable($now, $cntGroupNum);
        // Make TmpTable 2014/11/07 T.Ichikawa --end--
        
        $this->refer_ranking = $result;
    }
    
    /**
     * create download ranking data
     * アイテムダウンロード数のランキング情報を作成する
     */
    private function createDownloadRankingData()
    {
        $this->debugLog("createDownloadRankingData", __FILE__, __CLASS__, __LINE__);
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        
        $public_item_query = $this->createPublicItemQuery();        
        $public_index_query = $this->repositoryIndexAuthorityManager->getPublicIndexQuery(false, $this->repository_admin_base, $this->repository_admin_room);
        
        // Make TmpTable 2014/11/07 T.Ichikawa --start--
        $now = date("YmdHis", strtotime($this->accessDate));
        $public_index_query = $this->replaceQueryForTemporaryTable($public_index_query, $now, $cntGroupNum);
        
        // No.1: アイテムが複数のインデックスに所属しているとき、所属インデックス数分、ランキングの値が倍加してしまうのを防ぐための処置
        //       所属インデックス内でアイテムIDをグループ化することも考えたが、非公開インデックスが混ざっているとき値がおかしくなるためこの形となっている
        $query = "SELECT ITEM.item_id, ITEM.item_no, ITEM.title, ITEM.title_english, FILE.file_name AS file_name, FILE.file_no, count(*) AS CNT ". 
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 " INNER JOIN ". DATABASE_PREFIX. "repository_item ITEM ON LOG.item_id = ITEM.item_id ".
                 " INNER JOIN (". // No.1
                    " SELECT POS.item_id AS item_id, POS.item_no AS item_no ".
                    " FROM ". DATABASE_PREFIX. "repository_position_index AS POS ". 
                    " INNER JOIN (". $public_index_query. ") PUB ON POS.index_id = PUB.index_id ".
                    " WHERE POS.is_delete = ? ".
                    " GROUP BY POS.item_id, POS.item_no ". 
                 " ) PUB ON ITEM.item_id = PUB.item_id AND ITEM.item_no = PUB.item_no ".
                 " INNER JOIN ".DATABASE_PREFIX. "repository_file FILE ON LOG.item_id = FILE.item_id AND LOG.file_no = FILE.file_no AND LOG.attribute_id = FILE.attribute_id ". 
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.operation_id = ? ". 
                 " AND LOG.record_date >= ? ". 
                 " AND ". $public_item_query.
                 " GROUP BY LOG.item_id, LOG.attribute_id, LOG.file_no ". 
                 " ORDER BY count(*) DESC ;";
        $params = array();
        $params[] = 0;
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_DOWNLOAD_FILE;
        $params[] = $this->ranking_term_date;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->dropTemporaryTable($now, $cntGroupNum);
        // Make TmpTable 2014/11/07 T.Ichikawa --end--
        
        $this->download_ranking = $result;
    }
    
    /**
     * create user ranking data
     * ユーザランキングを作成
     */
    private function createUserRankingData()
    {
        $this->debugLog("createUserRankingData", __FILE__, __CLASS__, __LINE__);
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_DEFAULT);
        
        $sqlCmd = "SELECT USERS.handle AS handle, count(*) AS CNT ".
                  $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                  " LEFT JOIN ". DATABASE_PREFIX ."repository_item ITEM ON LOG.item_id = ITEM.item_id AND LOG.item_no = ITEM.item_no ".
                  " LEFT JOIN ". DATABASE_PREFIX ."users USERS ON USERS.user_id = ITEM.ins_user_id ".
                  " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                  " AND ITEM.shown_status = ? ". 
                  " AND LOG.record_date <= NOW() ".
                  " AND LOG.record_date >= ? ". 
                  " AND LOG.operation_id = ? ".
                  " GROUP BY ITEM.ins_user_id ".
                  " ORDER BY count(*) desc; ";
        $params = array();
        $params[] = 1;
        $params[] = $this->ranking_term_date;
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_ENTRY_ITEM;
        $items = $this->Db->execute($sqlCmd, $params);
        if($items === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->user_ranking = $items;
    }
    
    /**
     * create keyword ranking data
     * 検索キーワードランキングを作成
     */
    private function createKeywordRankingData()
    {
        $this->debugLog("createKeywordRankingData", __FILE__, __CLASS__, __LINE__);
        $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_RANKING);
        
        $sqlCmd = " SELECT LOG.search_keyword, count(LOG.log_no) AS CNT ".
                  $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                  " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                  " AND LOG.operation_id=? ".
                  " AND NOT(LOG.search_keyword=?) ";
        $params = array();
        $params[] = Repository_Components_Business_Logmanager::LOG_OPERATION_SEARCH;
        $params[] = '';
        if(strlen($this->ranking_end_date) > 0)
        {
        	$sqlCmd .= " AND LOG.record_date<=? ";
        	$params[] = $this->ranking_end_date;
        }
        else
        {
        	$sqlCmd .= " AND LOG.record_date<=NOW() ";
        }
        $sqlCmd .= " AND LOG.record_date>=? ".
                   " GROUP BY LOG.search_keyword ".
                   " ORDER BY count(LOG.log_no) DESC; ";
        $params[] = $this->ranking_term_date;
        $items = $this->Db->execute($sqlCmd, $params);
        if($items === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $this->debugLog($sqlCmd , __FILE__, __CLASS__, __LINE__);
        $this->debugLog(print_r($params, true), __FILE__, __CLASS__, __LINE__);
        
        $this->keyword_ranking = $items;
    }
    
    /**
     * create recent ranking data
     * 新着アイテムのランキング情報を作成する
     *
     */
    private function createRecentRankingData()
    {
        $this->debugLog("createRecentRankingData", __FILE__, __CLASS__, __LINE__);
        $public_item_query = $this->createPublicItemQuery();        
        $public_index_query = $this->repositoryIndexAuthorityManager->getPublicIndexQuery(false, $this->repository_admin_base, $this->repository_admin_room);
        
        // Make TmpTable 2014/11/07 T.Ichikawa --start--
        $now = date("YmdHis", strtotime($this->accessDate));
        $public_index_query = $this->replaceQueryForTemporaryTable($public_index_query, $now, $cntGroupNum);
        
        $query = "SELECT ITEM.item_id, ITEM.item_no, ITEM.title, ITEM.title_english, ITEM.shown_date ". 
                 " FROM ". DATABASE_PREFIX. "repository_item ITEM ". 
                 " INNER JOIN ". DATABASE_PREFIX. "repository_position_index POS ON ITEM.item_id = POS.item_id AND ITEM.item_no = POS.item_no AND POS.is_delete = ? ". 
                 " INNER JOIN (". $public_index_query. ") PUB ON POS.index_id = PUB.index_id ". 
                 " WHERE ". $public_item_query.
                 " AND ITEM.shown_date >= ? ". 
                 " GROUP BY ITEM.item_id ". 
                 " ORDER BY ITEM.shown_date DESC, ITEM.item_id DESC; ";
        $params = array();
        $params[] = 0;
        $params[] = $this->calcStartRecentDate();
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $this->dropTemporaryTable($now, $cntGroupNum);
        // Make TmpTable 2014/11/07 T.Ichikawa --end--
        $this->newitem_ranking = $result;
    }
    
    /**
     * If execute calculate ranking in long time, log, users, pages_users_link table is locked
     * If Shibboleth login, updates pages_users_link table
     * Lock in execute claculate ranking, Dead lock is occured
     * MｙISAMのテーブルを長い間SELECTし続けていると、ロックし続けてしまうため、
     * 一時テーブルを作成し、それにアクセスするよう、クエリを修正する
     *
     * @param string $mod_query Query クエリ
     * @param string $date Date 日付
     * @param int $cntGroupNum Create count 作成数
     * @return string Query クエリ 
     */
    private function replaceQueryForTemporaryTable($mod_query, $date, &$cntGroupNum=0)
    {
        // 一時テーブル作成
        $query = "CREATE TEMPORARY TABLE ". DATABASE_PREFIX. "repository_index_browsing_authority_".$date." ".
                 "( PRIMARY KEY (`index_id`), ".
                   "KEY `index_browsing_authority` (`exclusive_acl_role_id`,`exclusive_acl_room_auth`,`public_state`,`pub_date`,`is_delete`), ".
                   "KEY `index_public_state` (`public_state`,`pub_date`,`is_delete`) ) ".
                 "SELECT * FROM ". DATABASE_PREFIX. "repository_index_browsing_authority ;";
        $result = $this->Db->execute($query);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // Bug Fix temporary table can read only once 2014/11/20 T.Koyasu --start--
        // repository_index_browsing_groups is multiple exist in $mod_query
        // temporary table can read only once in query
        // therefore, create temporary table more than once
        // 一時テーブルを一つのクエリ内で複数回参照するとエラーとなる
        // $mod_query内にはrepository_index_browsing_groupsの記述が複数回(1~2)含まれているため、
        // $mod_query内の出現回数を調べ、その分ユニークな一時テーブルを作成している
        $word_num = mb_substr_count($mod_query, DATABASE_PREFIX. "repository_index_browsing_groups");
        for($temp_table_num = 0; $temp_table_num < $word_num; $temp_table_num++){
            $query = "CREATE TEMPORARY TABLE ". DATABASE_PREFIX. "repository_index_browsing_groups_".$date."_". $temp_table_num. " ".
                     "( PRIMARY KEY (`index_id`,`exclusive_acl_group_id`) ) ".
                     "SELECT * FROM ". DATABASE_PREFIX. "repository_index_browsing_groups ;";
            $result = $this->Db->execute($query);
            if($result === false)
            {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
            $cntGroupNum++;
        }
        // Bug Fix temporary table can read only once 2014/11/20 T.Koyasu --end--
        
        $query = "CREATE TEMPORARY TABLE ". DATABASE_PREFIX. "pages_users_link_".$date." ".
                 "( PRIMARY KEY (`room_id`,`user_id`), ".
                   "KEY `user_id` (`user_id`) ) ".
                 "SELECT * FROM ". DATABASE_PREFIX. "pages_users_link ;";
        $result = $this->Db->execute($query);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // クエリを一時テーブルを参照するよう修正
        $mod_query = str_replace(DATABASE_PREFIX."repository_index_browsing_authority", 
                                 DATABASE_PREFIX."repository_index_browsing_authority_".$date, 
                                 $mod_query);
        
        // Bug Fix temporary table can read only once 2014/11/20 T.Koyasu --start--
        // replace repository_index_browsing_groups to repository_index_browsing_groups
        $pattern = "/". DATABASE_PREFIX. "repository_index_browsing_groups[^_]/";
        $limit = 1;
        for($temp_table_num = 0; $temp_table_num < $word_num; $temp_table_num++){
            $replacement = DATABASE_PREFIX."repository_index_browsing_groups_".$date. "_". $temp_table_num. " ";
            $mod_query = preg_replace($pattern, $replacement, $mod_query, $limit);
        }
        // Bug Fix temporary table can read only once 2014/11/20 T.Koyasu --end--
        
        $mod_query = str_replace(DATABASE_PREFIX."pages_users_link", 
                                 DATABASE_PREFIX."pages_users_link_".$date, 
                                 $mod_query);
        
        return $mod_query;
    }
    
    /**
     * drop created temporary tables
     * 一時テーブルを削除する
     *
     * @param string $date Date 日付
     * @param int $cntGroupNum Create count 作成数
     */
    private function dropTemporaryTable($date, $cntGroupNum)
    {
        $result = $this->Db->execute("DROP TEMPORARY TABLE IF EXISTS ".DATABASE_PREFIX ."repository_index_browsing_authority_".$date.";");
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // Bug Fix temporary table can read only once 2014/11/20 T.Koyasu --start--
        // drop table to all temporary table "repository_index_browsing_groups_YYYYMMDD_?" by wild card
        for($ii = 0; $ii < $cntGroupNum; $ii++){
            $this->Db->execute("DROP TEMPORARY TABLE IF EXISTS ".DATABASE_PREFIX ."repository_index_browsing_groups_".$date."_".$ii.";");
            if($result === false)
            {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
        }
        // Bug Fix temporary table can read only once 2014/11/20 T.Koyasu --end--
        $result = $this->Db->execute("DROP TEMPORARY TABLE IF EXISTS ".DATABASE_PREFIX ."pages_users_link_".$date.";");
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * update repository_ranking_count_period table
     * ランキング集計期間を更新
     */
    public function updateRankingCountPeriod()
    {
        $query = "UPDATE {repository_ranking_count_period} SET " .
                "`from_date` = ?, " .
                "`to_date` = ?, " .
                "`mod_user_id`= ?, " .
                "`mod_date` = ?;";
        $params = array();
        $params[] = $this->ranking_term_date . ".000";
        $params[] = $this->accessDate;
        $params[] = $this->user_id;
        $params[] = $this->accessDate;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
    }
    
    /**
     * get ranking count period data from database
     * ランキング集計期間を取得
     *
     * @param string $from_date Start date 開始日時
     * @param string $to_date End date 終了日時
     */
    public function getRankingCountPeriodFromDb(&$from_date, &$to_date)
    {
        $from_date = "";
        $to_date = "";
        $query = "SELECT `from_date`, `to_date`, `mod_user_id`, `mod_date` " .
                "FROM {repository_ranking_count_period} ";
        $result = $this->Db->execute($query);
        if($result === 0)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        if($result[0]["from_date"] !== "0")
        {
            $from_date = $result[0]["from_date"];
        }
        
        if($result[0]["to_date"] !== "0")
        {
            $to_date = $result[0]["to_date"];
        }
    }
    
    /**
     * get ranking count period data from executing data
     * 現在時刻から集計期間を算出する
     *
     * @param string $from_date Start date 開始日時
     * @param string $to_date End date 終了日時
     */
    public function getRankingCountPeriodExecutingData(&$from_date, &$to_date)
    {
        $from_date = $this->ranking_term_date . ".000";
        $to_date = $this->accessDate;
    }
    
    // getter //
    /**
     * Get detail ranking
     * 詳細画面ランキング取得
     *
     * @return array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    public function getReferRanking(){  return $this->refer_ranking;}
    
    /**
     * Get download ranking
     * ダウンロードランキング取得
     *
     * @return array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    public function getDownloadRanking(){   return $this->download_ranking;}
    
    /**
     * Get user ranking
     * ユーザランキング取得
     *
     * @return array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    public function getUserRanking(){   return $this->user_ranking;}
    
    /**
     * Get search keyword ranking
     * 検索キーワードランキング取得
     *
     * @return array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    public function getKeywordRanking(){    return $this->keyword_ranking;}
    
    /**
     * Get new item ranking
     * 新着アイテムランキング取得
     *
     * @return array["item_id"|"item_no"|"title"|"title_english"|"count"]
     */
    public function getNewItemRanking(){    return $this->newitem_ranking;}
    
    // setter //
    /**
     * Set start date
     * 開始日時設定
     *
     * @param string $startDate Start date 開始日時
     */
    public function setStartDate($startDate){ $this->ranking_term_date = $startDate;}
    
    /**
     * Set end date
     * 終了日時設定
     *
     * @param string $endDate End date 終了日時
     */
    public function setEndDate($endDate){ $this->ranking_end_date = $endDate;}
    
    // private method for calclation
    /**
     * Calculation of the ranking Period
     * ランキング集計期間の計算
     */
    private function calcRankingTermDate()
    {
        // ランキング数（新着アイテム以外）
        $rank_term = 365;
        $query = "SELECT param_value FROM ". DATABASE_PREFIX ."repository_parameter WHERE param_name=?";
        $params = array();
        $params[] = 'ranking_term_stats';
        $items = $this->Db->execute($query, $params);
        if($items[0]['param_value'] != "" && $items[0]['param_value'] != null){
            $rank_term = $items[0]['param_value'];
        }
        
        // ランキング数（新着アイテム以外）
        $rank_num = 5;
        $query = "SELECT param_value FROM ". DATABASE_PREFIX ."repository_parameter WHERE param_name=?";
        $params = array();
        $params[] = 'ranking_disp_num';
        $items = $this->Db->execute($query, $params);
        if($items[0]['param_value'] != "" && $items[0]['param_value'] != null){
            $rank_num = $items[0]['param_value'];
        }
        
        // 新着アイテム扱いの期間（過去Ｘ日）
        $newitem_term = 14;
        $query = "SELECT param_value FROM ". DATABASE_PREFIX ."repository_parameter WHERE param_name=?";
        $params = array();
        $params[] = 'ranking_term_recent_regist';
        $items = $this->Db->execute($query, $params);
        if($items[0]['param_value'] != "" && $items[0]['param_value'] != null){
            $newitem_term = $items[0]['param_value'];
        }
        // Add log reset ranking refer 2010/02/18 K.Ando --start--
        
        $ranking_reset_last_date = "";
        $query = "SELECT param_value FROM ". DATABASE_PREFIX ."repository_parameter WHERE param_name=?;";
        $params = array();
        $params[] = 'ranking_last_reset_date';
        $items = $this->Db->execute($query, $params);
           if($items[0]['param_value'] != "" && $items[0]['param_value'] != null){
            $ranking_reset_last_date = $items[0]['param_value'];
        }
        
        if($ranking_reset_last_date != "" )
        {
            // Fix date calculate 2010/07/29 A.Suzuki --start--
            //$logjikan = time()- 60 * 60 * 24 * $rank_term;
            //$jikan = strtotime($ranking_reset_last_date);
            //$this->ranking_term_sec = $jikan <= $logjikan  ? $logjikan : $jikan;
            $ranking_reset_last_date = str_replace("/","-",$ranking_reset_last_date);
            $query = "SELECT DATE_SUB(NOW(), INTERVAL ? DAY) AS rank_date;";
            $params = array();
            $params[] = $rank_term;
            $result = $this->Db->execute($query, $params);
            $rank_term_date = $result[0]['rank_date'];
            $query = "SELECT DATEDIFF(?, ?) AS date_diff;";
            $params = array();
            $params[] = $rank_term_date;
            $params[] = $ranking_reset_last_date;
            $result = $this->Db->execute($query, $params);
            if($result[0]['date_diff'] >= 0){
                $this->ranking_term_date = $rank_term_date;
            } else {
                $this->ranking_term_date = $ranking_reset_last_date;
            }
            // Fix date calculate 2010/07/29 A.Suzuki --end--
        }else
        {
            // Fix date calculate 2010/07/29 A.Suzuki --start--
            //$this->ranking_term_sec = time() - 60 * 60 * 24 * $rank_term;
            $query = "SELECT DATE_SUB(NOW(), INTERVAL ? DAY) AS rank_date;";
            $params = array();
            $params[] = $rank_term;
            $result = $this->Db->execute($query, $params);
            $this->ranking_term_date = $result[0]['rank_date'];
            // Fix date calculate 2010/07/29 A.Suzuki --end--
        }
        // Add log reset ranking refer 2010/02/18 K.Ando --end--
    }
    
    /**
     * calc start date of new items 
     * return date of 'NOW - ranking_term_recent_regist' or 'ranking_last_reset_date'
     * 新着アイテムとしてみなす開始日時を算出する
     *
     * @return string Start date for new item 新着アイテム開始日時
     *                'YYYY-MM-dd hh:mm:ss.000'
     */
    private function calcStartRecentDate()
    {
        $newItemStartDate = "";
        
        $query = "SELECT param_value ". 
                 " FROM ". DATABASE_PREFIX. "repository_parameter ". 
                 " WHERE param_name = ? ". 
                 " AND is_delete = ? ;";
        $params = array();
        $params[] = 'ranking_term_recent_regist';
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // start date of new item is 'now - new_item_term'
        $query = "SELECT DATE_SUB(NOW(), INTERVAL ? DAY) AS start_date;";
        $params = array();
        $params[] = $result[0]['param_value'];
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        $newItemStartDate = $result[0]['start_date'];
        
        // If start date of new item is old from ranking_reset_date, 
        // start date of new item is ranking_reset_date
        if(strtotime($newItemStartDate) < strtotime($this->ranking_term_date))
        {
            $newItemStartDate = $this->ranking_term_date;
        }
        
        return $newItemStartDate;
    }
    
    /**
     * Create the query that item is public
     * 公開アイテムの条件を満たすクエリの条件句を作成する
     *
     * @return string Query that item is public
     *                公開アイテムの条件を満たすクエリの条件句
     */
    private function createPublicItemQuery()
    {
        $public_item_query = "ITEM.shown_date <= NOW() ". 
                             " AND ITEM.shown_status = 1 ".
                             " AND ITEM.is_delete = 0 ";
        
        return $public_item_query;
    }
    
    // set create ranking flg to OFF
    /**
     * Does not create a detailed screen ranking
     * 詳細画面ランキングを作成しない
     */
    public function toOffReferRanking(){    $this->isCreateReferRanking = self::CREATE_RANKING_IS_OFF;      }
    
    /**
     * Does not create a download ranking
     * ダウンロードランキングを作成しない
     */
    public function toOffDownloadRanking(){ $this->isCreateDownloadRanking = self::CREATE_RANKING_IS_OFF;   }
    /**
     * Does not create a new item ranking
     * 新着アイテムランキングを作成しない
     */
    public function toOffNewItemRanking(){  $this->isCreateNewItemRanking = self::CREATE_RANKING_IS_OFF;    }
    /**
     * Does not create a search keyword ranking
     * 検索キーワードランキングを作成しない
     */
    public function toOffKeywordRanking(){  $this->isCreateKeywordRanking = self::CREATE_RANKING_IS_OFF;    }
    /**
     * Does not create a user ranking
     * ユーザランキングを作成しない
     */
    public function toOffUserRanking(){     $this->isCreateUserRanking = self::CREATE_RANKING_IS_OFF;       }
}
?>