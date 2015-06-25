<?php
/**
 * $Id: Sendsitelicensemail.class.php 51333 2015-04-01 05:35:29Z tomohiro_ichikawa $
 * 
 * アイテム削除ビジネスクラス
 * 
 * @author IVIS
 * @sinse 2014/11/11
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
require_once WEBAPP_DIR. '/modules/repository/components/LogAnalyzor.class.php';
require_once WEBAPP_DIR. '/modules/repository/components/business/Logmanager.class.php';

class Repository_Components_Business_Sendsitelicensemail extends BusinessBase
{
    // 送付するZIPを作成する作業ディレクトリ
    private $zip_dir = "";
    // レポートファイルを作成する作業ディレクトリ
    private $tmp_file_dir = "";
    
    /**
     * constructer
     */
    public function __construct()
    {
        // メンバ変数は文字列連結で定義できないのでコンストラクタで設定する
        $this->zip_dir = WEBAPP_DIR."/uploads/repository/";
        $this->tmp_file_dir = WEBAPP_DIR."/uploads/repository/tmp/";
    }
    
    /**
    * 作業ディレクトリ作成処理
    * 
    */
    public function createSitelicenseMailTmpDir() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 一時ディレクトリが存在しない場合作成する
        if(!file_exists($this->tmp_file_dir)) {
            mkdir($this->tmp_file_dir, 0777);
        }
    }
    
    /**
    * サイトライセンスメール送信の可否の判定処理
    * 
    * @return bool
    */
    public function checkSendSitelicense() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // サイトライセンスメール送信フラグのチェック
        $query = "SELECT param_value FROM ". DATABASE_PREFIX. "repository_parameter ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "send_sitelicense_mail_activate_flg";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 送信フラグが0ならfalse
        if($result[0]["param_value"] == 0) {
            return false;
        }
        
        // サイトライセンスメール対象者の確認
        $query = "SELECT no FROM ". DATABASE_PREFIX. "repository_send_mail_sitelicense ;";
        $result = $this->Db->execute($query);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // 送信対象者がいればfalse
        if(count($result) > 0) {
            return false;
        }
        
        // すべての条件をクリアすればtrueを返す
        return true;
        
    }
    
    /**
    * サイトライセンスメール送信対象リストの作成処理
    * 
    * @return bool
    */
    public function insertSendSitelicenseMailList() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // サイトライセンス基本情報の取得
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_sitelicense_info ".
                 "WHERE is_delete = ? ;";
        $params = array();
        $params[] = 0;
        $slBaseInfo = $this->Db->execute($query, $params);
        if($slBaseInfo === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        // サイトライセンス基本情報のレコード数だけ行う
        for($ii = 0; $ii < count($slBaseInfo); $ii++) {
            // 送信対象者リストテーブルへの挿入
            $query = "INSERT INTO ". DATABASE_PREFIX. "repository_send_mail_sitelicense ".
                     "(no, organization_name, mail_address) ".
                     "VALUES ".
                     "(?, ?, ?)";
            $params = array();
            $params[] = $slBaseInfo[$ii]["organization_id"];
            $params[] = $slBaseInfo[$ii]["organization_name"];
            $params[] = $slBaseInfo[$ii]["mail_address"];
            $result = $this->Db->execute($query, $params);
            if($result === false) {
                $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
                throw new AppException($this->Db->ErrorMsg());
            }
        }
        
        return true;
    }
    
    /**
    * サイトライセンスメール送信対象者取得処理
    * 
    * @return array
    */
    public function getSendSitelicenseUser() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // サイトライセンスユーザー情報配列
        $sl = array();
        
        // サイトライセンスメール送信対象者を先頭の1件取得する
        $query = "SELECT * FROM ". DATABASE_PREFIX. "repository_send_mail_sitelicense ". 
                 "ORDER BY no ASC ".
                 "LIMIT 0,1 ;";
        $slBaseInfo = $this->Db->execute($query);
        if($slBaseInfo === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        // テーブルにレコードが無い場合は処理を終了する
        if(count($slBaseInfo) == 0) {
            // 空のまま返す
            return $sl;
        }
        
        // 出力用配列の作成
        $sl[0]["organization_id"] = $slBaseInfo[0]["no"];
        $sl[0]["organization_name"] = $slBaseInfo[0]["organization_name"];
        $sl[0]["mail_address"] = $slBaseInfo[0]["mail_address"];
        
        return $sl;
    }
    
    /**
    * サイトライセンス送信対象者データの削除処理
    * 
    * @param int $id sitelicense id
    * 
    * @return bool
    */
    public function deleteSendSitelicenseuser($id) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 指定したサイトライセンスIDのレコードを削除
        $query = "DELETE FROM ". DATABASE_PREFIX. "repository_send_mail_sitelicense ".
                 "WHERE no = ? ;";
        $params = array();
        $params[] = $id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return true;
    }
    
    /**
    * サイトライセンスID毎のログ件数取得処理
    * 
    * @param string $start_date
    * @param string $finish_date
    * @param int $sitelicense_id
    * @param int $operation_id
    * 
    * @return array
    */
    public function getLogCountBySitelicenseId($start_date, $finish_date, $sitelicense_id, $operation_id) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        if($operation_id != Repository_Components_Business_Logmanager::LOG_OPERATION_SEARCH)
        {
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog();
        }
        else
        {
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_RANKING);
        }
        
        // T.B.D
        $query = "SELECT COUNT(LOG.record_date), ". Repository_Components_Loganalyzor::dateformatMonthlyQuery("LOG"). 
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.record_date >= ? ".
                 $this->execlusiveIpAddressQuery("LOG").
                 Repository_Components_Loganalyzor::execlusiveRobotsQuery("LOG"). 
                 " AND LOG.operation_id = ? ". 
                 " AND LOG.site_license_id = ? ". 
                 Repository_Components_Loganalyzor::perMonthlyQuery(). " ;";
        $params = array();
        $params[] = $start_date;
        $params[] = $operation_id;
        $params[] = $sitelicense_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
    * サイトライセンスID毎の操作ログ取得処理
    * 
    * @param int $issn
    * @param string $start_date
    * @param string $finish_date
    * @param int $sitelicense_id
    * @param int $operation_id
    * 
    * @return array
    */
    public function getLogBySitelicenseId($issn, $start_date, $finish_date, $sitelicense_id, $operation_id) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        if($operation_id != Repository_Components_Business_Logmanager::LOG_OPERATION_SEARCH)
        {
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog();
        }
        else
        {
            $subQuery = Repository_Components_Business_Logmanager::getSubQueryForAnalyzeLog(Repository_Components_Business_Logmanager::SUB_QUERY_TYPE_RANKING);
        }
        
        // T.B.D
        $query = "SELECT COUNT(DISTINCT LOG.record_date), ".
                         "IDX.online_issn, ". 
                         Repository_Components_Loganalyzor::dateformatMonthlyQuery("LOG").
                 $subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_FROM].", ".
                 DATABASE_PREFIX. "repository_index AS IDX, ".
                 DATABASE_PREFIX. "repository_position_index AS POS, ".
                 DATABASE_PREFIX. "repository_item AS ITEM ".
                 " WHERE ".$subQuery[Repository_Components_Business_Logmanager::SUB_QUERY_KEY_WHERE].
                 " AND LOG.record_date >= ? ".
                 " AND LOG.record_date <= ? ".
                 " ". $this->execlusiveIpAddressQuery("LOG").
                 " ". Repository_Components_Loganalyzor::execlusiveRobotsQuery("LOG").
                 " AND LOG.operation_id = ? ".
                 " AND IDX.biblio_flag = ? ".
                 " AND IDX.online_issn IN ( ". $issn. " ) ".
                 " AND IDX.index_id = POS.index_id ".
                 " AND POS.item_id = LOG.item_id ".
                 " AND POS.item_id = ITEM.item_id ".
                 " AND LOG.site_license_id = ? ".
                 $this->getExclusiveSitelicenseItemtype("ITEM").
                 " ". Repository_Components_Loganalyzor::perMonthlyQuery(). ", IDX.online_issn ;";
        $params = array();
        $params[] = $start_date;
        $params[] = $finish_date;
        $params[] = $operation_id;
        $params[] = 1;
        $params[] = $sitelicense_id;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
    * ISSN取得処理
    * 
    * @return array
    */
    public function getOnlineIssn() {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // ISSN値を全て取得する
        $query = "SELECT issn, jtitle, jtitle_en, set_spec FROM ". DATABASE_PREFIX. "repository_issn ". 
                 "WHERE is_delete = ? ;";
        $params = array();
        $params[] = 0;
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        return $result;
    }
    
    /**
    * レポートファイル作成処理
    * 
    * @param string $file_name
    * @param string $file_body
    * 
    * @return bool
    */
    public function createReport($file_name, $file_body) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $BOM = pack('C*',0xEF,0xBB,0xBF);
        $logReport = fopen($this->tmp_file_dir.$file_name, "w");
        fwrite($logReport, $BOM.$file_body);
        fclose($logReport);
        
        return true;
    }
    
    /** 
     * 除外アイテムタイプサブクエリ取得処理
     * 
     * @param string $abbreviation
     * 
     * @return array
     */
    private function getExclusiveSitelicenseItemtype($abbreviation) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $query = "SELECT param_value FROM ".DATABASE_PREFIX ."repository_parameter ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params = "site_license_item_type_id";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $sitelicense_item_type_id = array();
        if(strlen($result[0]["param_value"]) > 0) {
            $sitelicense_item_type_id = explode(",", $result[0]["param_value"]);
        }
        $item_type_id_query = Repository_Components_Loganalyzor::exclusiveSitelicenseItemtypeQuery($abbreviation, $sitelicense_item_type_id);
        
        return $item_type_id_query;
    }
    
    /**
    * ZIPファイル作成処理
    * 
    * @param string $zip_name
    * 
    * @return bool
    */
    public function compressToZip($zip_name) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        // 一時ディレクトリを送付用のZIPに圧縮する
        $output_files = array($this->tmp_file_dir);
        File_Archive::extract($output_files, 
                              File_Archive::toArchive($zip_name, 
                                                      File_Archive::toFiles($this->zip_dir)
                                                     )
                             );
        // 一時ディレクトリ削除
        if ($handle = opendir($this->tmp_file_dir)) {
            while (false !== ($file = readdir($handle))) {
                chmod($this->tmp_file_dir. $file, 0777 );
                unlink($this->tmp_file_dir. $file);
            }
            closedir($handle);
        }
        chmod($this->tmp_file_dir, 0777 );
        rmdir($this->tmp_file_dir);
    }
    
    public function execlusiveIpAddressQuery($abbreviation) {
        $this->debugLog(__FUNCTION__ , __FILE__, __CLASS__, __LINE__);
        
        $query = "SELECT param_value FROM ". DATABASE_PREFIX. "repository_parameter ".
                 "WHERE param_name = ? ;";
        $params = array();
        $params[] = "log_exclusion";
        $result = $this->Db->execute($query, $params);
        if($result === false) {
            $this->errorLog($this->Db->ErrorMsg(), __FILE__, __CLASS__, __LINE__);
            throw new AppException($this->Db->ErrorMsg());
        }
        
        $ip_address = str_replace(array("\r\n", "\r", "\n"), ",", $result[0]["param_value"]);
        $ip_exclusion = "";
        $colomun_name = "";
        if(strlen($abbreviation) == 0) {
            $column_name = "ip_address";
        } else if(strlen($abbreviation) > 0) {
            $column_name = $abbreviation. ".ip_address";
        }
        if(strlen($ip_address) > 0) {
            $ip_exclusion = " AND ". $column_name. " NOT IN ('". $ip_address. "') ";
        }
        
        return $ip_exclusion;
    }
}
?>