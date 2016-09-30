<?php

/**
 * Item registration and editing process common classes
 * アイテム登録・編集処理共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: ItemRegister.class.php 70936 2016-08-09 09:53:57Z keiya_sugimoto $
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
 * Extended Log class for the WEKO
 * WEKO用拡張ログクラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';

/**
 * Name authority common classes
 * 著者名典拠共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';

/**
 * Common class to operate string
 * 文字列の操作を行う共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/util/StringOperator.class.php';

/**
 * Item registration and editing process common classes
 * アイテム登録・編集処理共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class ItemRegister extends RepositoryAction
{
    // member
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    var $Session = null;
    /**
     * DB object
     * Dbコンポーネントを受け取る
     *
     * @var DbObjectAdodb
     */
    var $Db = null;
    /**
     * Insert user id
     * 挿入ユーザID
     *
     * @var string
     */
    private $ins_user_id = null;
    /**
     * Update user id
     * 更新ユーザID
     *
     * @var string
     */
    private $mod_user_id = null;
    /**
     * Edit start date
     * 編集開始日時
     *
     * @var string
     */
    private $edit_start_date = null;
    
    // Add multimedia support 2012/08/27 T.Koyasu -start-
    /**
     * ffmpeg valid flag
     * ffmpeg有効フラグ
     *
     * @var boolean
     */
    private $isValidFfmpeg = null;
    // Add multimedia support 2012/08/27 T.Koyasu -end-
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $Session Session management objects Session管理オブジェクト
     * @param DbObject $Db Database management objects データベース管理オブジェクト
     */
    function ItemRegister($Session, $Db){
        if($Session){
            $this->Session = $Session;
        } else {
            return null;
        }
        if($Db != null){
            $this->Db = $Db;
        } else {
            return null;
        }
        $this->edit_start_date = $this->Session->getParameter("edit_start_date");
        $this->TransStartDate = $this->edit_start_date;
        $this->ins_user_id = $this->Session->getParameter("_user_id");
        $this->mod_user_id = $this->Session->getParameter("_user_id");
        $this->del_user_id = $this->Session->getParameter("_user_id");
        
        // ロガー
        $this->Logger = WekoBusinessFactory::getFactory()->logger;
    }
    
    /**
     * Set ins_user_id
     * 挿入ユーザID設定
     *
     * @param string $user_id Insert user id 挿入ユーザID
     */
    public function setInsUserId($user_id)
    {
        $this->ins_user_id = $user_id;
    }
    
    /**
     * Set mod_user_id
     * 更新ユーザID設定
     *
     * @param string $user_id Update user id 更新ユーザID
     */
    public function setModUserId($user_id)
    {
        $this->mod_user_id = $user_id;
    }
    
    /**
     * Set del_user_id
     * 削除ユーザID設定
     *
     * @param string $user_id Delet user id 削除ユーザID
     */
    public function setDelUserId($user_id)
    {
        $this->del_user_id = $user_id;
    }
    /**
     * Set edit_start_date
     * 編集開始日時設定
     *
     * @param string $date Date 日時
     */
    public function setEditStartDate($date)
    {
        $this->edit_start_date = $date;
        $this->TransStartDate = $date;
    }
    
    /**
     * Entry item
     * アイテム登録
     * 
     * @param array $item Item information アイテム情報
     *                    array["item_id"|"item_no"|"revision_no"|"item_type_id"|"title"|"title_english"|"language"|"serch_key"|"serch_key_english"|"pub_year"|"pub_month"|"pub_day"]
     * @param string $errMsg Error message エラーメッセージ
     * @param boolean $harvestingFlag Havest flag ハーベストフラグ
     * @return boolean Result 結果
     */
    function entryItem($item, &$errMsg, $harvestingFlag=false){
        $query = "INSERT INTO ". DATABASE_PREFIX ."repository_item ".
                    "(item_id, item_no, revision_no, item_type_id, prev_revision_no, ".
                    "title, title_english, language, review_status, review_date, shown_status, shown_date, ".
                    "reject_status, reject_date, reject_reason, serch_key, serch_key_english, remark, uri, ins_user_id, ".
                    "mod_user_id, del_user_id, ins_date, mod_date, del_date, is_delete) ".
                    "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
        $params = array();
        $params[] = $item['item_id'];   //item_id
        $params[] = $item['item_no'];   //item_no
        $params[] = 1;  //revision_no
        $params[] = $item['item_type_id'];  //item_type_id
        $params[] = 0;  //prev_revision_no
        $params[] = $item['title']; //title
        $params[] = $item['title_english']; //title_english
        $params[] = $item['language'];  //language
        $params[] = -1; //review_status
        $params[] = ""; //review_date
        $params[] = 0;  //shown_status
        if($item['pub_year'] != ""){
            $params[] = $this->generateDateStr( $item['pub_year'], 
                                                $item['pub_month'], 
                                                $item['pub_day']);  // "shown_date"
        } else {
            $params[] = ""; //shown_date
        }
        $params[] = 0;  //reject_status
        $params[] = ""; //reject_date
        $params[] = ""; //reject_reason
        $params[] = $item['serch_key']; //serch_key
        $params[] = $item['serch_key_english']; //serch_key_english
        $params[] = ""; //remark
        if($harvestingFlag) {
            $params[] = BASE_URL . "/?action=repository_uri&item_id=". $item['item_id']; //uri
        } else {
            $params[] = ""; //uri
        }
        $params[] = $this->ins_user_id; //ins_user_id
        $params[] = $this->mod_user_id; //mod_user_id
        $params[] = ""; //del_user_id
        $params[] = $this->edit_start_date; //ins_date
        $params[] = $this->edit_start_date; //mod_date
        $params[] = ""; //del_date
        $params[] = 0;  //is_delete
        $ret = $this->Db->execute($query, $params); 
        if ($ret === false) {
            $errMsg = $this->Db->ErrorMsg();
            $this->failTrans();   
            return false;
        }
        return true;
    }
    
    /**
     * Change the public status of the item while editing private, peer-reviewed approval
     * アイテムの公開状況を非公開、査読承認を編集中に変更
     * 
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function editItem($item_id, $item_no, &$errMsg){
        // コンテンツ数対応 add count contents 2009/02/17 A.Suzuki --start--
        // 編集前公開状況取得 get shown_status
        $query = "SELECT shown_status ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE item_id = ? ".
                 "AND item_no = ?; ";
        $params = array();
        $params[] = $item_id;                   // item_id
        $params[] = $item_no;                   // item_no
        $result = $this->Db->execute($query, $params);              
        if ($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            // roll back
            $this->failTrans();
            return false;
        }
        $old_shown_status = $result[0]['shown_status'];
        // コンテンツ数対応 add count contents 2009/02/17 A.Suzuki --end--
        
        $query = "UPDATE ". DATABASE_PREFIX ."repository_item ".
                 "SET shown_status = ?, ".
                 "review_status = ?, ".
                 "reject_status = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ? ".
                 "WHERE item_id = ? AND ".
                 "item_no = ?; ";
        $params = null;
        $params[] = 0;          // shown_status
        $params[] = -1;     // review_status
        $params[] = 0;      // reject_status
        $params[] = $this->mod_user_id;                 // mod_user_id
        $params[] = $this->edit_start_date;         // mod_date
        $params[] = $item_id;                   // item_id
        $params[] = $item_no;                   // item_no
        //UPDATE
        $result = $this->Db->execute($query, $params);              
        if ($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            // roll back
            $this->failTrans();
            return false;
        }
        
        // コンテンツ数対応 add count contents 2009/02/17 A.Suzuki --start--
        // 公開中だった場合所属インデックスからコンテンツ数を減らす
        if($old_shown_status == "1"){
            // 所属インデックス情報取得 get index info
            $query = "SELECT ".DATABASE_PREFIX."repository_index.index_id, ".DATABASE_PREFIX."repository_index.public_state ".
                     "FROM ".DATABASE_PREFIX."repository_index, ".DATABASE_PREFIX."repository_position_index ".
                     "WHERE ".DATABASE_PREFIX."repository_position_index.item_id = ? ".
                     "AND ".DATABASE_PREFIX."repository_position_index.item_no = ? ".
                     "AND ".DATABASE_PREFIX."repository_position_index.is_delete = 0 ".
                     "AND ".DATABASE_PREFIX."repository_position_index.index_id = ".DATABASE_PREFIX."repository_index.index_id ".
                     "AND ".DATABASE_PREFIX."repository_index.is_delete = 0; ";
            $params = array();
            $params[] = $item_id;                   // item_id
            $params[] = $item_no;                   // item_no
            $result = $this->Db->execute($query, $params);              
            if ($result === false) {
                $errMsg = $this->Db->ErrorMsg();
                // roll back
                $this->failTrans();
                return false;
            }
            
            for($ii=0; $ii<count($result); $ii++){
                // インデックスが公開中であるか
                if($result[$ii]['public_state'] == 1){
                    // 親インデックスが公開されているか
                    if($this->checkParentPublicState($result[$ii]['index_id'])){
                        // 所属する公開中インデックスのコンテンツ数を減らす
                        $del_result = $this->deleteContents($result[$ii]['index_id']);
                        if($del_result === false){
                            $errMsg = $this->Db->ErrorMsg();
                            // roll back
                            $this->failTrans();
                            return false;
                        }
                        // Add private_contents count K.Matsuo 2013/05/07 --start--
                        // インデックスの非公開コンテンツ数を増やす
                        // アイテム編集時非公開アイテムになるため
                        $del_result = $this->addPrivateContents($result[$ii]['index_id']);
                        if($del_result === false){
                            $errMsg = $this->Db->ErrorMsg();
                            // roll back
                            $this->failTrans();
                            return false;
                        }
                        // Add private_contents count K.Matsuo 2013/05/07 --end--
                    }
                }
            }
        }
        // コンテンツ数対応 add count contents 2009/02/17 A.Suzuki --end--
        // 新着情報対応 add delete whatsnew 2009/02/17 A.Suzuki --start--
        $result = $this->deleteWhatsnew($item_id);
        if($result === false){
            $errMsg = $this->Db->ErrorMsg();
            // roll back
            $this->failTrans();
            return false;
        }
        // 新着情報対応 add delete whatsnew 2009/02/17 A.Suzuki --end--
        
        return true;
    }

    /**
     * update item info
     * アイテム情報更新
     * 
     * @param array $item Item information アイテム情報
     *                    array["item_id"|"item_no"|"revision_no"|"item_type_id"|"title"|"title_english"|"language"|"serch_key"|"serch_key_english"|"pub_year"|"pub_month"|"pub_day"]
     * @param string $errMsg Error message エラーメッセージ
     * @param string $warningMsg Warning message 警告メッセージ
     */
    function updateItem($item, &$errMsg, &$warningMsg = ""){
        $query = "UPDATE ". DATABASE_PREFIX ."repository_item ".
                 "SET revision_no = ?, ".
                 "prev_revision_no = ?, ".
                 "title = ?, ".
                 "title_english = ?, ".
                 "language = ?, ".
                 "serch_key = ?, ".
                 "serch_key_english = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ? ";
        if($item['pub_year'] != ""){
            $query .= ", shown_date = ? ";
        }
        $query .= "WHERE item_id = ? AND ".
                  "item_no = ?; ";
        $params = null;
        $params[] = $item['revision_no'];           // revision_no
        $params[] = $item['prev_revision_no'];      // prev_revision_no
        $params[] = $item['title'];                 // title
        $params[] = $item['title_english'];         // title_english
        $params[] = $item['language'];              // language
        $params[] = $item['serch_key'];             // keyword
        $params[] = $item['serch_key_english'];     // keyword_english
        $params[] = $this->mod_user_id;             // mod_user_id
        $params[] = $this->edit_start_date;         // mod_date
        if($item['pub_year'] != ""){
            $params[] = $this->generateDateStr( $item['pub_year'], 
                                                $item['pub_month'], 
                                                $item['pub_day']);  // "shown_date"
        }
        $params[] = $item['item_id'];                   // item_id
        $params[] = $item['item_no'];                   // item_no
        //UPDATE
        $result = $this->Db->execute($query, $params);
        if ($result === false) {
            $errMsg = $this->Db->ErrorMsg();
            // roll back
            $this->failTrans();
            return false;
        }
        
        // Add e-person R.Matsuura 2013/10/23 --start--
        $ret = $this->deleteFeedbackMailAuthorId($item['item_id'], $item['item_no']);
        if($ret === false) {
            return false;
        }
        $authorIdNo = 1;
        if(isset($item['feedback_mailaddress']) && is_array($item['feedback_mailaddress']))
        {
            for ($cnt = 0; $cnt < count($item['feedback_mailaddress']); $cnt++){
                // check mail address
                if ( $item['feedback_mailaddress'][$cnt]['E_MAIL_ADDRESS'] == "") {
                    continue;
                }
                
                // get author_id
                $authorId = $this->getAuthorIdByMailAddress($item['feedback_mailaddress'][$cnt]['E_MAIL_ADDRESS']);
                if($authorId === false){
                    continue;
                }
                
                // insert send feedback mail author id
                $this->insertFeedbackMailAuthorId( $item['item_id'], $item['item_no'], $authorIdNo, $authorId);
                if($result === false){
                    return false;
                }
                
                // set author id no
                $authorIdNo++;
            }
        }
        // Add e-person R.Matsuura 2013/10/23 --end--
        // Add cnri handle T.Ichikawa 2014/09/17 --start--
        if(isset($item['cnri_suffix']) && is_array($item['cnri_suffix']))
        {
            $handleManager = new RepositoryHandleManager($this->Session, $this->Db, $this->TransStartDate);
            for ($cnt = 0; $cnt < count($item['cnri_suffix']); $cnt++){
                // サーバに設定されたprefixからsuffixを抽出する
                try{
                    $suffix = $handleManager->extractCnriSuffix($item['cnri_suffix'][$cnt]['CNRI']);
                    
                    // CNRIの登録を行う
                    $handleManager->registCnriSuffix($item['item_id'], $item['item_no'], $suffix);
                } catch(AppException $ex){
                    $smartyAssign = $this->Session->getParameter('smartyAssign');
                    $warningMsg .= $smartyAssign->getlang($ex->getMessage());
                    $this->debugLog($ex->getMessage(). "::itemId=". $item['item_id']. "::cnri=". $item['cnri_suffix'][$cnt]['CNRI'], __FILE__, __CLASS__, __LINE__);
                }
            }
        }
        // Add cnri handle T.Ichikawa 2014/09/17 --end--
        
        return true;
    }
    
    /**
     * get file no
     * ファイル通番取得
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $attribute_id Attribute id 属性ID
     * @param string $file_thumb Table name テーブル名
     * @return int file no ファイル通番
     */
    function getNewFileNo($item_id, $item_no, $attribute_id, $file_thumb){
        $query = "SELECT count(*) ".
                "FROM ". DATABASE_PREFIX .$file_thumb. " ".
                "WHERE item_id = ? AND ".
                "item_no = ? AND ".
                "attribute_id = ?; ";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = $attribute_id;
        $return = $this->Db->execute($query, $params);
        if($return === false){
            return 0;
        }
        return $return[0]['count(*)']+1;
    }
    
    /**
     * entry file
     * ファイル登録
     * entry to
     *  ・ file name ファイル名
     *  ・ thubnail for PDF PDFのサムネ
     *  ・ file contents(BLOB) ファイル本体(BLOB)
     * 
     * @param array $file_info file information アップロードファイル情報
     *                         array["item_id"|"item_no"|"attribute_id"|"file_no"]
     *                         array["upload"]["mimetype"|"extension"|"file_name"]
     * @param string $errMsg Error message エラーメッセージ
     * @param string $filePath File path ファイルパス
     * @param boolean $delFileFlag Delete file flag ファイル削除フラグ
     * @param boolean $duplicateUpdate Duplicate update flag 更新フラグ
     * @param array $flashList Flash file list Flashファイル一覧
     *                         array[$ii]
     * @return boolean Result 結果
     */
    function entryFile(&$file_info, &$errMsg, $filePath="", $delFileFlag=true, $duplicateUpdate=false, $flashList = null){
        // アイテム登録から移動
        // PDFのプレビュー化処理を追加 2008/07/22 Y.Nakao --start--
        // 外部コマンドをDBから取得する 2008/08/07 Y.Nakao --start--
        // Insert実行前に、uploadファイルがPDFならばサムネイルを作成する
        $prev_flg = sprintf("false"); // サムネイルができたかどうか
        
        // file upload path
        if(strlen($filePath)==0)
        {
            $filePath = WEBAPP_DIR. DIRECTORY_SEPARATOR. "uploads". DIRECTORY_SEPARATOR. "repository". DIRECTORY_SEPARATOR;
        }
        else
        {
            $filePath = rtrim($filePath, '\/').DIRECTORY_SEPARATOR;
        }
        
        // アップロードしたファイルを一時ディレクトリにリネーム
        // (Actionなどでアップロードしたファイルを削除することがあるため、一時ディレクトリにファイルを退避する)
        $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness('businessWorkdirectory');
        $tempDirPath = $businessWorkdirectory->create();
        
        // ファイル名は相対パスで指定されることがあるため、リネーム先のディレクトリにリネーム元と同じ階層を作成する
        $this->makeRecursiveDirectory($tempDirPath, $file_info['upload']['physical_file_name']);
        
        Repository_Components_Util_OperateFileSystem::rename($filePath.$file_info['upload']['physical_file_name'], $tempDirPath. $file_info['upload']['physical_file_name']);
        $filePath = $tempDirPath;
        
        if(strtolower(strtolower($file_info['upload']['extension'])) == "pdf"){
            // ファイル名格納
            $fileName = $file_info['upload']['physical_file_name'];
            
            // Fix 2013/10/28 R.Matsuura --start--
            // Thumbnail generate conditions
            $thumbnailGenerateCondition = 0;
            // popplerのパスを取得
            $query = "SELECT `param_value` ".
                     "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                     "WHERE `param_name` = 'path_poppler';";
            $poppler_path = $this->Db->execute($query);
            if ($poppler_path === false) {
                $errMsg = $this->Db->ErrorMsg();
                $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
                // delete upload file
                if(file_exists($filePath.$fileName)){
                    unlink($filePath.$fileName);
                }
                return false;
            }
            if(strlen($poppler_path[0]['param_value']) >= 1
               && (file_exists($poppler_path[0]['param_value']."pdfinfo.exe")
                   || file_exists($poppler_path[0]['param_value']."pdfinfo")))
            {
                $thumbnailGenerateCondition++;
            }
            // Fix 2013/10/28 R.Matsuura --end--
            
            // コマンドのパスを取得
            $query = "SELECT `param_value` ".
                     "FROM `". DATABASE_PREFIX ."repository_parameter` ".
                     "WHERE `param_name` = 'path_ImageMagick';";
            $cmd_path = $this->Db->execute($query);
            if ($cmd_path === false) {
                $errMsg = $this->Db->ErrorMsg();
                $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
                // delete upload file
                if(file_exists($filePath.$fileName)){
                    unlink($filePath.$fileName);
                }
                return false;
            }
            if(strlen($cmd_path[0]['param_value']) >= 1
               && (file_exists($cmd_path[0]['param_value']."convert")
                   || file_exists($cmd_path[0]['param_value']."convert.exe"))){
                // Add Fix 2013/10/28 R.Matsuura
                $thumbnailGenerateCondition++;
            }
            
            // Add check PDF image format not "" = JPEG2000. Y.Nakao 2014/08/20 --start--
            $catoutput = array();
            exec("cat ".$filePath.$fileName." | grep --text /JPXDecode", $catoutput);
            if(count($catoutput) == 0)
            {
                $thumbnailGenerateCondition++;
            }
            unset($catoutput);
            // Add check PDF image format not "" = JPEG2000. Y.Nakao 2014/08/20 --end--
            
            if($thumbnailGenerateCondition == 3)
            {
                // コマンド  pfdinfoコマンドパス/pdfinfo PDFファイルパス
                $cmd_getVertical = sprintf( $poppler_path[0]['param_value'] . "pdfinfo " . $filePath.$fileName. " | gawk '/Page size/ {print $5}'");
                $cmd_getHorizontal = sprintf( $poppler_path[0]['param_value'] . "pdfinfo " . $filePath.$fileName . " | gawk '/Page size/ {print $3}'");
                // PDFファイルのページサイズを取得
                $pdfPagesize = array();
                exec($cmd_getVertical, $pdfPagesize);
                exec($cmd_getHorizontal, $pdfPagesize);
                if(count($pdfPagesize) === 2){
                    if($pdfPagesize[0] >= 1 && $pdfPagesize[1] >= 1){
                        if($pdfPagesize[1] < $pdfPagesize[0]){
                            // 縦長
                            $cmd = sprintf("\"". $cmd_path[0]['param_value']. "convert\" -quality 100 -density 200x200 -resize 200x ". $filePath.$fileName. "[0] ". $filePath.$fileName. ".png");
                        } else {
                            // 横長
                            $cmd = sprintf("\"". $cmd_path[0]['param_value']. "convert\" -quality 100 -density 200x200 -resize x280 ". $filePath.$fileName. "[0] ". $filePath.$fileName. ".png");
                        }
                        exec($cmd);
                        // Fix 2013/10/28 R.Matsuura --end--
                    }
                }
                if(file_exists($filePath.$fileName.".png")){
                    // サムネイル作成OK
                    $prev_flg = sprintf("true");
                }
            }
        }
        // create image-file thumbnail using gd 2010/02/16 K.Ando --start--
        else if($file_info['upload']['mimetype'] == "image/bmp")
        {
            $fileName = $file_info['upload']['physical_file_name'];
            $image = $this->imagecreatefrombmp($filePath.$fileName); //read bmp file
            
            $result = $this->createThumbnailImage($image , $filePath.$fileName.".png");
            imagedestroy ($image); 
            
            if(file_exists($filePath.$fileName.".png")){
                // creating thumbnail is succeed 
                $prev_flg = sprintf("true");
            }           
            
        }
        else if(strcmp($file_info['upload']['mimetype'],"image/gif" )== 0)
        {
            $fileName = $file_info['upload']['physical_file_name'];
            $image = ImageCreateFromGIF($filePath.$fileName); //read gif file
            
            $result = $this->createThumbnailImage($image , $filePath.$fileName.".png");
            imagedestroy ($image); 
            
            if(file_exists($filePath.$fileName.".png")){
                // creating thumbnail is succeed 
                $prev_flg = sprintf("true");
            }           
        }
        else if(strcmp($file_info['upload']['mimetype'] , "image/jpeg") == 0
        || strcmp($file_info['upload']['mimetype'] , "image/pjpeg")== 0)
        {
            $fileName = $file_info['upload']['physical_file_name'];
            $image = ImageCreateFromJPEG($filePath.$fileName); //read jpeg file
            
            $result = $this->createThumbnailImage($image , $filePath.$fileName.".png");
            imagedestroy ($image); 
            
            if(file_exists($filePath.$fileName.".png")){
                // creating thumbnail is succeed 
                $prev_flg = sprintf("true");
            }           
        }
        else if(strcmp($file_info['upload']['mimetype'] ,"image/png")== 0|| strcmp($file_info['upload']['mimetype'] , "image/x-png")== 0)
        {
            $fileName = $file_info['upload']['physical_file_name'];
            $image = ImageCreateFromPNG($filePath.$fileName); //read png file
            
            $result = $this->createThumbnailImage($image , $filePath.$fileName.".png");
            imagedestroy ($image); 
            
            if(file_exists($filePath.$fileName.".png")){
                // creating thumbnail is succeed 
                $prev_flg = sprintf("true");
            }           
        }
        // create image-file thumbnail using gd 2010/02/16 K.Ando --end--
        
        // 外部コマンドをDBから取得する 2008/08/07 Y.Nakao --end--
        // PDFのプレビュー化処理を追加 2008/07/22 Y.Nakao --end--
        // file contents move to save point
        $fileName = $file_info['upload']['physical_file_name'];
        // Add separate file from DB 2009/04/17 Y.Nakao --start--
        // Move upload file and rename.
        $upload_filepath = $filePath.$fileName;
        
        // Add File replace T.Koyasu 2016/02/29 --start--
        // 登録するファイルが既に存在しているか否か
        $query = "SELECT * ". 
                 " FROM ". DATABASE_PREFIX. "repository_file ". 
                 " WHERE item_id = ? ".
                 " AND item_no = ? ".
                 " AND attribute_id = ? ".
                 " AND file_no = ?;";
        $params = array();
        $params[] = $file_info['item_id'];
        $params[] = $file_info['item_no'];
        $params[] = $file_info['attribute_id'];
        $params[] = $file_info['file_no'];
        $result = $this->executeSql($query, $params);
        
        $businessContentFileTransaction = BusinessFactory::getFactory()->getBusiness("businessContentfiletransaction");
        // count 1 or 0
        if(count($result) === 0){
            $this->insertRepositoryFile($file_info, $upload_filepath, $flashList, $duplicateUpdate);
        } else {
            // アップロードしたファイルパスはNC2で作成されたIDのファイル名となっている
            // 内部でファイルテーブルの更新も行っているため、ファイルパスは物理ファイル名となっている必要がある
            // そのため、一時ディレクトリにコピーする必要がある
            // ファイル名は相対パスで指定されることがあるため、リネーム先のディレクトリにリネーム元と同じ階層を作成する
            $tmpDir = $businessWorkdirectory->create();
            $this->makeRecursiveDirectory($tmpDir, $file_info['upload']['file_name']);
            $uploadFilePath = $tmpDir. $file_info['upload']['file_name'];
            Repository_Components_Util_OperateFileSystem::copy($upload_filepath, $uploadFilePath);
            
            $businessContentFileTransaction->update($file_info['item_id'], $file_info['attribute_id'], $file_info['file_no'], $uploadFilePath, $file_info['upload']['mimetype'], $flashList);
        }
        // Add File replace T.Koyasu 2016/02/29 --end--
        
        // PDFのプレビュー化処理を追加 2008/07/22 Y.Nakao --start--
        // 外部コマンドをDBから取得する 2008/08/07 Y.Nakao --start--
        if($prev_flg == "true"){
            $businessContentFileTransaction->updateFilePrevName($file_info['item_id'], $file_info['attribute_id'], $file_info['file_no'], $file_info['upload']['physical_file_name'].".png");
            
            // PDFのプレビューファイルをBLOBのカラムへ登録
            $ret = $this->Db->updateBlobFile(
                'repository_file',
                'file_prev',
                $filePath.$fileName. ".png", 
                'item_id = '. $file_info['item_id']. " AND ".
                'item_no = '. $file_info['item_no']. " AND ".
                'attribute_id = '. $file_info['attribute_id']. " AND ".
                'file_no = '. $file_info['file_no'],
                'LONGBLOB'
            );
            if ($ret === false) {
                $errMsg = $this->Db->ErrorMsg();
                $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
                // delete upload file and make thumbnail file
                if(file_exists($filePath.$fileName)){
                    unlink($filePath.$fileName);
                    if(file_exists($filePath.$fileName.".png")){
                        unlink($filePath.$fileName.".png");
                    }
                }
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * delete file record
     * ファイル削除
     *
     * @param array $file File information ファイル情報
     *                    array["item_id"|"item_no"|"attribute_id"|"file_no"]
     * @param boolean $file_price Price file flag 課金ファイルフラグ
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function deleteFile($file, $file_price, &$errMsg){
        // ファイル削除用クエリ
        $query = "UPDATE ". DATABASE_PREFIX ."repository_file ".
                 "SET del_user_id = ?, ".
                 "del_date = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ?, ".
                 "is_delete = ? ".
                 "WHERE item_id = ? AND ".
                 "item_no = ? AND ".
                 "attribute_id = ? AND ".
                 "file_no = ?; ";
        // $queryの?を置き換える配列
        $params = null;
        $params[] = $this->del_user_id;               // del_user_id
        $params[] = $this->TransStartDate;  // del_date
        $params[] = $this->del_user_id;               // mod_user_id
        $params[] = $this->TransStartDate;  // mod_date
        $params[] = 1;                      // is_delete
        $params[] = $file['item_id'];
        $params[] = $file['item_no'];
        $params[] = $file['attribute_id'];
        $params[] = $file['file_no'];
        // DELETE実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errMsg = $this->Db->ErrorMsg();
            $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
            return false;
        }
        
        // 2013/6/14-- A.Jin -- ここでflashファイルを削除していたが、課金ファイル削除実行後に、files&flashを削除するように移動した。
        
        if( $file_price ){
            // 課金ファイルの場合、課金情報も削除する
            $query = "UPDATE ". DATABASE_PREFIX ."repository_file_price ".
                     "SET del_user_id = ?, ".
                     "del_date = ?, ".
                     "mod_user_id = ?, ".
                     "mod_date = ?, ".
                     "is_delete = ? ".
                     "WHERE item_id = ? AND ".
                     "item_no = ? AND ".
                     "attribute_id = ? AND ".
                     "file_no = ?; ";
            // $queryの?を置き換える配列
            $params = null;
            $params[] = $this->del_user_id;               // del_user_id
            $params[] = $this->TransStartDate;  // del_date
            $params[] = $this->del_user_id;               // mod_user_id
            $params[] = $this->TransStartDate;  // mod_date
            $params[] = 1;                    
            $params[] = $file['item_id'];
            $params[] = $file['item_no'];
            $params[] = $file['attribute_id'];
            $params[] = $file['file_no'];
            // DELETEE実行
            $result = $this->Db->execute($query, $params);
            if($result === false){
                $errMsg = $this->Db->ErrorMsg();    
                $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
                return false;
            }
        }
        
        // Mod remove physical file T.Koyasu 2016/02/29 --start--
        $businessName = "businessContentfiletransaction";
        $business = BusinessFactory::getFactory()->getBusiness($businessName);
        $business->delete($file['item_id'], $file['attribute_id'], $file['file_no']);
        // Mod remove physical file T.Koyasu 2016/02/29 --end--
        
        return true;
    }
    
    /**
     * upload file info
     * ファイル情報更新
     * 
     * @param array $file file information アップロードファイル情報
     *                    array["item_id"|"item_no"|"attribute_id"|"file_no"]
     *                    array["upload"]["mimetype"|"extension"|"file_name"]
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function updateFileLicense($file, &$errMsg){
        $query = "UPDATE ". DATABASE_PREFIX ."repository_file ".
                 "SET license_id = ?, ".
                 "license_notation = ?, ".
                 "display_name = ?, ".
                 "display_type = ?, ".
                 "pub_date = ?, ".
                 "flash_pub_date = ?, ".
                 "mod_date = ?, ".
                 "mod_user_id = ? ".
                 "WHERE item_id = ? AND ".
                 "item_no = ? AND ".
                 "attribute_id = ? AND ".
                 "file_no = ?; ";
        $params = array();
        $params[] = $file['license_id'];
        $params[] = $file['license_notation'];
        $params[] = $file['display_name'];
        if($file['display_type'] != ""){
            $params[] = $file['display_type'];
        } else {
            $params[] = 0;
        }
        // Modify file, flash immovable. Y.Nakao 2012/02/13 --start--
        // set edit date
        $editDate = str_replace("-", "", $this->edit_start_date);
        $editDate = str_replace(" ", "", $editDate);
        $editDate = str_replace(":", "", $editDate);
        $editDate = str_replace(".", "", $editDate);
        // set file pub date
        $filePubDate = $this->generateDateStr(  $file['embargo_year'], 
                                                $file['embargo_month'],
                                                $file['embargo_day']);
        $filePubDate = str_replace("-", "", $filePubDate);
        $filePubDate = str_replace(" ", "", $filePubDate);
        $filePubDate = str_replace(":", "", $filePubDate);
        $filePubDate = str_replace(".", "", $filePubDate);
        // set flash pub date
        $flashPubDate = $this->generateDateStr(  $file['flash_embargo_year'], 
                                                 $file['flash_embargo_month'],
                                                 $file['flash_embargo_day']);
        $flashPubDate = str_replace("-", "", $flashPubDate);
        $flashPubDate = str_replace(" ", "", $flashPubDate);
        $flashPubDate = str_replace(":", "", $flashPubDate);
        $flashPubDate = str_replace(".", "", $flashPubDate);
        if($file['embargo_flag'] == 1) {
            // 公開日が過去日なら変更なし、未来日なら今日の日付
            if($filePubDate < $editDate)
            {
                $params[] = $this->generateDateStr( $file['embargo_year'], 
                                                    $file['embargo_month'],
                                                    $file['embargo_day']);
            }
            else
            {
                $params[] = $this->edit_start_date;
            }
        } elseif($file['embargo_flag'] == 2) {
            // 個別指定
            $params[] = $this->generateDateStr($file['embargo_year'], 
                                                $file['embargo_month'],
                                                $file['embargo_day']);
        } elseif($file['embargo_flag'] == 4) {
            // do not publish => parameter is 9999/12/31
            $params[] = $this->generateDateStr(9999, 12, 31);
        } else {
            // 会員のみ => 内部的には9999年1月1日として扱う
            $params[] = $this->generateDateStr(9999, 1, 1);
        }
        
        // add flash pub date of multimedia file
        if($file['display_type'] == 2){
        // Add multimedia support 2012/08/27 T.Koyasu -end-
            if($file['flash_embargo_flag'] == 1) {
                // 公開日が過去日なら変更なし、未来日なら今日の日付
                if($flashPubDate < $editDate)
                {
                    $params[] = $this->generateDateStr( $file['flash_embargo_year'], 
                                                        $file['flash_embargo_month'],
                                                        $file['flash_embargo_day']);
                }
                else
                {
                    $params[] = $this->edit_start_date;
                }
            } elseif($file['flash_embargo_flag'] == 2) {
                // 個別指定
                $params[] = $this->generateDateStr( $file['flash_embargo_year'], 
                                                    $file['flash_embargo_month'],
                                                    $file['flash_embargo_day']);
            } else {
                // 会員のみ => 内部的には9999年1月1日として扱う
                $params[] = $this->generateDateStr(9999, 1, 1);
            }
        } else {
            // flash表示以外の場合は空にする
            $params[] = "";
        }
        // Modify file, flash immovable. Y.Nakao 2012/02/13 --end--
        
        $params[] = $this->edit_start_date; // mod_Date
        $params[] = $this->mod_user_id; // mod_user_id
        $params[] = $file['item_id'];
        $params[] = $file['item_no'];
        $params[] = $file['attribute_id'];
        $params[] = $file['file_no'];
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errMsg = $this->Db->ErrorMsg();
            $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
            return false;
        }
        return true;
    }
    
    /**
     * entry file info
     * 課金ファイル情報登録
     *
     * @param array $file file information アップロードファイル情報
     *                    array["item_id"|"item_no"|"attribute_id"|"file_no"]
     *                    array["upload"]["mimetype"|"extension"|"file_name"]
     * @param string $errMsg Error message エラーメッセージ
     * @param boolean $duplicateUpdate Duplicate update flag 更新フラグ
     * @return boolean Result 結果
     */
    function entryFilePrice($file, &$errMsg, $duplicateUpdate=false){
        $params = array();
        $params[] = $file['item_id'];   // item_id
        $params[] = $file['item_no'];   // item_no
        $params[] = $file['attribute_id'];  // attribute_id
        $params[] = $file['file_no'];   // file_no
        $params[] = '0,0';  // price
        $params[] = $this->ins_user_id; // ins_user_id
        $params[] = $this->mod_user_id; // mod_user_id
        $params[] = ''; // del_user_id
        $params[] = $this->edit_start_date; // ins_date
        $params[] = $this->edit_start_date; // mod_date
        $params[] = ''; // del_date
        $params[] = 0;  // is_delete
        if($duplicateUpdate){
            $result = $this->insertOrUpdatePrice($params, $errMsg);
        } else {
            $result = $this->insertFilePrice($params, $errMsg);
        }
        if($result === false){
            $Error_Msg = $this->Db->ErrorMsg();
            $this->failTrans();
            return false;
        }
    }
    
    /**
     * Update file price info
     * 課金ファイル情報更新
     * 
     * @param array $file file information アップロードファイル情報
     *                    array["item_id"|"item_no"|"attribute_id"|"file_no"]
     *                    array["upload"]["mimetype"|"extension"|"file_name"]
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function updatePrice($file, &$errMsg){
        $price = "";
        if(isset($file['embargo_flag']) && intval($file['embargo_flag'])==4)
        {
            // If file not public, set to no price.
            $file['price_num'] = 0;
        }
        // room_idとpriceを登録用に「room_id,price|room_id,price|...」の形に整形
        for($price_cnt=0;$price_cnt<$file['price_num'];$price_cnt++){
            // 空入力をレコード保存しない
            if( $file['room_id'][$price_cnt] == '' && 
                $file['price_value'][$price_cnt] == '') {
                continue;
            }
            if($price_cnt > 0){
                $price .= "|";
            }
            $price .= $file['room_id'][$price_cnt].",".
                    str_replace(",", "", $file['price_value'][$price_cnt]);
        }
        $params = array();
        $params[] = $file['file_no'];
        $params[] = $price;
        $params[] = $this->mod_user_id; // mod_user_id
        $params[] = $this->edit_start_date; // mod_Date
        $params[] = 0;    // is_Dalete
        $params[] = $file['item_id'];
        $params[] = $file['item_no'];
        $params[] = $file['attribute_id'];
        $params[] = $file['file_no'];
        $result = $this->updateFilePrice($params, $errMsg);
        if($result === false){
            $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
            return false;
        }
        return true;
    }
    
    /**
     * entry thumbnail
     * サムネイル登録
     * entry to
     *  ・ file name ファイル名
     *  ・ file contents(BLOB) ファイル本体(BLOB)
     * 
     * @param array $thumbnail file information アップロードファイル情報
     *                         array["item_id"|"item_no"|"attribute_id"|"file_no"]
     *                         array["upload"]["mimetype"|"extension"|"file_name"]
     * @param string $errMsg Error message エラーメッセージ
     * @param string $filePath file path ファイルパス
     * @param bool $delFileFlag Delete file flag ファイル削除フラグ
     * @param boolean $duplicateUpdate Duplicate update flag ファイル更新フラグ
     * @return boolean Result 結果
     */
    function entryThumbnail(&$thumbnail, &$errMsg, $filePath="", $delFileFlag=true, $duplicateUpdate=false){
        // file upload path
        if(strlen($filePath)==0)
        {
            $filePath = WEBAPP_DIR. DIRECTORY_SEPARATOR. "uploads". DIRECTORY_SEPARATOR. "repository". DIRECTORY_SEPARATOR;
        }
        else
        {
            $filePath = rtrim($filePath, '\/').DIRECTORY_SEPARATOR;
        }
        
        if($thumbnail['file_no'] == ""){
            $thumbnail['file_no'] = $this->getNewFileNo($thumbnail['item_id'],
                                                        $thumbnail['item_no'],
                                                        $thumbnail['attribute_id'],
                                                        "repository_thumbnail");
        }
        if($thumbnail['file_no'] == "0"){
            array_push($errMsg, "thumbnail no is not set 0");
            // delete upload file
            if(file_exists($filePath.$fileName)){
                unlink($filePath.$fileName);
            }
            return false;
        }
        
        // ディレクトリセパレータが含まれないようにファイル名を抽出する
        $thumbnailName = Repository_Components_Util_Stringoperator::extractFileNameFromFilePath($thumbnail['upload']['file_name']);
        
        // Thumbnail
        $params = array();
        $params[] = $thumbnail['item_id'];  // item_id
        $params[] = $thumbnail['item_no'];  // item_no
        $params[] = $thumbnail['attribute_id']; // "attribute_id"
        $params[] = $thumbnail['file_no'];  // "file_no"
        $params[] = $thumbnailName;  // "file_name"
        $params[] = $thumbnail['show_order'];  // "show_order"
        $params[] = $thumbnail['upload']['mimetype'];       // "mime_type"
        $params[] = $thumbnail['upload']['extension'];  // "extension"
        $params[] = "";             // "file"
        $params[] = $thumbnail['item_type_id']; // "item_type_id"
        $params[] = $this->ins_user_id;         // "ins_user_id"
        $params[] = $this->mod_user_id;         // "mod_user_id"
        $params[] = "";                     // "del_user_id"
        $params[] = $this->edit_start_date; // "ins_date"
        $params[] = $this->edit_start_date; // "mod_date" 
        $params[] = "";             // "del_date"
        $params[] = 0;                  // "is_delete"
        //INSERT実行
        if($duplicateUpdate){
            $result = $this->insertOrUpdateThumbnail($params, $errMsg);
        } else {
            $result = $this->insertThumbnail($params, $errMsg);
        }
        if ($result === false) {
            $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
            // delete upload file
            if(file_exists($filePath.$fileName)){
                unlink($filePath.$fileName);
            }
            return false;       // 登録失敗。errorページをmaple.iniに書いておくこと。
        }
        // BLOB設定
        $fileName = $thumbnail['upload']['physical_file_name'];
        //ファイルをBLOBのカラムへ登録
        $ret = $this->Db->updateBlobFile(
            'repository_thumbnail',
            'file',
            $filePath . $fileName, 
            'item_id = '. $params[0]. " AND ".
            'item_no = '. $params[1]. " AND ".
            'attribute_id = '. $params[2]. " AND ".
            'file_no = '. $params[3],
            'LONGBLOB'
        );
        if ($ret === false) {
            $errMsg = "repository_error_failed_entry_thumbnail";
            $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
            // delete upload file
            if(file_exists($filePath.$fileName)){
                unlink($filePath.$fileName);
            }
            return false;
        }
        
        // Fix thumbnail width, height 2012/02/03 Y.Nakao --start--
        $imgSize = getimagesize($filePath.$fileName);
        if(isset($imgSize[0]))
        {
            $thumbnail['width'] = $imgSize[0];
        }
        if(isset($imgSize[1]))
        {
            $thumbnail['height'] = $imgSize[1];
        }
        // Fix thumbnail width, height 2012/02/03 Y.Nakao --end--
        
        // delete upload file
        if($delFileFlag)
        {
            if(file_exists($filePath.$fileName)){
                unlink($filePath.$fileName);
            }
        }
        return true;
    }
    
    /**
     * delete thumbnail record
     * サムネイル削除
     *
     * @param array $file file information ファイル情報
     *                    array["item_id"|"item_no"|"attribute_id"|"file_no"]
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function deleteThumbnail($file, &$errMsg){
        // ファイル削除用クエリ
        $query = "UPDATE ". DATABASE_PREFIX ."repository_thumbnail ".
                 "SET del_user_id = ?, ".
                 "del_date = ?, ".
                 "mod_user_id = ?, ".
                 "mod_date = ?, ".
                 "is_delete = ? ".
                 "WHERE item_id = ? AND ".
                 "item_no = ? AND ".
                 "attribute_id = ? AND ".
                 "file_no = ?; ";
        // $queryの?を置き換える配列
        $params = array();
        $params[] = $this->del_user_id;               // del_user_id
        $params[] = $this->TransStartDate;  // del_date
        $params[] = $this->del_user_id;               // mod_user_id
        $params[] = $this->TransStartDate;  // mod_date
        $params[] = 1;                    
        $params[] = $file['item_id'];
        $params[] = $file['item_no'];
        $params[] = $file['attribute_id'];
        $params[] = $file['file_no'];
        // DELETEE実行
        $result = $this->Db->execute($query, $params);
        if($result === false){
            $errMsg = $this->Db->ErrorMsg();
            $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
            return false;
        }
        return true;
    }
    
    /**
     * entry meta data info
     * メタデータ登録
     *
     * @param array $metadata entry metadata info メタデータ一覧
     *                        array["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                        array["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                        array["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param string $errMsg Error message エラーメッセージ
     * @return true : success
     *         false: error
     */
    function entryMetadata(&$metadata, &$errMsg){
        switch($metadata["input_type"]){
            case 'select':
                // if null, continue;
                if(strlen($metadata["attribute_value"]) == 0){
                    break;
                }
            case 'text':
            case 'textarea':
            case 'link':
            case 'checkbox':
            case 'radio':
            case 'date':
            case 'heading':
                $params = array();
                $params[] = $metadata["attribute_value"];   // attribute_value
                $params[] = $this->mod_user_id;             // mod_user_id
                $params[] = $this->edit_start_date;     // mod_date
                $params[] = 0;  // is_delete
                $params[] = $metadata["item_id"];       // item_id
                $params[] = $metadata["item_no"];       // item_no
                $params[] = $metadata["attribute_id"];  // attribute_id
                $params[] = $metadata["attribute_no"];  // attribute_no
                // UPDATE
                $result = $this->updateItemAttr($params, $errMsg);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $this->failTrans(); //ROLLBACK
                    return false;   
                }   
                // UPDATE件数を検査 => 更新無しであればINSERT
                $isUpdate = $this->Db->affectedRows();
                // UPDATE失敗時はそのレコードが無いものとし、新規に作成する
                if(!$isUpdate) {
                    $params = array();
                    $params[] = $metadata["item_id"];       // item_id
                    $params[] = $metadata["item_no"];       // item_no
                    $params[] = $metadata["attribute_id"];  // attribute_id
                    $params[] = $metadata["attribute_no"];  // attribute_no
                    $params[] = $metadata["attribute_value"];   // attribute_value
                    $params[] = $metadata["item_type_id"];  // item_type_id
                    $params[] = $this->ins_user_id;             // ins_user_id
                    $params[] = $this->mod_user_id;             // mod_user_id
                    $params[] = "";                         // del_user_id
                    $params[] = $this->edit_start_date;     // ins_date
                    $params[] = $this->edit_start_date;     // mod_date
                    $params[] = "";                         // del_date
                    $params[] = 0;  // is_delete
                    //INSERT
                    $result = $this->insertItemAttr($params, $errMsg);
                    if ($result === false) {
                        $errMsg = $this->Db->ErrorMsg()." ".$metadata["attribute_id"];
                        $this->failTrans(); // ROLLBACK
                        return false;   
                    }
                }
                break;
            case 'name':
                $NameAuthority = new NameAuthority($this->Session, $this->Db);
                // 典拠テーブルに著者名を登録
                $result = $NameAuthority->entryNameAuthority($metadata, $errMsg, false);
                if($result === false){
                    $this->failTrans(); //ROLLBACK
                    return false;
                }
                
                $metadata["author_id"] = $result;
                
                $params = array();
                $params[] = $metadata["family"];    // family
                $params[] = $metadata["name"];      // name
                $params[] = $metadata["family_ruby"];    // family_ruby
                $params[] = $metadata["name_ruby"];      // name_ruby
                $params[] = $metadata["e_mail_address"];    // e_mail_address
                $params[] = $metadata["author_id"];      // author_id
                $params[] = $this->mod_user_id; // mod_user_id
                $params[] = $this->edit_start_date; // mod_date
                $params[] = 0;  // is_delete
                $params[] = $metadata["item_id"];   // item_id
                $params[] = $metadata["item_no"];   // item_no
                $params[] = $metadata["attribute_id"];  // attribute_id
                $params[] = $metadata["personal_name_no"];  // personal_name_no
                // UPDATE   
                $result = $this->updatePersonalName($params, $errMsg);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $this->failTrans(); //ROLLBACK
                    return false;   
                }
                // UPDATE件数を検査 => 更新無しであればINSERT
                $isUpdate = $this->Db->affectedRows();
                // UPDATE失敗時はそのレコードが無いものとし、新規に作成する
                if(!$isUpdate) {
                    $params = array();
                    $params[] = $metadata["item_id"];   // item_id
                    $params[] = $metadata["item_no"];   // item_no
                    $params[] = $metadata["attribute_id"];  // attribute_id
                    $params[] = $metadata["personal_name_no"];  // personal_name_no
                    $params[] = $metadata["family"];    // family
                    $params[] = $metadata["name"];      // name
                    $params[] = $metadata["family_ruby"];    // family_ruby
                    $params[] = $metadata["name_ruby"];      // name_ruby
                    $params[] = $metadata["e_mail_address"];    // e_mail_address
                    $params[] = $metadata["item_type_id"];  // item_type_id
                    $params[] = $metadata["author_id"];    // author_id
                    $params[] = $this->ins_user_id; // ins_user_id
                    $params[] = $this->mod_user_id; // mod_user_id
                    $params[] = ""; // del_user_id
                    $params[] = $this->edit_start_date; // ins_date
                    $params[] = $this->edit_start_date; // mod_date
                    $params[] = ""; // del_date
                    $params[] = 0;  // is_delete
                    //INSERT
                    $result = $this->insertPersonalName($params, $errMsg);
                    if ($result === false) {
                        $errMsg = $this->Db->ErrorMsg();
                        $this->failTrans(); // ROLLBACK
                        return false;
                    }   
                }
                break;
            case 'biblio_info':
                $params = array();
                $params[] = $metadata["biblio_name"];   // biblio_name
                $params[] = $metadata["biblio_name_english"];   // biblio_name_english
                $params[] = $metadata["volume"];        // volume
                $params[] = $metadata["issue"];         // issue
                $params[] = $metadata["start_page"];    // start_page
                $params[] = $metadata["end_page"];      // end_page
                $params[] = $metadata["date_of_issued"];// date_of_issued
                $params[] = $this->mod_user_id; // mod_user_id
                $params[] = $this->edit_start_date; // mod_date
                $params[] = 0;  // is_delete
                $params[] = $metadata["item_id"];   // item_id
                $params[] = $metadata["item_no"];   // item_no
                $params[] = $metadata["attribute_id"];  // attribute_id
                $params[] = $metadata["biblio_no"];     // biblio_no
                // UPDATE
                $result = $this->updateBilioInfo($params, $errMsg);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $this->failTrans(); // ROLLBACK
                    return false;
                }
                // UPDATE件数を検査 => 更新無しであればINSERT
                $isUpdate = $this->Db->affectedRows();
                // UPDATE失敗時はそのレコードが無いものとし、新規に作成する
                if(!$isUpdate) {
                    $params = array();
                    $params[] = $metadata["item_id"];   // item_id
                    $params[] = $metadata["item_no"];   // item_no
                    $params[] = $metadata["attribute_id"];  // attribute_id
                    $params[] = $metadata["biblio_no"];     // biblio_no
                    $params[] = $metadata["biblio_name"];   // biblio_name
                    $params[] = $metadata["biblio_name_english"];   // biblio_name_english
                    $params[] = $metadata["volume"];        // volume
                    $params[] = $metadata["issue"];         // issue
                    $params[] = $metadata["start_page"];    // start_page
                    $params[] = $metadata["end_page"];      // end_page
                    $params[] = $metadata["date_of_issued"];// date_of_issued
                    $params[] = $metadata["item_type_id"];  // item_type_id
                    $params[] = $this->ins_user_id; // ins_user_id
                    $params[] = $this->mod_user_id; // mod_user_id
                    $params[] = ""; // del_user_id
                    $params[] = $this->edit_start_date; // ins_date
                    $params[] = $this->edit_start_date; // mod_date
                    $params[] = ""; // del_date
                    $params[] = 0;  // is_delete
                    //INSERT
                    $result = $this->insertBiblioInfo($params, $errMsg);
                    if ($result === false) {
                        $errMsg = $this->Db->ErrorMsg();
                        $this->failTrans();   // ROLLBACK
                        return false;
                    }
                }
                break;
            default:
                break;
        }
        return true;
    }
    
    /**
     * entry position index info
     * 所属インデックス登録
     * 
     * @param array $item Item information アイテム情報
     *                    array["item"][$ii]["item_id"|"item_no"]
     * @param array $index Index information インデックス情報
     *                     array[$ii]["index_id"]
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function entryPositionIndex($item, $index, &$errMsg){
        // Fix check index_id Y.Nakao 2013/06/07 --start--
        // count($index) == 0 の場合に所属インデックスを消すように修正
        
        // Add private_contents count K.Matsuo 2013/05/21 --start--
        // 登録されなくなったインデックスの非公開コンテンツ数を減らす
        $Result_List = null;
        $error_msg = null;
        $result = $this->getItemIndexData($item["item_id"],$item["item_no"],$Result_List,$error_msg);
        if($result === false){
            $this->failTrans();                 //トランザクション失敗を設定(ROLLBACK)
            return false;
        }
        for($ii=0; $ii<count($Result_List["position_index"]); $ii++) {
            $this->deletePrivateContents($Result_List["position_index"][$ii]['index_id']);
        }
        // Add private_contents count K.Matsuo 2013/05/21 --end--
        // 一旦全ての所属インデックスを論理削除
        $result = $this->deletePositionIndexTableData($item["item_id"], 
                                                    $item["item_no"], 
                                                    $this->mod_user_id, 
                                                    $errMsg);
        if($result === false){
            // ROLLBACK
            $this->failTrans();
            return false;  
        }
        
        
        $query_index = "INSERT INTO ". DATABASE_PREFIX ."repository_position_index ".
                 "(item_id, item_no, index_id, custom_sort_order, ".
                 "ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, ".
                 "del_date, is_delete) ".
                 "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
        
        // 再登録
        $ins_params = array();
        $ins_params[0] = $item["item_id"];  // item_id
        $ins_params[1] = $item["item_no"];  // item_no
        $ins_params[2] = 0;                // index_id
        $ins_params[3] = 0;                 // custom_sort_order
        $ins_params[4] = $this->ins_user_id;    // ins_user_id
        $ins_params[5] = $this->mod_user_id;    // mod_user_id
        $ins_params[6] = "";    // del_user_id
        $ins_params[7] = $this->edit_start_date;    // ins_date
        $ins_params[8] = $this->edit_start_date;    // mod_date
        $ins_params[9] = "";    // del_date
        $ins_params[10] = 0;    // is_delete
        
        for($ii=0; $ii<count($index); $ii++) {
            // もしそのインデックスが既に論理削除状態の場合復活させる。
            $query = "SELECT * ".
                    "FROM ". DATABASE_PREFIX ."repository_position_index ".     // 所属インデックステーブル
                    "WHERE item_id = ? AND ".       // アイテムID
                    "item_no = ? AND ".             // アイテムNo
                    "index_id = ? ; ";            // インデックスNo
            // $queryの?を置き換える配列
            $params = null;
            $params[] = $item["item_id"];
            $params[] = $item["item_no"];
            $params[] = $index[$ii]['index_id'];
            // SELECT実行
            $result = $this->Db->execute($query, $params);
            if($result === false){
                $errMsg = $this->Db->ErrorMsg();
                $this->failTrans();                //トランザクション失敗を設定(ROLLBACK)
                return false;
            }
            if(count($result)>0) {
                $query = "UPDATE ". DATABASE_PREFIX ."repository_position_index ".
                     "SET is_delete = ?, ".
                     "mod_user_id = ?, ".
                     "mod_date = ? ,".
                     "del_user_id  = ?, ".
                     "del_date = ? ".
                     "WHERE item_id = ? AND ".
                     "item_no = ? AND index_id = ?; ";
                // $queryの?を置き換える配列
                $params = null;
                $params[] = 0;
                // Fix 2013/10/29 R.Matsuura --start--
                $params[] = $this->mod_user_id;
                $params[] = $this->edit_start_date;
                // Fix 2013/10/29 R.Matsuura --end--
                // Fix 2014/03/04 S.Suzuki --start--
                $params[] = "";
                $params[] = "";
                // Fix 2014/03/04 S.Suzuki --end--
                $params[] = $item["item_id"];
                $params[] = $item["item_no"];
                $params[] = $index[$ii]['index_id'];
                // UPDATE実行
                $result = $this->Db->execute($query, $params);
                if($result === false){
                    $errMsg = $this->Db->ErrorMsg();
                    $this->failTrans();                //トランザクション失敗を設定(ROLLBACK)
                    return false;
                }
            } else {
                // インデックスが存在しない場合、挿入
                
                // Add custom_sort_order A.Jin 2012/12/26 --start--
                // ------------------------------------------------
                // 対象インデックスのcustom_sort_orderのMax値を取得
                 // ------------------------------------------------
                $max_sort_order = 0;
                $query = "SELECT MAX(custom_sort_order) AS MAX_SORT_ORDER ".
                    "FROM ". DATABASE_PREFIX ."repository_position_index ".     // 所属インデックステーブル
                    "WHERE index_id = ? ".       // インデックスNo
                    "GROUP BY index_id;";
                $params = null;
                $params[] = $index[$ii]['index_id'];
                //SELECT実行
                $max_result = $this->Db->execute($query, $params);
                if($max_result === false){
                    $errMsg = $this->Db->ErrorMsg();
                    $this->failTrans();                //トランザクション失敗を設定(ROLLBACK)
                    return false;
                }
                if(count($max_result)==1) {
                    $max_sort_order = $max_result[0]['MAX_SORT_ORDER'];
                }
                // Add custom_sort_order A.Jin 2012/12/26 --end--
                
                // ins_paramsを更新する
                $ins_params[2] = $index[$ii]['index_id'];   // "index_id"
                $ins_params[3] = $max_sort_order+1; // "custom_sort_order"
                
                //INSERT実行
                $result = $this->Db->execute($query_index, $ins_params);
                if ($result === false) {
                    //必要であればSQLエラー番号・メッセージ取得
                    $errMsg = $this->Db->ErrorMsg();
                    // ROLLBACK 
                    $this->failTrans();
                    return false;
                }
            }
            // Add private_contents count K.Matsuo 2013/05/07 --start--
            // entryPositionIndex時にはアイテムはすべて非公開のため非公開コンテンツ数を増やす。
            $result = $this->addPrivateContents($index[$ii]['index_id']);
            // Add private_contents count K.Matsuo 2013/05/07 --end--
        }
        return true;
        // Fix check index_id Y.Nakao 2013/06/07 --end--
    }
    
    /**
     * entry link info
     * リンク情報登録 
     * entry to
     *  ・アイテム登録先インデックス position index
     *  ・アイテム間リンク reference
     * 
     * @param array $item Item information アイテム情報
     *                    array["item_id"|"item_no"]
     * @param array $link Link information リンク情報
     *                    array[$ii]["item_id"|"item_no"|"relation"]
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function entryReference($item, $link, &$errMsg){
        // 参照(リンク)用クエリー (挿入)
        $query_reference = "INSERT INTO ". DATABASE_PREFIX ."repository_reference ".
                 "(org_reference_item_id, org_reference_item_no, ".
                 "dest_reference_item_id, dest_reference_item_no, reference, ".
                 "ins_user_id, mod_user_id, del_user_id, ins_date, mod_date, ".
                 "del_date, is_delete) ".
                 "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
        
        // 一旦全ての参照を論理削除
        $result = $this->deleteReference($item["item_id"], 
                                        $item["item_no"], 
                                        $this->mod_user_id, 
                                        $errMsg);
        if($result === false){
            // ROLLBACK
            $this->failTrans();
            return false;  
        }
        // 再登録
        $ins_params = array();
        $ins_params[0] = $item["item_id"];  // org_reference_item_id
        $ins_params[1] = $item["item_no"];  // org_reference_item_no
        $ins_params[2] = 0;    // dest_reference_item_id
        $ins_params[3] = 0;    // dest_reference_item_no
        $ins_params[4] = "";    // reference
        $ins_params[5] = $this->ins_user_id;    // ins_user_id
        $ins_params[6] = $this->mod_user_id;    // mod_user_id
        $ins_params[7] = "";    // del_user_id
        $ins_params[8] = $this->edit_start_date;    // ins_date
        $ins_params[9] = $this->edit_start_date;    // mod_date
        $ins_params[10] = "";   // del_date
        $ins_params[11] = 0;    // is_delete
        for($ii=0; $ii<count($link); $ii++) {
            // もしその既にリンクが論理削除状態の場合復活させる。
            $query = "SELECT * ".
                    "FROM ". DATABASE_PREFIX ."repository_reference ".  // 参照テーブル
                    "WHERE org_reference_item_id = ? AND ".     // アイテムID
                    "org_reference_item_no = ? AND ".           // アイテムNo
                    "dest_reference_item_id = ? AND ".          // 参照先ID
                    "dest_reference_item_no = ? AND ".          // 参照先No
                    "is_delete = ?; ";              // 削除フラグ
            // $queryの?を置き換える配列
            $params = null;
            $params[] = $item["item_id"];
            $params[] = $item["item_no"];
            $params[] = $link[$ii]['item_id'];  // "dest_reference_item_id"
            $params[] = $link[$ii]['item_no'];  // "dest_reference_item_no"
            $params[] = 1;
            // SELECT実行
            $result = $this->Db->execute($query, $params);
            if($result === false){
                $errMsg = $this->Db->ErrorMsg();
                // ROLLBACK
                $this->failTrans(); 
                return false;
            }
            if(count($result)>0) {
                $query = "UPDATE ". DATABASE_PREFIX ."repository_reference ".
                     "SET reference = ?, ".                     // 関係性
                     "del_user_id = ?, ".                       // 削除ユーザID
                     "mod_date = ?, ".                          // 更新日
                     "del_date = ?, ".                          // 削除日
                     "is_delete = ? ".                          // 削除フラグ
                     "WHERE org_reference_item_id = ? AND ".    // アイテムID
                     "org_reference_item_no = ? AND ".          // アイテムNo
                     "dest_reference_item_id = ? AND ".         // 参照先ID
                     "dest_reference_item_no = ?;".             // 参照先No
                // $queryの?を置き換える配列
                $params = null;
                $params[] = $link[$ii]['relation']; // "reference"
                $params[] = "";
                $params[] = $this->edit_start_date;
                $params[] = "";
                $params[] = 0;
                $params[] = $item["item_id"];
                $params[] = $item["item_no"];
                $params[] = $link[$ii]['item_id'];  // "dest_reference_item_id"
                $params[] = $link[$ii]['item_no'];  // "dest_reference_item_no"
                // UPDATE実行
                $result = $this->Db->execute($query, $params);
                if($result === false){
                    $errMsg = $this->Db->ErrorMsg();
                    // ROLLBACK 
                    $this->failTrans();
                    return false;
                }       
            } else {
                $ins_params[2] = $link[$ii]['item_id']; // "dest_reference_item_id"
                $ins_params[3] = $link[$ii]['item_no']; // "dest_reference_item_no"
                $ins_params[4] = $link[$ii]['relation'];// "reference"
                //INSERT実行
                $result = $this->Db->execute($query_reference, $ins_params);            
                if ($result === false) {
                    //必要であればSQLエラー番号・メッセージ取得
                    $errMsg = $this->Db->ErrorMsg();
                    // ROLLBACK
                    $this->failTrans();
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * All registration information scrutiny
     * 全登録情報精査
     *
     * @param array $item_attr_type Item attr type information メタデータ項目情報
     *                              array[$ii]["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param array $item_num_attr Item number information メタデータ数情報
     *                             array[$ii]
     * @param array $item_attr Item attr information メタデータ情報
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"personal_name_no"|"family"|"name"|"family_ruby"|"name_ruby"|"e_mail_address"|"item_type_id"|"author_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"show_order"|"mime_type"|"extension"|"file"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"biblio_no"|"biblio_name"|"biblio_name_english"|"volume"|"issue"|"start_page"|"end_page"|"date_of_issued"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"file_no"|"file_name"|"display_name"|"display_type"|"show_order"|"mime_type"|"extension"|"prev_id"|"file_prev"|"file_prev_name"|"license_id"|"license_notation"|"pub_date"|"flash_pub_date"|"item_type_id"|"browsing_flag"|"cover_created_flag"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"|"price"]
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"supple_no"|"item_type_id"|"supple_weko_item_id"|"supple_title"|"supple_title_en"|"uri"|"supple_item_type_name"|"mime_type"|"file_id"|"supple_review_status"|"supple_review_date"|"supple_reject_status"|"supple_reject_date"|"supple_reject_reason"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     *                         array[$ii][$jj]["item_id"|"item_no"|"attribute_id"|"attribute_no"|"attribute_value"|"item_type_id"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @param string $type Input type 入力タイプ
     * @param string $errMsg Error message エラーメッセージ
     * @param string $warning Warning message 警告メッセージ
     * @return boolean Result 結果
     */
    function checkEntryInfo($item_attr_type, $item_num_attr, $item_attr, $type, &$err_msg, &$warning){
        $smarty_assign = $this->Session->getParameter("smartyAssign");
        if(is_null($smarty_assign))
        {
            $this->setLangResource();
            $smarty_assign = $this->Session->getParameter("smartyAssign");
        }
        
        // ------------------------------------------------------------
        // 必須入力検査 
        // ------------------------------------------------------------
        for($ii=0; $ii<count($item_attr_type); $ii++) {
            if( $item_attr_type[$ii]['input_type']=='file' ||
                $item_attr_type[$ii]['input_type']=='file_price' ||
                $item_attr_type[$ii]['input_type']=='thumbnail'){
                if($type == "all" || $type == "file"){
                    // 必須指定でないメタデータはスルー
                    if( $item_attr_type[$ii]['is_required']!=1){
                        continue;
                    }
                    // 必須ファイル／サムネイルが最低一件入力済みか検査
                    $IsInput = false;
                    for($jj=0; $jj<$item_num_attr[$ii]; $jj++) {
                        if($item_attr[$ii][$jj] != null){
                            if(isset($item_attr[$ii][$jj]["upload"])) {
                                $IsInput = true;
                                break;
                            }
                        }
                    }
                    // 未入力の場合はエラーメッセージを登録
                    if($IsInput == false) {
                        $msg = $smarty_assign->getLang("repository_input_error");
                        array_push($err_msg, sprintf($msg, $item_attr_type[$ii]['attribute_name']));
                    }
                }
                if($type == "all" || $type == "license"){
                    for($jj=0; $jj<$item_num_attr[$ii]; $jj++) {
                        if(!isset($item_attr[$ii][$jj]['upload'])){
                            continue;
                        }
                        // 価格精査
                        if($item_attr_type[$ii]['input_type']=='file_price'){
                            
                            // 個数保持
                            $loop_num = $item_attr[$ii][$jj]['price_num'];
                            // 価格
                            $price_array = $item_attr[$ii][$jj]['price_value'];
                            // グループ
                            $room_id_array = $item_attr[$ii][$jj]['room_id'];
                            $room_id = array_count_values($item_attr[$ii][$jj]['room_id']);
                            for($price_num=0;$price_num<$loop_num;$price_num++){
                                // 設定された価格が正しいかどうかチェック
                                // 同じルームIDに価格が設定されていないかチェックする
                                if($room_id["$room_id_array[$price_num]"] > 1){
                                    // 同じルームIDに複数価格が設定されている場合エラー
                                    $msg = $smarty_assign->getLang("repository_item_error_same_groups");
                                    array_push($err_msg, $msg);
                                    $istest = false;
                                }
                                if(strlen($room_id_array[$price_num]) == 0 || 
                                    strlen($price_array[$price_num]) == 0 || 
                                    !is_numeric($price_array[$price_num])){
                                    // ルームIDもしくは価格が設定されていない場合、価格が数字ではない場合、エラー
                                    $msg = $smarty_assign->getLang("repository_item_error_price");
                                    array_push($err_msg, $msg);
                                    $istest = false;
                                }
                            }
                        }
                        // オープンアクセス日指定のみ精査
                        if(!isset($item_attr[$ii][$jj]['embargo_flag']) || $item_attr[$ii][$jj]['embargo_flag'] != 2){
                            continue;
                        }
                        // 公開日指定のみチェック
                        if( checkdate($item_attr[$ii][$jj]['embargo_month'], 
                                        $item_attr[$ii][$jj]['embargo_day'], 
                                        $item_attr[$ii][$jj]['embargo_year']) == false ) {
                            $msg = $smarty_assign->getLang("repository_item_error_date");
                            array_push($err_msg, sprintf($msg, $item_attr[$ii][$jj]['upload']['file_name']));   
                        }
                    }
                }
            } else {
                // 必須指定でないメタデータはスルー
                if( $item_attr_type[$ii]['is_required']!=1){
                    continue;
                }
                if($type == "all" || $type == "meta"){
                    // テキスト入力項目が入力済みか検査, 必須入力は最低1属性に入っていれば良い
                    // 氏名は名入力時に姓が入っていなければNG
                    $cnt = 0;
                    for($jj=0; $jj<count($item_attr[$ii]); $jj++) {
                        if($item_attr_type[$ii]['input_type']=='name'){
                            if( $item_attr[$ii][$jj]['family'] != '' && $item_attr[$ii][$jj]['family'] != RepositoryConst::BLANK_WORD) {
                                $cnt++;
                            }
                            if ($item_attr[$ii][$jj]['family'] === RepositoryConst::BLANK_WORD) {
                                $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                            }
                        } else if($item_attr_type[$ii]['input_type']=='checkbox'){
                            if($item_attr[$ii][$jj] != 0 && $item_attr[$ii][$jj] != RepositoryConst::BLANK_WORD){
                                $cnt++;
                            }
                            if ($item_attr[$ii][$jj] === RepositoryConst::BLANK_WORD) {
                                $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                            }
                        // Add radio,select K.Matsuo 2013/10/02 --start--
                        } else if(  $item_attr_type[$ii]['input_type']=='radio' ||
                                    $item_attr_type[$ii]['input_type']=='select'){
                            if($item_attr[$ii][$jj] != '' && $item_attr[$ii][$jj] != RepositoryConst::BLANK_WORD){
                                $cnt++;
                            }
                            if ($item_attr[$ii][$jj] === RepositoryConst::BLANK_WORD) {
                                $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                            }
                        // Add radio,select K.Matsuo 2013/10/02 --end--
                        // Add contents page Y.Nakao 2010/08/06 --start--
                        } else if($item_attr_type[$ii]['input_type']=='heading'){
                            $values = explode("|", $item_attr[$ii][$jj]);
                            for ($kk = 0; $kk < count($values); $kk++) {
                                if($values[$kk] != '' && $values[$kk] != RepositoryConst::BLANK_WORD) {
                                    $cnt++;
                                }
                                if ($values[$kk] === RepositoryConst::BLANK_WORD) {
                                    $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                    array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                                    break;
                                }
                            }
                        // Add contents page Y.Nakao 2010/08/06 --end--
                        } else if($item_attr_type[$ii]['input_type']=='biblio_info'){
                            $str = $item_attr[$ii][$jj]["biblio_name"].
                                   $item_attr[$ii][$jj]["biblio_name_english"].
                                   $item_attr[$ii][$jj]["volume"].
                                   $item_attr[$ii][$jj]["issue"].
                                   $item_attr[$ii][$jj]["spage"].
                                   $item_attr[$ii][$jj]["epage"].
                                   $item_attr[$ii][$jj]["date_of_issued"];
                            if(strlen($str) > 0 && preg_match('/'.RepositoryConst::BLANK_WORD.'/', $str) == 0){
                                $cnt++;
                            }
                            if(preg_match('/'.RepositoryConst::BLANK_WORD.'/', $str) > 0) {
                                $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                            }
                        } else if($item_attr_type[$ii]['input_type']=='date'){
                            if(strlen($item_attr[$ii][$jj]['date']) > 0 && $item_attr[$ii][$jj]['date'] != RepositoryConst::BLANK_WORD){
                                $cnt++;
                            }
                            if($item_attr[$ii][$jj]['date'] === RepositoryConst::BLANK_WORD) {
                                $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                            }
                        } else if($item_attr_type[$ii]['input_type']=='link'){
                            $values = explode("|", $item_attr[$ii][$jj]);
                            if ($values[0] !== RepositoryConst::BLANK_WORD && $values[0] != "") {
                                $cnt++;
                            }
                            if ($values[0] === RepositoryConst::BLANK_WORD || $values[0] =="" && count($values) == 1) {
                                $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                            }
                        } else {
                            $values = explode("|", $item_attr[$ii][$jj]);
                            for ($kk = 0; $kk < count($values); $kk++) {
                                if($values[$kk] != '' && $values[$kk] != RepositoryConst::BLANK_WORD) {
                                    $cnt++;
                                }
                                if ($values[$kk] === RepositoryConst::BLANK_WORD) {
                                    $msg = $smarty_assign->getLang("repository_item_error_required_item");
                                    array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                                    break;
                                }
                            }
                        }
                    }
                    // 未入力の場合はエラメッセージを登録して前画面(ファイル入力)に戻る
                    if($cnt <= 0) {
                        $msg = $smarty_assign->getLang("repository_item_error_metadata");
                        array_push($err_msg, sprintf($msg, $item_attr_type[$ii]["attribute_name"]));
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Item basic information scrutiny
     * アイテム基本情報精査
     *
     * @param array $item Item information アイテム情報
     *                    array["item_id"|"item_no"|"revision_no"|"item_type_id"|"title"|"title_english"|"language"|"serch_key"|"serch_key_english"|"pub_year"|"pub_month"|"pub_day"]
     * @param string $errMsg Error message エラーメッセージ
     * @param string $warning Warning message 警告メッセージ
     * @return boolean Result 結果
     */
    function checkBaseInfo($item, &$err_msg, &$warning){
        $smarty_assign = $this->Session->getParameter("smartyAssign");
        if(is_null($smarty_assign))
        {
            $this->setLangResource();
            $smarty_assign = $this->Session->getParameter("smartyAssign");
        }
        
        //タイトルが空だった場合には重複チェックしない  2009/08/26 K.Ito --start--
        /*
        if(($item['title'] == ' ' || $item['title'] == '') && ($item['title_english'] == ' ' || $item['title_english'] == '')) {
            $msg = $smarty_assign->getLang("repository_item_error_title");
            array_push($err_msg, $msg);
        }
        */
        // タイトルの空チェック
        if( trim($item['title']) == "" && trim($item['title_english']) == "") {
            $msg = $smarty_assign->getLang("repository_item_error_title");
            array_push($warning, $msg);
            //WEKOの言語判定
            if($this->Session->getParameter("_lang") == "japanese"){
                //論文の言語判定
                //WEKOの設定言語だけに依存せず、論文の言語で判定するので文字列はベタ書きです
                if($item['language'] == "ja"){
                    $str = "※タイトルが「タイトル無し」になります";
                }else{
                    $str = "※タイトル(英)が「no title」になります";
                }
            }else{
                //論文の言語判定
                if($item['language'] == "ja"){
                    $str = "*The title becomes \"タイトル無し\"";
                }else{
                    $str = "*An English title becomes \"no title\"";
                }
            }
            array_push($warning, $str);
        }
        else if ($item['title'] == RepositoryConst::BLANK_WORD || $item['title_english'] == RepositoryConst::BLANK_WORD) {
            $msg = $smarty_assign->getLang("repository_item_error_required_item");
            array_push($err_msg, $msg);
        }
        else{
            // タイトルはDB照合し、重複タイトルをチェック
            // Add registered info save action 2009/02/10 Y.Nakao --start--
            $query = "SELECT item_id FROM ". DATABASE_PREFIX ."repository_item ".
                     "WHERE title = ? AND ".
                     "title_english = ? AND ".
                     "is_delete = 0 AND ".
                     " NOT (item_id = ? AND ".
                     " item_no = ?) ".
                     "LIMIT 0, 1; ";
            $params = array();
            $params[] = $item['title'];
            $params[] = $item['title_english'];
            $params[] = $item['item_id'];
            $params[] = $item['item_no'];
            $result = $this->Db->execute($query, $params);
            if($result === false){
                $msg = $this->Db->ErrorMsg();
                array_push($err_msg, $msg);
                return 'error';
            }
            if (count($result) > 0) {
                $msg = $smarty_assign->getLang("repository_item_error_same_title");
                if(!isset($warning) || !is_array($warning)){
                    $warning = array();
                }
                array_push($warning, $msg);
            }
        }
        //タイトルが空だった場合には重複チェックしない 2009/08/26 K.Ito --end--
        
        // アイテム公開日のチェック
        if( checkdate(
                intval($item['pub_month']),
                intval($item['pub_day']),
                intval($item['pub_year'])) == false ) {
            $msg = $smarty_assign->getLang("repository_item_error_date");
            $tmp = $smarty_assign->getLang("repository_search_item");
            array_push($err_msg, sprintf($msg, $tmp));
        }
        return true;
    }
    
    /**
     * Registration index check
     * 登録インデックスチェック
     *
     * @param array $indice Index inforamtion 検査対象インデックス情報
     *                      array[$ii]
     * @param string $errMsg Error message エラーメッセージ
     * @param string $warning Warning message 警告メッセージ
     * @return boolean Result 結果
     */
    function checkIndex($indice, &$err_msg, &$warning){
        $smarty_assign = $this->Session->getParameter("smartyAssign");
        if(is_null($smarty_assign))
        {
            $this->setLangResource();
            $smarty_assign = $this->Session->getParameter("smartyAssign");
        }
        if(count($indice) < 1) {
            $msg = $smarty_assign->getLang("repository_item_error_index");
            array_push($err_msg, $msg);
        }
        return true;
    }
    
    /**
     * Return date of data (int) in the date string
     * 年月日のデータ(int)を日付文字列にして返す
     * 
     * @param int $year Year 年
     * @param int $month Month 月
     * @param int $day Day 日
     * @return string Date 日付
     */
    function generateDateStr($year, $month, $day){
        $str_year = strval($year);
        $str_month = strval($month);
        $str_day = strval($day);
        // 0付加
        if(intval($month)<10){ $str_month = '0' . $str_month; }
        if(intval($day)<10){ $str_day = '0' . $str_day; }
        // 結合
        return $str_year . '-' . $str_month . '-' . $str_day . ' ' . '00:00:00.000';
    }
    
    // create image-file thumbnail using gd 2010/02/16 K.Ando --start--
    /**
     * create thumnail image using GD
     * ファイルプレビュー画像を作成
     *
     * @param resource $image image file 画像イメージ
     * @param string $filepath upload file path ファイルパス
     * @return boolean Result 結果
     */
    function createThumbnailImage(&$image, $filepath)
    {
        $basewidth  = 280;
        $baseheight = 200;
        $width = ImageSX($image);  //image width (pixel)
        $height = ImageSY($image); //image height (pixel)
        $new_width = ImageSX($image);  
        $new_height = ImageSY($image); 
        
            if($baseheight <= $height || $basewidth <= $width )
        {
            // calc resize-image size 
            if($width > $height){
                $new_width = 280;
                $rate = $new_width / $width;    //resize-rate
                $new_height = $rate * $height;
            }
            else
            {
                $new_height = 200;
                $rate = $new_height / $height; // resize-rate
                $new_width = $rate * $width;
            }
            // initialize resize image
            $new_image = ImageCreateTrueColor($new_width, $new_height);
            // generate resize image
            $result = ImageCopyResampled($new_image,$image,0,0,0,0,$new_width,$new_height,$width,$height);
            if(!$result)
            {
                return $result;
            }
            
            // ファイルに保存する
            $result = ImagePNG($new_image, $filepath);
            if($result)
            {
                imagedestroy ($new_image); //サムネイル用イメージIDの破棄 ※3
            }
            return $result;
        }
        else
        {
            // ファイルに保存する
            $result = ImagePNG($image, $filepath); 
            return $result;
        }
    }
    
    /**
     * Convert bmp to GD Object
     * BMPに変換
     *
     * @param string $src Input file path 入力ファイルパス
     * @param string $dest Output file path 出力ファイルパス
     * @return boolean Result 結果
     */
    function ConvertBMP2GD($src, $dest = false) {
        if(!($src_f = fopen($src, "rb"))) {
            return false;
        }
        if(!($dest_f = fopen($dest, "wb"))) {
            return false;
        }
        $header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f, 14));
        $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
        fread($src_f, 40));
        
        extract($info);
        extract($header);
        
        if($type != 0x4D42) {    // signature "BM"
            return false;
        }
        
        $palette_size = $offset - 54;
        $ncolor = $palette_size / 4;
        $gd_header = "";
        // true-color vs. palette
        $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
        $gd_header .= pack("n2", $width, $height);
        $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
        if($palette_size) {
            $gd_header .= pack("n", $ncolor);
        }
        // no transparency
        $gd_header .= "\xFF\xFF\xFF\xFF";
        
        fwrite($dest_f, $gd_header);
        
        if($palette_size) {
            $palette = fread($src_f, $palette_size);
            $gd_palette = "";
            $j = 0;
            while($j < $palette_size) {
                $b = $palette{$j++};
                $g = $palette{$j++};
                $r = $palette{$j++};
                $a = $palette{$j++};
                $gd_palette .= "$r$g$b$a";
            }
                $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
                fwrite($dest_f, $gd_palette);
        }
        
        $scan_line_size = (($bits * $width) + 7) >> 3;
        $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;
        
        for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
            // BMP stores scan lines starting from bottom
            fseek($src_f, $offset + (($scan_line_size + $scan_line_align) * $l));
            $scan_line = fread($src_f, $scan_line_size);
            if($bits == 24) {
                $gd_scan_line = "";
                $j = 0;
                while($j < $scan_line_size) {
                    $b = $scan_line{$j++};
                    $g = $scan_line{$j++};
                    $r = $scan_line{$j++};
                    $gd_scan_line .= "\x00$r$g$b";
                }
            }
            else if($bits == 8) {
                $gd_scan_line = $scan_line;
            }
            else if($bits == 4) {
                $gd_scan_line = "";
                $j = 0;
                while($j < $scan_line_size) {
                    $byte = ord($scan_line{$j++});
                    $p1 = chr($byte >> 4);
                    $p2 = chr($byte & 0x0F);
                    $gd_scan_line .= "$p1$p2";
                }
                $gd_scan_line = substr($gd_scan_line, 0, $width);
            }
            else if($bits == 1) {
                $gd_scan_line = "";
                $j = 0;
                while($j < $scan_line_size) {
                    $byte = ord($scan_line{$j++});
                    $p1 = chr((int) (($byte & 0x80) != 0));
                    $p2 = chr((int) (($byte & 0x40) != 0));
                    $p3 = chr((int) (($byte & 0x20) != 0));
                    $p4 = chr((int) (($byte & 0x10) != 0));
                    $p5 = chr((int) (($byte & 0x08) != 0));
                    $p6 = chr((int) (($byte & 0x04) != 0));
                    $p7 = chr((int) (($byte & 0x02) != 0));
                    $p8 = chr((int) (($byte & 0x01) != 0));
                    $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
            }
            $gd_scan_line = substr($gd_scan_line, 0, $width);
            }
            
            fwrite($dest_f, $gd_scan_line);
        }
        fclose($src_f);
        fclose($dest_f);
        return true;
    }
    
    /**
     * create GD image by bmp  
     * GD画像を作成する
     *
     * @param string $filename bmp file path ファイルパス
     * @return resource Image イメージ
     */
    function imagecreatefrombmp($filename) {
        $tmp_name = $filename."bk"; 
        if($this->ConvertBMP2GD($filename, $tmp_name)) 
        {
            $img = imagecreatefromgd($tmp_name);
            unlink($tmp_name);
            return $img;
        }
        return false;
    }
    // create image-file thumbnail using gd 2010/02/16 K.Ando --end--
    
    // Add Contributor(Posted agency) A.Suzuki 2011/12/13 --start--
    /**
     * Update ins_user_id For Contributor
     * 挿入ユーザIDを更新
     *
     * @param int $item_id Item id アイテムID
     * @param string $user_id User id ユーザID
     * @return boolean Result 結果
     */
    function updateInsertUserIdForContributor($item_id, $user_id)
    {
        // Update ins_user_id
        $query = "UPDATE ".DATABASE_PREFIX.RepositoryConst::DBTABLE_REPOSITORY_ITEM." ".
                 "SET ins_user_id = ? ".
                 "WHERE item_id = ? ".
                 "AND item_no = ?;";
        $params = array();
        $params[] = $user_id; // ins_user_id
        $params[] = $item_id; // item_id
        $params[] = 1;        // item_no
        $result = $this->Db->execute($query, $params);
        if($result===false){
            return false;
        }
        
        return true;
    }
    // Add Contributor(Posted agency) A.Suzuki 2011/12/13 --end--
    
    /**
     * Get isValidFfmpeg parameter
     * isValidFfmpeg取得
     *
     * @return boolean isValidFfmpeg ffmpegが有効か
     */
    public function getIsValidFfmpeg()
    {
        if(!isset($this->isValidFfmpeg)){
            $this->checkPathForFfmpeg();
        }
        
        return $this->isValidFfmpeg;
    }
    
    // Add multimedia support 2012/08/27 T.Koyasu -start-
    /**
     * check ffmpeg command
     * ffmpegコマンドパスチェック
     */
    private function checkPathForFfmpeg()
    {
        $this->isValidFfmpeg = false;
        
        $query = "SELECT param_value ".
                 " FROM ". DATABASE_PREFIX. RepositoryConst::DBTABLE_REPOSITORY_PARAMETER.
                 " WHERE param_name = ? ";
        $params = array();
        $params[] = "path_ffmpeg";
        $result = $this->Db->execute($query, $params);
        if($result === false || count($result) != 1)
        {
            $errMsg = $this->Db->ErrorMsg();
            $this->Db->failTrans();
            return false;
        }
        $ffmpegDirPath = $result[0]["param_value"];
        
        if(file_exists($ffmpegDirPath. "ffmpeg") || file_exists($ffmpegDirPath. "ffmpeg.exe")){
            // output of ffmpeg command is stderr
            $cmd = $ffmpegDirPath. "ffmpeg -version 2>&1";
            
            // $returnValue == 0 -> success
            //              != 0 -> failed
            exec($cmd, $out);
            if(preg_match('/ffmpeg version/i', $out[0]) === 1){
                $this->isValidFfmpeg = true;
            }
        }
    }
    
    /**
     * A file with a specific mimetype converted to flash files, returns the path list of the flash file after return
     * 特定のmimetypeを持つファイルをflashファイルに変換し、返還後のflashファイルのパス一覧を返す
     *
     * @param string $uploadFilePath File path ファイルパス
     * @param string $extension Extension 拡張子
     * @param string $mimetype mimetype mimetype
     * @param string $errMsg Error message エラーメッセージ
     * @return array Flash file list flash化したファイルの一覧
     *               array[$ii]
     */
    public function convertFileToFlv($uploadFilePath, $extension, $mimeType, &$errMsg)
    {
        $flashList = null;
        if(!isset($this->isValidFfmpeg)){
            $this->checkPathForFfmpeg();
        }
        
        /**
         * convertable mimetype list
            
            audio/3gpp
            audio/mp3
            audio/mpeg
            audio/mpegurl
            audio/mpg
            audio/ogg
            audio/tta
            audio/vnd.wave
            audio/x-matroska
            audio/x-monkeys-audio
            audio/x-mpeg
            audio/x-mpegurl
            audio/x-omg
            audio/x-tta
            audio/x-twinvq
            audio/x-twinvq-plugin
            audio/x-wav
            video/3gpp
            video/avi
            video/mp4
            video/mpeg
            video/mpg
            video/msvideo
            video/ogg
            video/quicktime
            video/wavelet
            video/x-flv
            video/x-m4v
            video/x-matroska
            video/x-mpeg
            video/x-mpeg2
            video/x-mpeg2a
            video/x-ms-asf
            video/x-msvideo
            video/x-ms-wmv
            application/vnd.smaf
            application/x-smaf
            application/ogg
         */
        
        // ffmpeg有効時
        if($this->isValidFfmpeg){
            // 基本的にはMIMETYPEから動画ファイルかを判別する(nutだけは拡張子で判定)
            if(preg_match('/^(audio|video)\/((x-(m(4v|s-asf|svideo|s-wmv)|flv|matroska|monkeys-audio|mpeg(url|2|2a|)|omg|tta|twinvq(-plugin|)|wav))|(3gpp|avi|mp([3-4]|eg(url|)|g)|ogg)|tta|vnd\.wave|msvideo|quicktime|wavelet)$/', $mimeType) === 1 || 
               preg_match('/^application\/(vnd\.smaf|x-smaf|ogg)$/', $mimeType) === 1 || 
               $extension == "nut"){
                // コマンドのパスを取得
                $query = "SELECT `param_value` ".
                         "FROM `". DATABASE_PREFIX. RepositoryConst::DBTABLE_REPOSITORY_PARAMETER. "` ".
                         "WHERE `param_name` = 'path_ffmpeg';";
                $result = $this->Db->execute($query);
                if ($result === false) {
                    $errMsg = $this->Db->ErrorMsg();
                    $this->failTrans();             //トランザクション失敗を設定(ROLLBACK)
                    return false;
                }
                $ffmpegDirPath = $result[0]['param_value'];
                
                if(file_exists($ffmpegDirPath."ffmpeg") || file_exists($ffmpegDirPath."ffmpeg.exe")){
                    // create temp directory
                    $this->infoLog("businessWorkdirectory", __FILE__, __CLASS__, __LINE__);
                    $businessWorkdirectory = BusinessFactory::getFactory()->getBusiness('businessWorkdirectory');
                    $tempDirPath = $businessWorkdirectory->create();
                    $tempDirPath = substr($tempDirPath, 0, -1);
                    
                    // コマンドを実行し、FLVファイルを生成
                    // audio/video file -> FLV ffmpegを使用
                    // サンプリング周波数
                    $samplingRate = RepositoryConst::FFMPEG_DEFAULT_SAMPLING_RATE;
                    // ビットレート
                    $bitRate = RepositoryConst::FFMPEG_DEFAULT_BIT_RATE;
                    $tempFilePath = $tempDirPath. DIRECTORY_SEPARATOR. "weko.flv";
                    $cmd = $ffmpegDirPath. "ffmpeg -i ". $uploadFilePath. " -y -b ". $bitRate. " -ar ". $samplingRate. " ". $tempFilePath. " 2>&1";
                    exec($cmd, $out, $result);
                    if($result !== 0){
                        $errMsg = "ffmpeg convert error";
                        return array();
                    }
                    
                    $flashList = array();
                    array_push($flashList, $tempFilePath);
                }
            }
        }
        
        return $flashList;
    }
    // Add multimedia support 2012/08/27 T.Koyasu -end-
    
    // Add multimedia support 2013/10/02 K.Matsuo --start--
    /**
     * swap show_order
     * 表示順序入替
     *
     * @param array $file1 File information ファイル情報
     *                     array["item_id"|"item_no"|"file_no"|"attribute_id"]
     * @param array $file2 File information ファイル情報
     *                     array["item_id"|"item_no"|"file_no"|"attribute_id"]
     * @param string $dbName
     * @param string $errMsg Error message エラーメッセージ
     * @return boolean Result 結果
     */
    function swapFileShowOrder($file1, $file2, $dbName, &$errMsg)
    {
        // replace file1's show_order and file2's show_order
        $query = "UPDATE ". DATABASE_PREFIX .$dbName." ".
                 "SET show_order = ?, ".
                 "mod_date = ?, ".
                 "mod_user_id = ? ".
                 "WHERE item_id = ? AND ".
                 "item_no = ? AND ".
                 "attribute_id = ? AND ".
                 "file_no = ?; ";
        $params = array();
        $params[] = $file1['show_order'];
        $params[] = $this->edit_start_date;
        $params[] = $this->mod_user_id;
        $params[] = $file1['item_id'];
        $params[] = $file1['item_no'];
        $params[] = $file1['attribute_id'];
        $params[] = $file1['file_no'];
        $ret = $this->Db->execute($query, $params);
        if($ret === false){
            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 ); //主メッセージとログIDを指定して例外を作成
            $exception->setDetailMsg( $this->Db->ErrorMsg() );         //詳細メッセージ設定
            $this->failTrans();                                     //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
        }
        $query = "UPDATE ". DATABASE_PREFIX .$dbName." ".
                 "SET show_order = ?, ".
                 "mod_date = ?, ".
                 "mod_user_id = ? ".
                 "WHERE item_id = ? AND ".
                 "item_no = ? AND ".
                 "attribute_id = ? AND ".
                 "file_no = ?; ";
        $params = array();
        $params[] = $file2['show_order'];
        $params[] = $this->edit_start_date;
        $params[] = $this->mod_user_id;
        $params[] = $file2['item_id'];
        $params[] = $file2['item_no'];
        $params[] = $file2['attribute_id'];
        $params[] = $file2['file_no'];
        $ret = $this->Db->execute($query, $params);
        if($ret === false){
            $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 ); //主メッセージとログIDを指定して例外を作成
            $exception->setDetailMsg( $this->Db->ErrorMsg() );         //詳細メッセージ設定
            $this->failTrans();                                     //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
        }
        return true;
    }
    
    // Add e-person 2013/10/22  R.Matsuura --start--
    /**
     * insert send feedback mail author id
     * 利用統計フィードバックメール送信著者挿入
     *
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @param int $authorIdNo Author id number 著者ID通番
     * @param int $authorId Author id 著者ID
     */
    public function insertFeedbackMailAuthorId($itemId, $itemNo, $authorIdNo, $authorId)
    {
        $query = "INSERT INTO ". DATABASE_PREFIX ."repository_send_feedbackmail_author_id (" .
                "item_id, " .
                "item_no, " .
                "author_id_no, " .
                "author_id ) " .
                "VALUES( ?, ?, ?, ? );";
        // バインド変数設定
        $param_send_feedback_author_id = array();
        $param_send_feedback_author_id[] = intval( $itemId );
        $param_send_feedback_author_id[] = intval( $itemNo );
        $param_send_feedback_author_id[] = intval( $authorIdNo );
        $param_send_feedback_author_id[] = intval( $authorId );
        
        // Run query
        $result = $this->Db->execute( $query, $param_send_feedback_author_id );
        if($result === false){
            $this->failTrans(); // ROLLBACK
            return false;
        }
    }
    
    /**
     * delete record from send_feedback_author_id table by itemID and itemNo
     * 利用統計フィードバックメール送信著者削除
     *
     * @param int $itemId Item id アイテムID
     * @param int $itemNo Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function deleteFeedbackMailAuthorId($itemId, $itemNo)
    {
        $query = "DELETE FROM ".DATABASE_PREFIX."repository_send_feedbackmail_author_id ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ;";
        $params = array();
        $params[] = $itemId;
        $params[] = $itemNo;
        // Run query
        $result = $this->Db->execute($query, $params);
        if($result === false)
        {
            $this->failTrans(); // ROLLBACK
            return false;
        }
        return true;
    }
    
    /**
     * get Author ID by mail address
     * 著者ID取得
     *
     * @param string $feedbackMailaddress Mail address メールアドレス
     * @return author_id Author id 著者ID
     */
    public function getAuthorIdByMailAddress($feedbackMailaddress)
    {
        // get author_id
        $select_query =  "SELECT author_id " .
                         "FROM ". DATABASE_PREFIX ."repository_external_author_id_suffix " .
                         "WHERE prefix_id = 0 " .
                         "AND suffix = ? " .
                         "AND is_delete = 0 " .
                         "ORDER BY author_id ASC; ";
        $params_select = array();
        $params_select[] = $feedbackMailaddress;
        // Run query
        $result_select = $this->Db->execute( $select_query, $params_select );
        if($result_select === false || count($result_select) < 1){
            return false;
        }
        return $result_select[0]['author_id'];
    }
    // Add e-person 2013/10/22  R.Matsuura --end--
    
    /**
     * Registering a new file
     * ファイルを新規登録する
     *
     * @param array $fileInfo Table preserving information of the registered file 登録するファイルのテーブル保存用情報
     * @param string $insertFilePath The path of the registration file 登録するファイルのパス
     * @param string $insertFlashFilePathList Path list of flash files to be registered 登録するflashファイルのパス一覧
     * @param bool $duplicateUpdate Overwrite flag 上書きフラグ
     */
    private function insertRepositoryFile(&$fileInfo, $insertFilePath, $insertFlashFilePathList, $duplicateUpdate=false){
        $businessContentFileTransaction = BusinessFactory::getFactory()->getBusiness("businessContentfiletransaction");
        $fileInfo["file_no"] = $businessContentFileTransaction->insert($fileInfo['item_id'], $fileInfo['attribute_id'], $insertFilePath, $insertFlashFilePathList);
        
        // ディレクトリセパレータが含まれないようにファイル名を抽出する
        $fileName = Repository_Components_Util_Stringoperator::extractFileNameFromFilePath($fileInfo['upload']['file_name']);
        
        // Insert
        $params = array();
        $params[] = $fileInfo['item_id'];  // item_id
        $params[] = $fileInfo['item_no'];  // item_no
        $params[] = $fileInfo['attribute_id']; // "attribute_id"
        $params[] = $fileInfo['file_no'];  // "file_no"
        $params[] = $fileName;  // "file_name"
        $params[] = $fileInfo['display_name']; // "display_name"
        if($fileInfo['display_type'] != ""){
            $params[] = $fileInfo['display_type']; // "display_type"
        } else {
            $params[] = 0;  // "display_type"空
        }
        $params[] = $fileInfo['show_order'];  // "show_order"
        $params[] = $fileInfo['upload']['mimetype'];       // "mime_type"
        $params[] = $fileInfo['upload']['extension'];  // "extension"
        $params[] = 0;  // PDF_preview_ID Add separate file from DB 2009/04/20 Y.Nakao 
        $params[] = ""; // "file_prev", まずは空で登録 2008/07/22 追加
        $params[] = "";             // "file_prev_name" まずは空で登録
        $params[] = $fileInfo['license_id'];   // "license_id"
        $params[] = $fileInfo['license_notation']; // "license_notation"
        $params[] = ""; // "pub_date"
        $params[] = ""; // "flash_pub_date"
        $params[] = $fileInfo['item_type_id']; // "item_type_id"
        $params[] = 0;  // "browsing_flag"
        $params[] = 0;  // "create_cover_flag"
        $params[] = $this->ins_user_id; // "ins_user_id"
        $params[] = $this->mod_user_id; // "mod_user_id"
        $params[] = ""; // "del_user_id"
        $params[] = $this->edit_start_date; // "ins_date"
        $params[] = $this->edit_start_date; // "mod_date" 
        $params[] = ""; // "del_date"
        $params[] = 0;  // "is_delete"
        //INSERT実行
        if($duplicateUpdate){
            $query = "INSERT INTO ". DATABASE_PREFIX ."repository_file ".
                 "VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
            $query .= " ON DUPLICATE KEY UPDATE  ".
                  " display_name=VALUES(`display_name`), ".
                  " display_type=VALUES(`display_type`), show_order=VALUES(`show_order`), ".
                  " prev_id=VALUES(`prev_id`), file_prev=VALUES(`file_prev`), ".
                  " file_prev_name=VALUES(`file_prev_name`), ".
                  " license_id=VALUES(`license_id`), license_notation=VALUES(`license_notation`), ".
                  " pub_date=VALUES(`pub_date`), item_type_id=VALUES(`item_type_id`), ".
                  " browsing_flag=VALUES(`browsing_flag`), cover_created_flag=0, mod_user_id=VALUES(`mod_user_id`), ".
                  " del_user_id=VALUES(`del_user_id`), mod_date=VALUES(`mod_date`), ".
                  " del_date=VALUES(`del_date`), is_delete=VALUES(`is_delete`); ";
        } else {
            $query = "INSERT INTO ". DATABASE_PREFIX ."repository_file ".
                     " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?); ";
        }
        //INSERT実行
        $this->executeSql($query, $params);
    }
    
    /**
     * Create the file name from (with path) directories recursively
     * ファイル名（パス付）からディレクトリを再帰的に作成する
     *
     * @param string $baseDir ディレクトリの作成元ディレクトリ
     * @param string $fileNameWithPath ファイル名（パス付）
     */
    private function makeRecursiveDirectory($baseDir, $fileNameWithPath) {
        // ファイル名は相対パスで指定されることがあるため、リネーム先のディレクトリにリネーム元と同じ階層を作成する
        $filePathSplited = preg_split('/[\/\\\\]+/', $fileNameWithPath);
        if(count($filePathSplited) > 1) {
            $relativePath = $baseDir;
            for($ii = 0; $ii < count($filePathSplited)-1; $ii++) {
                $relativePath .= $filePathSplited[$ii]."/";
                Repository_Components_Util_OperateFileSystem::mkdir($relativePath);
            }
        }
    }
    
    /**
     * Entry model of item base information
     * アイテム基本情報の雛形を登録する
     *
     * @param int $item_id Item ID アイテムID
     * @param int $item_no Item No アイテム通番
     * @param int $item_type_id Item type ID アイテムタイプID
     * @param string $title Title タイトル
     * @param string $title_english English title 英語タイトル
     * @param string $language Language 言語
     */
    public function entryItemModel($item_id, $item_no, $item_type_id, $title, $title_english, $language) {
        $item = array();
        $item["item_id"] = $item_id;
        $item["item_no"] = $item_no;
        $item["title"] = $title;
        $item["title_english"] = $title_english;
        $item["language"] = $language;
        $item["item_type_id"] = $item_type_id;
        $item["serch_key"] = "";
        $item["serch_key_english"] = "";
        $item["pub_year"] = "";
        $item["pub_month"] = "";
        $item["pub_day"] = "";
        $result = $this->entryItem($item, $error);
        if($result === false){
            // upload failed
            $this->errorLog($error, __FILE__, __CLASS__, __LINE__);
            $exception = new AppException($error);
            $exception->addError($error);
            throw $exception;
        }
    }

}

?>
