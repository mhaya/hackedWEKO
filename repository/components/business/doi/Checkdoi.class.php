<?php

/**
 * Check grant doi business class
 * DOI付与チェックビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Checkdoi.class.php 70924 2016-08-09 07:28:57Z keiya_sugimoto $
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
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Result of check grant DOI class
 * DOI付与チェック結果クラス
 *
 */
require_once WEBAPP_DIR. '/modules/repository/components/business/doi/Resultcheckdoi.class.php';
/**
 * Const for WEKO class
 * WEKO用定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryConst.class.php';

/**
 * Check grant doi business class
 * DOI付与チェックビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Doi_Checkdoi extends BusinessBase
{
    /**
     * Junii2 mapping of ISBN
     * ISBNのjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_ISBN = RepositoryConst::JUNII2_ISBN;
    /**
     * Junii2 mapping of ISSN
     * ISSNのjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_ISSN = RepositoryConst::JUNII2_ISSN;
    /**
     * Junii2 mapping of title
     * タイトルのjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_TITLE = RepositoryConst::JUNII2_TITLE;
    /**
     * Junii2 mapping of journal title
     * 雑誌名のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_JTITLE = RepositoryConst::JUNII2_JTITLE;
    /**
     * Junii2 mapping of volume
     * 巻のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_VOLUME = RepositoryConst::JUNII2_VOLUME;
    /**
     * Junii2 mapping of issue
     * 号のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_ISSUE = RepositoryConst::JUNII2_ISSUE;
    /**
     * Junii2 mapping of start page
     * 開始ページのjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_SPAGE = RepositoryConst::JUNII2_SPAGE;
    /**
     * Junii2 mapping of end page
     * 終了ページのjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_EPAGE = RepositoryConst::JUNII2_EPAGE;
    /**
     * Junii2 mapping of date of issued
     * 発行日のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_DATE_OF_ISSUED = RepositoryConst::JUNII2_DATE_OF_ISSUED;
    /**
     * Junii2 mapping of publisher
     * 出版者のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_PUBLISHER = RepositoryConst::JUNII2_PUBLISHER;
    /**
     * Junii2 mapping of language
     * 言語のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_LANGUAGE = RepositoryConst::JUNII2_LANGUAGE;
    /**
     * Junii2 mapping of fullTextURL
     * fullTextURLのjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_FULL_TEXT_URL = RepositoryConst::JUNII2_FULL_TEXT_URL;
    /**
     * Junii2 mapping of creator
     * 著者のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_CREATOR = RepositoryConst::JUNII2_CREATOR;
    /**
     * Junii2 mapping of biblio info
     * 書誌情報のjunii2マッピング
     *
     * @var string
     */
    const JUNII2_MAPPING_BIBLIO = 'jtitle,volume,issue,spage,epage,dateofissued';
    /**
     * Parameter name of review flag
     * 査読フラグのパラメータ名
     *
     * @var string
     */
    const PARAMETER_REVIEW_FLG = 'review_flg';
    /**
     * Parameter name of whether or not item auto public
     * アイテムが自動公開かどうかのパラメータ名
     *
     * @var string
     */
    const PARAMETER_ITEM_AUTO_PUBLIC = 'item_auto_public';
    
    /**
     * Status of item regist/edit from screen
     * 画面からのアイテム登録/編集の場合のステータス
     *
     * @var int
     */
    const STATUS_FOR_REGISTRATION = 0;
    /**
     * Status of DOI lump-sum
     * DOI一括付与の場合のステータス
     *
     * @var int
     */
    const CHECKING_STATUS_ITEM_MANAGEMENT = 1;
    /**
     * Status of item regist/edit from import and SWORD
     * インポート・SWORDからのアイテム登録/編集の場合のステータス
     *
     * @var int
     */
    const CHECKING_STATUS_IMPORT_SWORD = 2;
    
    /**
     * Status of item regist/edit from SWORD normal mode
     * SWORD通常モードからのアイテム登録/編集の場合のステータス
     *
     * @var int
     */
    const CHECKING_STATUS_SWORD_NORMAL = 0;
    /**
     * Status of item regist/edit from SWORD DOI change mode
     * SWORD DOI変更モードからのアイテム登録/編集の場合のステータス
     *
     * @var int
     */
    const CHECKING_STATUS_SWORD_DOI_CHANGE = 1;
    
    /**
     * Status of use JaLC DOI
     * JaLC DOIを使用する場合のステータス
     *
     * @var int
     */
    const TYPE_JALC_DOI = 0;
    /**
     * Status of use JaLC CrossRef DOI
     * JaLC CrossRef DOIを使用する場合のステータス
     *
     * @var int
     */
    const TYPE_CROSS_REF = 1;
    /**
     * Status of use National Diet Library JaLC DOI
     * 国立国会図書館JaLC DOIを使用する場合のステータス
     *
     * @var int
     */
    const TYPE_LIBRARY_JALC_DOI = 2;
    /**
     * Status of use JaLC Datacite DOI
     * JaLC Datacite DOIを使用する場合のステータス
     *
     * @var int
     */
    const TYPE_DATACITE = 3;
    
    /**
     * Status of being able to grant DOI
     * DOI付与可能な場合のステータス
     *
     * @var int
     */
    const CAN_GRANT_DOI = 0;
    /**
     * Status of being unable to grant DOI
     * DOI付与不可能な場合のステータス
     *
     * @var int
     */
    const CANNOT_GRANT_DOI = 1;
    /**
     * DOI registered
     * DOI登録済み
     *
     * @var int
     */
    const DOI_STATUS_GRANTED = 1;
    /**
     * DOI withdrawn already
     * DOI取り下げ済み
     *
     * @var int
     */
    const DOI_STATUS_DROPED = 2;
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Check whether or not be able to grant DOI
     * DOIの発番可否をチェックする
     *
     * @param int $item_id Item id 
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param string $suffix DOI suffix value
     *                       DOI suffix値
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD) 
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode) 
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @return Object Structure of result of check DOI grant
     *                DOI付与チェック結果の構造体
     */
    public function checkDoiGrant($item_id, $item_no, $type, $suffix, $status=self::STATUS_FOR_REGISTRATION, $changeMode=self::CHECKING_STATUS_SWORD_NORMAL)
    {
        $resultCheckDoi = new ResultCheckDoi();
        $resultCheckDoi->isGrantDoi= true;
        
        // WEKO設定関連のDOI付与チェック
        $this->checkDoiForWekoSetting($status, $type, $resultCheckDoi);
        if(!$resultCheckDoi->isGrantDoi)
        {
            return $resultCheckDoi;
        }
        
        // NII資源タイプ関連のDOI付与チェック
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        $nii_type = $this->getNiiType($item_type_id);
        $this->checkDoiForNiiTypeSetting($nii_type, $type, $resultCheckDoi);
        if(!$resultCheckDoi->isGrantDoi)
        {
            return $resultCheckDoi;
        }
        
        // インデックス関連のDOI付与チェック
        $this->checkDoiForIndexSetting($item_id, $item_no, $resultCheckDoi);
        if(!$resultCheckDoi->isGrantDoi)
        {
            return $resultCheckDoi;
        }
        
        // DOI suffix入力値関連のチェック
        // suffix=空文字：入力値指定なし
        // suffix=null：入力値に影響しない条件
        if(isset($suffix))
        {
            if($type == self::TYPE_LIBRARY_JALC_DOI) {
                $this->checkDoiForLibraryDoiSuffixInput($suffix, $resultCheckDoi);
                if(!$resultCheckDoi->isGrantDoi) { return $resultCheckDoi; }
            }
            // インポート・SCfWの時は、入力されたsuffixがDOI付与モードに合った入力値になっているかチェックする
            // 自動発番モードならsuffixは指定できず、自由入力モードならsuffixは指定が必須である
            // DOI変更モード時はどちらでもよい
            if($status == self::CHECKING_STATUS_IMPORT_SWORD) {
                $this->checkDoiForDoiSuffixInput($type, $suffix, $changeMode, $resultCheckDoi);
                if(!$resultCheckDoi->isGrantDoi) { return $resultCheckDoi; }
            }
            
            $this->checkDoiForDoiSuffix($item_id, $item_no, $type, $suffix, $changeMode, $resultCheckDoi);
            if(!$resultCheckDoi->isGrantDoi)
            {
                return $resultCheckDoi;
            }
        }
        
        switch($nii_type)
        {
            case RepositoryConst::NIITYPE_JOURNAL_ARTICLE:
                $this->checkDoiForJournalArticle($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_ARTICLE:
                $this->checkDoiForArticle($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_PREPRINT:
                $this->checkDoiForPreprint($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_DEPARTMENTAL_BULLETIN_PAPER:
                $this->checkDoiForDepartmentalBulletinPaper($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_THESIS_OR_DISSERTATION:
                $this->checkDoiForThesisOrDissertation($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_CONFERENCE_PAPER:
                $this->checkDoiForConferencePaper($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_BOOK:
                $this->checkDoiForBook($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_TECHNICAL_REPORT:
                $this->checkDoiForTechnicalReport($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_RESEARCH_PAPER:
                $this->checkDoiForResearchPaper($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_LEARNING_MATERIAL:
                $this->checkDoiForLearningMaterial($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_DATA_OR_DATASET:
                $this->checkDoiForDataOrDataset($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_SOFTWARE:
                $this->checkDoiForSoftware($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_PRESENTATION:
                $this->checkDoiForPresentation($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            case RepositoryConst::NIITYPE_OTHERS:
                $this->checkDoiForOthers($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
                break;
            
            default:
                $resultCheckDoi->isSetNiiType = false;
                $resultCheckDoi->isGrantDoi = false;
                break;
        }
        return $resultCheckDoi;
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * Check whether or not be able to grant DOI for WEKO setting
     * WEKO設定でDOIの発番可否をチェックする
     *
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForWekoSetting($status, $type, &$resultCheckDoi)
    {
        // IDサーバーPrefixIDチェック
        $this->checkDoiForYHandlePrefixSetting($resultCheckDoi);
        
        // DOI使用許可チェック
        $this->checkDoiForDoiAllow($type, $resultCheckDoi);
        
        // DOI Prefix設定
        $this->checkDoiForDoiPrefixSetting($type, $resultCheckDoi);
        
        // 査読・自動公開設定チェック
        if($status != self::CHECKING_STATUS_ITEM_MANAGEMENT)
        {
            $this->checkDoiForReviewAutoPublicSetting($resultCheckDoi);
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for Y handle prefix
     * YハンドルPrefix設定でDOIの発番可否をチェックする
     *
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForYHandlePrefixSetting(&$resultCheckDoi)
    {
        $repositoryHandleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
        
        $yHandlePrefix = $repositoryHandleManager->getYHandlePrefix();
        if(!isset($yHandlePrefix) || strlen($yHandlePrefix) == 0)
        {
            $resultCheckDoi->isSetYhandlePrefix = false;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            $resultCheckDoi->isSetYhandlePrefix = true;
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for DOI allow use setting
     * DOI使用許可設定でDOIの発番可否をチェックする
     *
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForDoiAllow($type, &$resultCheckDoi)
    {
        switch($type)
        {
            case self::TYPE_JALC_DOI:
            case self::TYPE_LIBRARY_JALC_DOI:
                if(!_REPOSITORY_JALC_DOI)
                {
                    $resultCheckDoi->isAllowDoi = false;
                    $resultCheckDoi->isGrantDoi = false;
                }
                else
                {
                    $resultCheckDoi->isAllowDoi = true;
                }
                break;
            case self::TYPE_CROSS_REF:
                if(!_REPOSITORY_JALC_CROSSREF_DOI)
                {
                    $resultCheckDoi->isAllowDoi = false;
                    $resultCheckDoi->isGrantDoi = false;
                }
                else
                {
                    $resultCheckDoi->isAllowDoi = true;
                }
                break;
            case self::TYPE_DATACITE:
                if(!_REPOSITORY_JALC_DATACITE_DOI)
                {
                    $resultCheckDoi->isAllowDoi = false;
                    $resultCheckDoi->isGrantDoi = false;
                }
                else
                {
                    $resultCheckDoi->isAllowDoi = true;
                }
                break;
            default:
                $resultCheckDoi->isAllowDoi = false;
                $resultCheckDoi->isGrantDoi = false;
                break;
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for DOI allow use setting
     * DOI Prefix設定でDOIの発番可否をチェックする
     *
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForDoiPrefixSetting($type, &$resultCheckDoi)
    {
        switch($type)
        {
            case self::TYPE_JALC_DOI:
                if(!$this->existJalcdoiPrefix())
                {
                    $resultCheckDoi->isSetDoiPrefix = false;
                    $resultCheckDoi->isGrantDoi = false;
                }
                else
                {
                    $resultCheckDoi->isSetDoiPrefix = true;
                }
                break;
            case self::TYPE_CROSS_REF:
                if(!$this->existCrossrefPrefix())
                {
                    $resultCheckDoi->isSetDoiPrefix = false;
                    $resultCheckDoi->isGrantDoi = false;
                }
                else
                {
                    $resultCheckDoi->isSetDoiPrefix = true;
                }
                break;
            case self::TYPE_DATACITE:
                if(!$this->existDatacitePrefix())
                {
                    $resultCheckDoi->isSetDoiPrefix = false;
                    $resultCheckDoi->isGrantDoi = false;
                }
                else
                {
                    $resultCheckDoi->isSetDoiPrefix = true;
                }
                break;
            case self::TYPE_LIBRARY_JALC_DOI:
                break;
            default:
                $resultCheckDoi->isSetDoiPrefix = false;
                $resultCheckDoi->isGrantDoi = false;
                break;
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for review and automatic public setting
     * 査読・自動公開設定でDOIの発番可否をチェックする
     *
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForReviewAutoPublicSetting(&$resultCheckDoi)
    {
        $query = "SELECT * ".
                 "FROM ". DATABASE_PREFIX ."repository_parameter ".
                "WHERE param_name = ? ".
                "AND is_delete = ?; ";
        $params = array();
        $params[0] = self::PARAMETER_REVIEW_FLG;
        $params[1] = 0;
        // execute SQL
        $result = $this->executeSql($query, $params);
        $review_flg = $result[0]["param_value"];
        
        if($review_flg == '1')
        {
            $resultCheckDoi->isNotReviewItem = false;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            $resultCheckDoi->isNotReviewItem = true;
        }
        
        $query = "SELECT * ".
                 "FROM ". DATABASE_PREFIX ."repository_parameter ".
                "WHERE param_name = ? ".
                "AND is_delete = ?; ";
        $params = array();
        $params[0] = self::PARAMETER_ITEM_AUTO_PUBLIC;
        $params[1] = 0;
        // execute SQL
        $result = $this->executeSql($query, $params);
        $item_auto_public = $result[0]["param_value"];
        
        if($item_auto_public == '0')
        {
            $resultCheckDoi->isPublicItem = false;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            $resultCheckDoi->isPublicItem = true;
        }
    }
    
    /**
     * Check whether or not be Nii type to be able to grant target DOI
     * 指定のDOIを発番できるNII資源タイプかチェックする
     *
     * @param string $nii_type Nii type NII資源タイプ
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForNiiTypeSetting($nii_type, $type, &$resultCheckDoi)
    {
        $exist_nii_type = $this->checksNiiType($nii_type);
        $result = $this->canRegistDoi($nii_type, $type);
        if(!$exist_nii_type || !$result)
        {
            $resultCheckDoi->isSetNiiType = false;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            $resultCheckDoi->isSetNiiType = true;
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for Index setting
     * インデックス設定でDOIの発番可否をチェックする
     *
     * @param int $item_id Item ID アイテムID
     * @param int $item_no Item No アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForIndexSetting($item_id, $item_no, &$resultCheckDoi)
    {
        // 所属インデックスを取得
        $result = $this->getPositionIndexId($item_id, $item_no);
        if(count($result) == 0)
        {
            $resultCheckDoi->isPublicIndex = false;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            // 所属インデックスの内少なくとも一つが、公開インデックスかつハーベスト公開ONであるか
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                $this->isHarvestPublicIndex($result[$cnt]['index_id'], $resultCheckDoi);
                if($resultCheckDoi->isGrantDoi)
                {
                    break;
                }
            }
            // DOI付与済みのアイテムに対して所属インデックスを非公開にするようにした場合、
            // 警告を出して登録できるようにするため、DOI付与可否のフラグをtrueにする
            $doiStatus = $this->getDoiStatus($item_id, $item_no);
            if($doiStatus == self::DOI_STATUS_GRANTED)
            {
                $resultCheckDoi->isGrantDoi = true;
            }
        }
    }

    /**
     * Check whether or not be able to grant DOI for DOI suffix value
     * DOI suffix値でDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param string $suffix DOI suffix value
     *                       DOI suffix値
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForDoiSuffix($item_id, $item_no, $type, $suffix, $changeMode, &$resultCheckDoi)
    {
        $repositoryHandleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
        
        // 半角数字8桁でないかチェック
        // 自動発番モード、国立国会図書館JaLC DOIの付与、DOI変更モード時はチェックしない
        if($changeMode != self::CHECKING_STATUS_SWORD_DOI_CHANGE 
           && $type != self::TYPE_LIBRARY_JALC_DOI
           && defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") && _REPOSITORY_WEKO_DOISUFFIX_FREE)
        {
            if($repositoryHandleManager->checkSuffixHalfSizeNumber8Digit($suffix))
            {
                $resultCheckDoi->isNotHalfSizeNumber8Digit = false;
                $resultCheckDoi->isGrantDoi = false;
            }
            else
            {
                $resultCheckDoi->isNotHalfSizeNumber8Digit = true;
            }
        }
        
        // 利用できる文字、文字数のチェックは自由入力モードまたはDOI変更モード時のみ
        if($changeMode == self::CHECKING_STATUS_SWORD_DOI_CHANGE || (defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") && _REPOSITORY_WEKO_DOISUFFIX_FREE))
        {
            // 利用できる文字のチェック
            $checkSuffix = $repositoryHandleManager->checkDoiFormat($suffix);
            if(strlen($checkSuffix) == 0)
            {
                $resultCheckDoi->isStringAvailable = false;
                $resultCheckDoi->isGrantDoi = false;
            }
            else
            {
                $resultCheckDoi->isStringAvailable = true;
            }
            
            // 文字数チェック
            $result = $repositoryHandleManager->checkSuffixAvailableDoiStringLength($type, $suffix);
            if(!$result)
            {
                $resultCheckDoi->isAvailableDoiStringLength = false;
                $resultCheckDoi->isGrantDoi = false;
            }
            else
            {
                $resultCheckDoi->isAvailableDoiStringLength = true;
            }
        }
        
        // 重複チェック
        $result = $repositoryHandleManager->checkSuffixExisted($item_id, $item_no, $type, $suffix);
        if(count($result) > 0)
        {
            // 重複したsuffixが取り下げ済みである
            if($result["status"] == self::DOI_STATUS_DROPED || $result["is_delete"] == 1)
            {
                $resultCheckDoi->isNotDoiSuffixDroped = false;
                $resultCheckDoi->isGrantDoi = false;
            }
            // 重複したsuffixが他アイテムで付与済みである
            else
            {
                $resultCheckDoi->isNotDoiSuffixUsed = false;
                $resultCheckDoi->isGrantDoi = false;
            }
        }
        else
        {
            $resultCheckDoi->isNotDoiSuffixDroped = true;
            $resultCheckDoi->isNotDoiSuffixUsed = true;
        }
    }

    /**
     * Check whether or not be able to grant DOI for Journal Article
     * Journal ArticleでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForJournalArticle($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        // アイテムタイプID取得
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        
        // JaLC DOI
        if($type == self::TYPE_JALC_DOI)
        {
            // JaLC DOI付与可能アイテムである条件を満たす
            $this->isJournalArticleJalcDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
        // Cross Ref
        else if($type == self::TYPE_CROSS_REF)
        {
            // Cross Ref DOI付与可能アイテムである条件を満たす
            $this->isJournalArticleCrossRefDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for Article
     * ArticleでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForArticle($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForJournalArticle($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Preprint
     * PreprintでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForPreprint($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForJournalArticle($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Departmental Bulletin Paper
     * Departmental Bulletin PaperでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForDepartmentalBulletinPaper($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForJournalArticle($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Thesis or Dissertation
     * Thesis or DissertationでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForThesisOrDissertation($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        // アイテムタイプID取得
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        
        // JaLC DOI
        if($type == self::TYPE_JALC_DOI)
        {
            // JaLC DOI付与可能アイテムである条件を満たす
            $this->isThesisOrDissertationJalcDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
        // 国立国会図書館JaLC DOI
        else if($type == self::TYPE_LIBRARY_JALC_DOI)
        {
            // 国立国会図書館JaLC DOI付与可能アイテムである条件を満たす
            $this->isThesisOrDissertationJalcDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for Conference Paper
     * Conference PaperでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForConferencePaper($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        // アイテムタイプID取得
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        
        // JaLC DOI
        if($type == self::TYPE_JALC_DOI)
        {
            // JaLC DOI付与可能アイテムである条件を満たす
            $this->isBookJalcDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
        // Cross Ref
        else if($type == self::TYPE_CROSS_REF)
        {
            // Cross Ref DOI付与可能アイテムである条件を満たす
            $this->isBookCrossRefDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for Book
     * BookでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForBook($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForConferencePaper($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Technical Report
     * Technical ReportでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForTechnicalReport($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForConferencePaper($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Research Paper
     * Research PaperでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForResearchPaper($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForConferencePaper($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Learning Material
     * Learning MaterialでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForLearningMaterial($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        // アイテムタイプID取得
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        
        // JaLC DOI
        if($type == self::TYPE_JALC_DOI)
        {
            // JaLC DOI付与可能アイテムである条件を満たす
            $this->isELearningJalcDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Check whether or not be able to grant DOI for Data or Dataset
     * Data or DatasetでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForDataOrDataset($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        // アイテムタイプID取得
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        
        // JaLC DOI
        if($type == self::TYPE_JALC_DOI)
        {
            // JaLC DOI付与可能アイテムである条件を満たす
            $this->isResearchDataJalcDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
        // DataCite
        else if($type == self::TYPE_DATACITE)
        {
            // DataCite DOI付与可能アイテムである条件を満たす
            $this->isResearchDataDataCiteDoiJunii2Required($item_id, $item_no, $resultCheckDoi);
            $this->checkPublicItem($item_id, $item_no, $status, $resultCheckDoi);
            $this->isNotEnteredDoi($item_id, $item_no, $status, $changeMode, $resultCheckDoi);
        }
    }
    
    /**
     * Check whether or not be able to grant DOI for Software
     * SoftwareでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForSoftware($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForDataOrDataset($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * Check whether or not be able to grant DOI for Presentation
     * PresentationでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForPresentation($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForLearningMaterial($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Check whether or not be able to grant DOI for Others
     * OthersでDOIの発番可否をチェックする
     *
     * @param int $item_id Item id
     *                     アイテムID
     * @param int $item_no Item number
     *                     アイテム通番
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForOthers($item_id, $item_no, $type, $status, $changeMode, &$resultCheckDoi)
    {
        $this->checkDoiForLearningMaterial($item_id, $item_no, $type, $status, $changeMode, $resultCheckDoi);
    }
    
    /**
     * Get Nii type of item
     * アイテムの資源タイプを取得する
     *
     * @param int $item_type_id Item type id
     *                          アイテムタイプID
     * @return string Nii type
     *                資源タイプ
     */
    public function getNiiType($item_type_id)
    {
        $query = "SELECT mapping_info ".
                 "FROM ".DATABASE_PREFIX."repository_item_type ".
                 "WHERE item_type_id = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_type_id;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        $nii_type = $result[0]['mapping_info'];
        
        return $nii_type;
    }
    
    /**
     * Get item type id
     * アイテムのitem_type_idを取得する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return int Item type id
     *             アイテムタイプID
     */
    public function getItemTypeId($item_id, $item_no)
    {
        $query = "SELECT item_type_id ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        $item_type_id = $result[0]['item_type_id'];
        
        return $item_type_id;
    }
    
    /**
     * Whether or not JaLC JuNii2 metadata items required to grant the journal article has been entered
     * ジャーナルアーティクルのJaLC付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isJournalArticleJalcDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, false, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
        // <spage>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_SPAGE, false, false, $resultCheckDoi);
    }
    
    /**
     * Whether or not CrossRef JuNii2 metadata items required to grant the journal article has been entered
     * ジャーナルアーティクルのCrossRef付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isJournalArticleCrossRefDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, true, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
        // <publisher>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_PUBLISHER, true, true, $resultCheckDoi);
        // <jtitle>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_JTITLE, true, true, $resultCheckDoi);
        // <ISSN>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_ISSN, true, false, $resultCheckDoi);
        // <spage>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_SPAGE, false, false, $resultCheckDoi);
        // <language>に値が入力されているか
        $this->existJunii2LanguageMetadata($item_id, $item_no, $item_type_id, true, $resultCheckDoi);
    }
    
    /**
     * Whether or not JaLC JuNii2 metadata items required to grant the thesis or dissertation has been entered
     * 学位論文のJaLC付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isThesisOrDissertationJalcDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, false, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
    }
    
    /**
     * Whether or not JaLC JuNii2 metadata items required to grant the book has been entered
     * 書籍のJaLC付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isBookJalcDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, false, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
    }
    
    /**
     * Whether or not CrossRef JuNii2 meta data items required for grant the book has been entered
     * 書籍のCrossRef付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isBookCrossRefDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, true, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
        // <publisher>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_PUBLISHER, true, true, $resultCheckDoi);
        // <ISBN>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_ISBN, true, false, $resultCheckDoi);
        // <language>に値が入力されているか
        $this->existJunii2LanguageMetadata($item_id, $item_no, $item_type_id, true, $resultCheckDoi);
        // JuNii2メタデータ項目の<fullTextURL>, <publisher>, <ISBN>, <language>が入力されている
    }
    
    /**
     * Whether or not JaLC JuNii2 metadata items required to grant the e-learning has been entered
     * e-learningのJaLC付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isELearningJalcDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, false, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
    }
    
    /**
     * Whether or not JaLC JuNii2 metadata items required to grant the research data has been entered
     * 研究データのJaLC付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isResearchDataJalcDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, false, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
        // <creator>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_CREATOR, false, false, $resultCheckDoi);
        // <publisher>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_PUBLISHER, true, false, $resultCheckDoi);
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Whether or not DataCite JuNii2 metadata items required to grant the research data has been entered
     * 研究データのDataCite付与に必要なJuNii2メタデータ項目が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isResearchDataDataCiteDoiJunii2Required($item_id, $item_no, &$resultCheckDoi)
    {
        $item_type_id = $this->getItemTypeId($item_id, $item_no);
        // <title>に値が入力されているか
        $this->existJunii2TitleMetadata($item_id, $item_no, true, $resultCheckDoi);
        // <fullTextURL>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_FULL_TEXT_URL, false, false, $resultCheckDoi);
        // <creator>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_CREATOR, false, true, $resultCheckDoi);
        // <publisher>に値が入力されているか
        $this->existJunii2MappingMetadata($item_id, $item_no, $item_type_id, self::JUNII2_MAPPING_PUBLISHER, true, true, $resultCheckDoi);
        // <language>に値が入力されているか
        $this->existJunii2LanguageMetadata($item_id, $item_no, $item_type_id, true, $resultCheckDoi);
        return $resultCheckDoi;
    }
     // Add DataCite 2015/02/10 K.Sugimoto --end--

    /**
     * To get the attribute ID of the specified JuNii2 mapping
     * 指定したJuNii2マッピングの属性IDを取得する
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param string $mapping mapping マッピング
     * @return array Attribute id list 属性ID一覧
     *               array[$ii]["attribute_id"|"display_lang_type"]
     */
    private function getAttributeIdFromJuNii2Mapping($item_type_id, $mapping)
    {
        // アイテムタイプ中の指定したマッピングの属性IDを取得する
        $query = "SELECT attribute_id, display_lang_type ".
                 "FROM ".DATABASE_PREFIX."repository_item_attr_type ".
                 "WHERE item_type_id = ? ".
                 "AND junii2_mapping = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_type_id;
        $params[] = $mapping;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        return $result;
    }
    
    /**
     * junii2 or metadata item of title has been input
     * junii2メタデータ項目のタイトルが入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param boolean $check_lang Language is english flag 言語が英語であるかチェックするフラグ
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function existJunii2TitleMetadata($item_id, $item_no, $check_lang, &$resultCheckDoi)
    {
        $exist_data = false;
        // 指定した属性IDの属性値を取得する
        $query = "SELECT title, title_english, language ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        
        // タイトル、またはタイトル(英)に値がある
        if(count($result) != 0 &&
           (strlen($result[0]['title']) !== 0 || strlen($result[0]['title_english']) !== 0))
        {
            $exist_data = true;
        }
        
        // 言語判定をする場合、言語がenである時true
        if($check_lang && $result[0]['language'] !== "en")
        {
            $exist_data = false;
        }
        
        // タイトルのメタデータが存在しない場合、DOI付与不可
        if(!$exist_data)
        {
            $resultCheckDoi->isSetMetadata = false;
            $resultCheckDoi->LackMetadata[] = self::JUNII2_MAPPING_TITLE;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            if(!isset($resultCheckDoi->isSetMetadata))
            {
                $resultCheckDoi->isSetMetadata = true;
            }
        }
    }
    
    /**
     * junii2 or metadata item of language to the value "eng" has been input
     * junii2メタデータ項目の言語に値"eng"が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $item_type_id Item type id アイテムタイプID
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function existJunii2LanguageMetadata($item_id, $item_no, $item_type_id, $check_num, &$resultCheckDoi)
    {
        $num = 0;
        $exist_data = false;
        
        // 言語メタデータの値を取得
        $query = "SELECT language ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        
        // 言語がenである時true
        if(count($result) != 0)
        {
        	if($result[0]['language'] === "en")
        	{
            	$exist_data = true;
        	}
        	$num++;
        }
        
        // アイテムタイプ中の指定したマッピングの属性IDを取得する
        $attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id, self::JUNII2_MAPPING_LANGUAGE);
        
        // アイテムタイプ中に指定したマッピングのデータがある
        if(count($attr_id_array) > 0)
        {
            // 指定した属性IDの属性値を取得する
            $query = "SELECT attribute_value ".
                     "FROM ".DATABASE_PREFIX."repository_item_attr ".
                     "WHERE item_id = ? ".
                     "AND item_no = ? ".
                     "AND attribute_id IN (";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            for($cnt = 0; $cnt < count($attr_id_array); $cnt++)
            {
                if($cnt > 0)
                {
                    $query .= ", ";
                }
                $query .= "?";
                $params[] = $attr_id_array[$cnt]['attribute_id'];
            }
            $query .= ") ".
                      "AND is_delete = ? ;";
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            // 指定した属性IDのデータがある
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                // 属性値に値がある
                if(isset($result[$cnt]['attribute_value']) &&
                   strlen($result[$cnt]['attribute_value']) > 0)
                {
                    if($result[$cnt]['attribute_value'] === "eng")
                    {
                        $exist_data = true;
                    }
                    $num++;
                }
            }
        }
        
        // 登録数を判定する場合、1の時のみtrue
        if($check_num && $num != 1)
        {
            $exist_data = false;
        }
        
        // 言語のメタデータが存在しない場合、DOI付与不可
        if(!$exist_data)
        {
            $resultCheckDoi->isSetMetadata = false;
            $resultCheckDoi->LackMetadata[] = self::JUNII2_MAPPING_LANGUAGE;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            if(!isset($resultCheckDoi->isSetMetadata))
            {
                $resultCheckDoi->isSetMetadata = true;
            }
        }
    }
    
    /**
     * junii2 or mapping metadata item value of the fourth parameter is input
     * junii2メタデータ項目のマッピングが第4引数である値が入力されているか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $item_type_id Item type id アイテムタイプID
     * @param string $mapping mapping マッピング
     * @param boolean $check_num Number is one flag メタデータ数が1であるかをチェックするフラグ
     * @param boolean $check_lang Language is english flag 英語であるかをチェックするフラグ
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function existJunii2MappingMetadata($item_id, $item_no, $item_type_id, $mapping, $check_num, $check_lang, &$resultCheckDoi)
    {
        $num = 0;
        $exist_data = false;
        // アイテムタイプ中の書誌情報の属性IDを取得する
        $attr_id_biblio_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
            self::JUNII2_MAPPING_BIBLIO);
        // アイテムタイプ中の指定したマッピングの属性IDを取得する
        $attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id, $mapping);
        // 指定のマッピングが存在するかチェック
        $this->checkExistTargetMappingIncludingBiblioInfo($mapping, $attr_id_array, $attr_id_biblio_array, $check_lang, $resultCheckDoi);
        if(!$resultCheckDoi->isGrantDoi)
        {
            return;
        }
        
        // アイテムタイプ中に指定したマッピングのデータがある
        if(count($attr_id_array) > 0)
        {
            // 指定した属性IDの属性値を取得する
            $query = "SELECT attribute_value, attribute_id ".
                     "FROM ".DATABASE_PREFIX."repository_item_attr ".
                     "WHERE item_id = ? ".
                     "AND item_no = ? ".
                     "AND attribute_id IN (";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            for($cnt = 0; $cnt < count($attr_id_array); $cnt++)
            {
                if($cnt > 0)
                {
                    $query .= ", ";
                }
                $query .= "?";
                $params[] = $attr_id_array[$cnt]['attribute_id'];
            }
            $query .= ") ".
                      "AND is_delete = ? ;";
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            // 指定した属性IDのデータがある
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                // 属性値に値がある
                if(isset($result[$cnt]['attribute_value']) &&
                   strlen($result[$cnt]['attribute_value']) > 0 &&
                   $result[$cnt]['attribute_value'] !== "|||" &&
                   $result[$cnt]['attribute_value'] !== "&EMPTY&")
                {
                    for($cnt_attr_id_array = 0; $cnt_attr_id_array < count($attr_id_array); $cnt_attr_id_array++)
                    {
                        if($attr_id_array[$cnt_attr_id_array]['attribute_id'] == $result[$cnt]['attribute_id'])
                        {
                            if(!$check_lang || $attr_id_array[$cnt_attr_id_array]['display_lang_type'] === "english")
                            {
                                $exist_data = true;
                            }
                        }
                    }
                    $num++;
                }
            }
            
            // 指定した属性IDの氏名情報を取得する
            $query = "SELECT family, name, attribute_id ".
                     "FROM ".DATABASE_PREFIX."repository_personal_name ".
                     "WHERE item_id = ? ".
                     "AND item_no = ? ".
                     "AND attribute_id IN (";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            for($cnt = 0; $cnt < count($attr_id_array); $cnt++)
            {
                if($cnt > 0)
                {
                    $query .= ", ";
                }
                $query .= "?";
                $params[] = $attr_id_array[$cnt]['attribute_id'];
            }
            $query .= ") ".
                      "AND is_delete = ? ;";
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            // 指定した属性IDのデータがある
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                // 属性値に値がある
                if((isset($result[$cnt]['family']) &&
                   strlen($result[$cnt]['family']) > 0) ||
                   (isset($result[$cnt]['name']) &&
                   strlen($result[$cnt]['name']) > 0))
                {
                    for($cnt_attr_id_array = 0; $cnt_attr_id_array < count($attr_id_array); $cnt_attr_id_array++)
                    {
                        if($attr_id_array[$cnt_attr_id_array]['attribute_id'] == $result[$cnt]['attribute_id'])
                        {
                            if(!$check_lang || $attr_id_array[$cnt_attr_id_array]['display_lang_type'] === "english")
                            {
                                $exist_data = true;
                            }
                        }
                    }
                    $num++;
                }
            }
            
            // 指定した属性IDのファイル情報を取得する
            $query = "SELECT file_no ".
                     "FROM ".DATABASE_PREFIX."repository_file ".
                     "WHERE item_id = ? ".
                     "AND item_no = ? ".
                     "AND attribute_id IN (";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            for($cnt = 0; $cnt < count($attr_id_array); $cnt++)
            {
                if($cnt > 0)
                {
                    $query .= ", ";
                }
                $query .= "?";
                $params[] = $attr_id_array[$cnt]['attribute_id'];
            }
            $query .= ") ".
                      "AND is_delete = ? ;";
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            // 指定した属性IDのデータがある
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                if(isset($result[$cnt]['file_no']) && strlen($result[$cnt]['file_no']) > 0)
                {
                    if(!$check_lang)
                    {
                        $exist_data = true;
                    }
                    $num++;
                }
            }
            
             // 指定した属性IDのサムネイル情報を取得する
            $query = "SELECT file_no ".
                     "FROM ".DATABASE_PREFIX."repository_thumbnail ".
                     "WHERE item_id = ? ".
                     "AND item_no = ? ".
                     "AND attribute_id IN (";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            for($cnt = 0; $cnt < count($attr_id_array); $cnt++)
            {
                if($cnt > 0)
                {
                    $query .= ", ";
                }
                $query .= "?";
                $params[] = $attr_id_array[$cnt]['attribute_id'];
            }
            $query .= ") ".
                      "AND is_delete = ? ;";
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            // 指定した属性IDのデータがある
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                if(isset($result[$cnt]['file_no']) && strlen($result[$cnt]['file_no']) > 0)
                {
                    if(!$check_lang)
                    {
                        $exist_data = true;
                    }
                    $num++;
                }
            }
            
       }
       
        
        // アイテムタイプ中に書誌情報のデータがある
        if(count($attr_id_biblio_array) > 0)
        {
            // 指定した属性IDの書誌情報を取得する
            $query = "SELECT biblio_name, biblio_name_english, volume, issue, start_page, end_page, date_of_issued ".
                     "FROM ".DATABASE_PREFIX."repository_biblio_info ".
                     "WHERE item_id = ? ".
                     "AND item_no = ? ".
                     "AND attribute_id IN (";
            $params = array();
            $params[] = $item_id;
            $params[] = $item_no;
            for($cnt = 0; $cnt < count($attr_id_biblio_array); $cnt++)
            {
                if($cnt > 0)
                {
                    $query .= ", ";
                }
                $query .= "?";
                $params[] = $attr_id_biblio_array[$cnt]['attribute_id'];
            }
            $query .= ") ".
                      "AND is_delete = ? ;";
            $params[] = 0;
            $result = $this->executeSql($query, $params);
            // 指定した属性IDのデータがある
            for($cnt = 0; $cnt < count($result); $cnt++)
            {
                switch($mapping)
                {
                    case self::JUNII2_MAPPING_JTITLE:
                        // 属性値に値がある
                        if(isset($result[$cnt]['biblio_name']) &&
                           strlen($result[$cnt]['biblio_name']) > 0)
                        {
                            if(!$check_lang)
                            {
                                $exist_data = true;
                            }
                            $num++;
                        }
                        if(isset($result[$cnt]['biblio_name_english']) &&
                           strlen($result[$cnt]['biblio_name_english']) > 0)
                        {
                            $exist_data = true;
                            $num++;
                        }
                        break;
                    case self::JUNII2_MAPPING_VOLUME:
                        // 属性値に値がある
                        if((isset($result[$cnt]['volume']) &&
                           strlen($result[$cnt]['volume']) > 0))
                        {
                            $exist_data = true;
                            $num++;
                        }
                        break;
                    case self::JUNII2_MAPPING_ISSUE:
                        // 属性値に値がある
                        if((isset($result[$cnt]['issue']) &&
                           strlen($result[$cnt]['issue']) > 0))
                        {
                            $exist_data = true;
                            $num++;
                        }
                        break;
                    case self::JUNII2_MAPPING_SPAGE:
                        // 属性値に値がある
                        if((isset($result[$cnt]['start_page']) &&
                           strlen($result[$cnt]['start_page']) > 0))
                        {
                            $exist_data = true;
                            $num++;
                        }
                        break;
                    case self::JUNII2_MAPPING_EPAGE:
                        // 属性値に値がある
                        if((isset($result[$cnt]['end_page']) &&
                           strlen($result[$cnt]['end_page']) > 0))
                        {
                            $exist_data = true;
                            $num++;
                        }
                        break;
                    case self::JUNII2_MAPPING_DATE_OF_ISSUED:
                        // 属性値に値がある
                        if((isset($result[$cnt]['date_of_issued']) &&
                           strlen($result[$cnt]['date_of_issued']) > 0))
                        {
                            $exist_data = true;
                            $num++;
                        }
                        break;
                }
            }
       }
       
       // 登録数を調べる場合、登録数が1の時のみtrue
       if($check_num && $num != 1)
       {
            $exist_data = false;
       }
       
       // 指定されたマッピングにメタデータが過不足なく存在するかチェック
       if(!$exist_data)
       {
            $resultCheckDoi->isSetMetadata = false;
            $resultCheckDoi->LackMetadata[] = $mapping;
            $resultCheckDoi->isGrantDoi = false;
       }
        else
        {
            if(!isset($resultCheckDoi->isSetMetadata))
            {
                $resultCheckDoi->isSetMetadata = true;
            }
        }
    }
    
    /**
     * Check exist target mapping including biblio info
     * 指定されたマッピングが存在するか書誌情報を含めてチェックする
     *
     * @param string $mapping Mapping マッピング
     * @param array $attr_id_array Set of attribute having target mapping 指定されたマッピングを持つ属性一覧
     *                             array[$ii]["attribute_id"|"display_lang_type"]
     * @param array $attr_id_biblio_array Set of attribute having biblio info mapping 書誌情報マッピングを持つ属性一覧
     *                             array[$ii]["attribute_id"|"display_lang_type"]
     * @param boolean $check_lang Language is english flag 英語であるかをチェックするフラグ
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkExistTargetMappingIncludingBiblioInfo($mapping, $attr_id_array, $attr_id_biblio_array, $check_lang, &$resultCheckDoi)
    {
        switch($mapping)
        {
            case self::JUNII2_MAPPING_JTITLE:
            case self::JUNII2_MAPPING_VOLUME:
            case self::JUNII2_MAPPING_ISSUE:
            case self::JUNII2_MAPPING_SPAGE:
            case self::JUNII2_MAPPING_EPAGE:
            case self::JUNII2_MAPPING_DATE_OF_ISSUED:
                if(count($attr_id_biblio_array) == 0)
                {
                    $this->checkExistTargetMapping($mapping, $attr_id_array, $check_lang, $resultCheckDoi);
                }
                else
                {
                    if(!isset($resultCheckDoi->isSetItemTypeMapping))
                    {
                        $resultCheckDoi->isSetItemTypeMapping = true;
                    }
                }
                break;
            default:
                $this->checkExistTargetMapping($mapping, $attr_id_array, $check_lang, $resultCheckDoi);
                break;
        }
    }
    
    /**
     * Check exist target mapping
     * 指定されたマッピングが存在するかチェックする
     *
     * @param string $mapping Mapping マッピング
     * @param array $attr_id_array Set of attribute having target mapping 指定されたマッピングを持つ属性一覧
     *                             array[$ii]["attribute_id"|"display_lang_type"]
     * @param boolean $check_lang Language is english flag 英語であるかをチェックするフラグ
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkExistTargetMapping($mapping, $attr_id_array, $check_lang, &$resultCheckDoi)
    {
        // マッピングの存在チェック
        if(count($attr_id_array) == 0)
        {
            $resultCheckDoi->isSetItemTypeMapping = false;
            $resultCheckDoi->LackItemTypeMapping[] = $mapping;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            // マッピングの言語チェック
            if($check_lang)
            {
                $isFindEnglishMapping = false;
                for($cnt = 0; $cnt < count($attr_id_array); $cnt++)
                {
                    // 1つでも英語が見つかったらtrue
                    if($attr_id_array[$cnt]["display_lang_type"] == RepositoryConst::ITEM_ATTR_TYPE_LANG_EN)
                    {
                        $isFindEnglishMapping = true;
                        if(!isset($resultCheckDoi->isSetItemTypeMapping))
                        {
                            $resultCheckDoi->isSetItemTypeMapping = true;
                        }
                        break;
                    }
                }
                if(!$isFindEnglishMapping)
                {
                    $resultCheckDoi->LackItemTypeMapping[] = $mapping;
                    $resultCheckDoi->isGrantDoi = false;
                }
            }
            else
            {
                if(!isset($resultCheckDoi->isSetItemTypeMapping))
                {
                    $resultCheckDoi->isSetItemTypeMapping = true;
                }
            }
        }
    }
    
    /**
     * Whether or not pubic item
     * 公開アイテムであるか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkPublicItem($item_id, $item_no, $status, &$resultCheckDoi)
    {
        // 公開アイテムでありかつ削除されていない
        $is_public_item = false;
        if($status == self::CHECKING_STATUS_ITEM_MANAGEMENT)
        {
            // DOI一括付与
            $is_public_item = $this->isPublicItemForManagement($item_id, $item_no);
        }
        else
        {
            // 登録・編集(画面・SWORD共)
            $is_public_item = $this->isPublicItemForRegistration($item_id, $item_no);
        }
        
        if(!$is_public_item)
        {
            $resultCheckDoi->isPublicItem = false;
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            $resultCheckDoi->isPublicItem = true;
        }
    }
    
    /**
     * A public item, and either not deleted
     * 公開アイテムである、かつ削除されていないか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    private function isPublicItemForManagement($item_id, $item_no)
    {
        $query = "SELECT item_id, title, title_english ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND review_status = ? ".
                 "AND shown_status = ? ".
                 "AND reject_status <= ? ".
                 "AND uri = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 1;
        $params[] = 1;
        $params[] = 0;
        $params[] = BASE_URL."/?action=repository_uri&item_id=".$item_id;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        if(count($result) > 0 && (strlen($result[0]['title']) > 0 || strlen($result[0]['title_english']) > 0) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * A public item, and either not deleted
     * 公開アイテムである、かつ削除されていないか
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    private function isPublicItemForRegistration($item_id, $item_no)
    {
        $is_public_item = false;
        $query = "SELECT item_id, title, title_english ".
                 "FROM ".DATABASE_PREFIX."repository_item ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        
        if(count($result) === 1 && (strlen($result[0]['title']) > 0 || strlen($result[0]['title_english']) > 0) )
        {
            $is_public_item = true;
        }
        return $is_public_item;
    }
    
    /**
     * Examine whether the index is a public index and harvest the public if
     * 公開インデックスであるかつハーベスト公開であるインデックスであるかどうか調べる
     *
     * @param int $index_id Index id インデックスID
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    public function isHarvestPublicIndex($index_id, &$resultCheckDoi)
    {
        $indexManager = BusinessFactory::getFactory()->getBusiness("businessIndexmanager");
        
        // インデックス公開判定
        $indexState = $indexManager->checkIndexStateRecursive($index_id);
        if($indexState == "private")
        {
            $resultCheckDoi->isPublicIndex = false;
        }
        else
        {
            $resultCheckDoi->isPublicIndex = true;
        }
        
        // ハーベスト公開判定
        $indexHarvestState = $indexManager-> checkIndexHarvestStateRecursive($index_id);
        if($indexHarvestState == "private")
        {
            $resultCheckDoi->isPublicHarvestIndex = false;
        }
        else
        {
            $resultCheckDoi->isPublicHarvestIndex = true;
        }
        
        if($indexState == "private" || $indexHarvestState == "private")
        {
            $resultCheckDoi->isGrantDoi = false;
        }
        else
        {
            $resultCheckDoi->isGrantDoi = true;
        }
    }
    
    /**
     * To check whether the correct value to NIItype has been input
     * NIItypeに正しい値が入力されているかをチェックする
     *
     * @param string $nii_type Nii type NII資源タイプ
     * @return boolean Result 結果
     */
    private function checksNiiType($nii_type)
    {
        // NII typeの値をチェック
        if($nii_type === RepositoryConst::NIITYPE_JOURNAL_ARTICLE ||
           $nii_type === RepositoryConst::NIITYPE_THESIS_OR_DISSERTATION ||
           $nii_type === RepositoryConst::NIITYPE_DEPARTMENTAL_BULLETIN_PAPER ||
           $nii_type === RepositoryConst::NIITYPE_CONFERENCE_PAPER ||
           $nii_type === RepositoryConst::NIITYPE_PRESENTATION ||
           $nii_type === RepositoryConst::NIITYPE_BOOK ||
           $nii_type === RepositoryConst::NIITYPE_TECHNICAL_REPORT ||
           $nii_type === RepositoryConst::NIITYPE_RESEARCH_PAPER ||
           $nii_type === RepositoryConst::NIITYPE_ARTICLE ||
           $nii_type === RepositoryConst::NIITYPE_PREPRINT ||
           $nii_type === RepositoryConst::NIITYPE_LEARNING_MATERIAL ||
           $nii_type === RepositoryConst::NIITYPE_DATA_OR_DATASET ||
           $nii_type === RepositoryConst::NIITYPE_SOFTWARE ||
           $nii_type === RepositoryConst::NIITYPE_OTHERS)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Is not entered DOI (is not droped DOI case registration from SWORD)
     * DOIが登録されていない(未設定)か (SWORDからの登録の場合、DOIが取り下げでないか)
     *
     * @param int $item_id ItemID
     *                     アイテムID
     * @param int $item_no ItemNo
     *                     アイテム通番
     * @param int $status Status of DOI regist(0:regist/edit from screen, 1:DOI lump-sum, 2:regist/edit from import and SWORD)
     *                    DOI登録の状態(0:画面からの登録/編集, 1:DOI一括付与, 2:インポート・SWORDからの登録/編集)
     * @param int $changeMode Mode of regist(0:normal mode, 1:DOI change mode)
     *                         登録のモード(0:通常モード, 1:DOI変更モード)
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function isNotEnteredDoi($item_id, $item_no, $status, $changeMode, &$resultCheckDoi)
    {
        $doiStatus = $this->getDoiStatus($item_id, $item_no);
        if($changeMode == self::CHECKING_STATUS_SWORD_DOI_CHANGE)
        {
            // DOI変更モード
            if($doiStatus > 1)
            {
                // 取り下げ済みの場合DOI登録不可
                $resultCheckDoi->isNotAlreadyGrantDoi = false;
                $resultCheckDoi->isGrantDoi = false;
            }
            else
            {
                $resultCheckDoi->isNotAlreadyGrantDoi = true;
            }
        }
        else if($status == self::CHECKING_STATUS_ITEM_MANAGEMENT || $status == self::CHECKING_STATUS_IMPORT_SWORD)
        {
            // 通常モード
            if($doiStatus > 0)
            {
                // 付与済みまたは取り下げ済みの場合DOI登録不可
                $resultCheckDoi->isNotAlreadyGrantDoi = false;
                $resultCheckDoi->isGrantDoi = false;
            }
            else
            {
                $resultCheckDoi->isNotAlreadyGrantDoi = true;
            }
        }
    }
    
    /**
     * Check library DOI format
     * 図書館DOIのフォーマットをチェックする
     *
     * @param string $suffix DOI suffix value
     *                       DOI suffix値
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForLibraryDoiSuffixInput($suffix, &$resultCheckDoi) {
        // またはsuffix部分が未入力の場合エラー
        if(strlen($suffix) == 0) {
            $resultCheckDoi->isCorrectLibraryDoiFormat = false;
            $resultCheckDoi->isGrantDoi = false;
        } else {
            $resultCheckDoi->isCorrectLibraryDoiFormat = true;
        }
    }
    
    /**
     * It is in the input state of suits DOI suffix to the mode
     * モードに合ったDOI suffixの入力状態になっているか
     *
     * @param int $type DOI type (0:JaLC, 1:CrossRef, 2:National Diet Library JaLC, 3:DataCite)
     *                  DOI種別(0:JaLC, 1:CrossRef, 2:国会図書館JaLC, 3:DataCite)
     * @param string $suffix DOI suffix value
     *                       DOI suffix値
     * @param int $changeMode DOI change mode
     *                        DOI変更モードフラグ
     * @param Object $resultCheckDoi Structure of result of check DOI grant
     *                               DOI付与チェック結果の構造体
     */
    private function checkDoiForDoiSuffixInput($type, $suffix, $changeMode, &$resultCheckDoi) {
        // DOI変更モード、図書館DOIなら何もしない
        if($changeMode == self::CHECKING_STATUS_SWORD_DOI_CHANGE || $type == self::TYPE_LIBRARY_JALC_DOI) { return; }
        
        // prefixを取得
        $handleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
        $prefix = "";
        $extractedSuffix = "";
        if($type == self::TYPE_JALC_DOI) { $prefix = $handleManager->getJalcDoiPrefix(); }
        elseif($type == self::TYPE_CROSS_REF) { $prefix = $handleManager->getCrossRefPrefix(); }
        elseif($type == self::TYPE_DATACITE) { $prefix = $handleManager->getDataCitePrefix(); }
        // suffixを抽出
        if(preg_match("/^". preg_quote(Repository_Components_Business_Handle_Handlemanager::PREFIX_SELF_DOI. $prefix, "/"). "\/(.+)$/", $suffix, $matches) === 1
            || preg_match("/^". preg_quote(Repository_Components_Business_Handle_Handlemanager::PREFIX_LIBRARY_DOI_HTTP_DX. $prefix, "/"). "\/(.+)$/", $suffix, $matches) === 1
            || preg_match("/^". preg_quote(Repository_Components_Business_Handle_Handlemanager::PREFIX_LIBRARY_DOI_HTTP. $prefix, "/"). "\/(.+)$/", $suffix, $matches) === 1
            || preg_match("/^". preg_quote(Repository_Components_Business_Handle_Handlemanager::PREFIX_LIBRARY_DOI_DOI. $prefix, "/"). "\/(.+)$/", $suffix, $matches) === 1
            || preg_match("/^". preg_quote($prefix, "/"). "\/(.+)$/", $suffix, $matches) === 1)
        { $extractedSuffix = $matches[1]; }
        
        if(defined("_REPOSITORY_WEKO_DOISUFFIX_FREE") && _REPOSITORY_WEKO_DOISUFFIX_FREE) {
            // 自由入力モードでのインポート時にsuffixが未入力の場合エラーとする
            if(strlen($suffix) == 0) {
                // suffixが未入力なら×
                $resultCheckDoi->isSpecifyDoiInFree = false;
                $resultCheckDoi->isGrantDoi = false;
            } elseif(strlen($extractedSuffix) == 0 && $suffix == $prefix."/") {
                // suffixが「[prefix]/」の形なら×
                $resultCheckDoi->isSpecifyDoiInFree = false;
                $resultCheckDoi->isGrantDoi = false;
            } else {
                $resultCheckDoi->isSpecifyDoiInFree = true;
            }
        } else {
            // 自動発番モードでのインポート時にsuffixが入力済の場合エラーとする
            if(strlen($suffix) == 0) {
                // suffixが未入力ならOK
                $resultCheckDoi->isNotSpecifyDoiInAuto = true;
            } elseif(strlen($extractedSuffix) == 0 && $suffix == $prefix."/") {
                // suffixが「[prefix]/」の形ならOK
                $resultCheckDoi->isNotSpecifyDoiInAuto = true;
            } else {
                // 他の形式は×
                $resultCheckDoi->isNotSpecifyDoiInAuto = false;
                $resultCheckDoi->isGrantDoi = false;
            }
        }
    }
    
    /**
     * Get doi status
     * JaLC DOI付与状態取得
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return string DOI status DOI付与状態
     */
    public function getDoiStatus($item_id, $item_no)
    {
        $query = "SELECT status ".
                 "FROM ".DATABASE_PREFIX."repository_doi_status ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        $doi_status = 0;
        if(count($result) === 0 || (isset($result[0]) && $result[0]['status'] == 0))
        {
            $doi_status = 0;
        }
        else
        {
            $doi_status = $result[0]['status'];
        }
        return $doi_status;
    }
    
    /**
     * Get the index ID of the index that belongs
     * 所属するインデックスのインデックスIDを取得する
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return array Index id list インデックスID一覧
     *               array[$ii]["index_id"]
     */
    private function getPositionIndexId($item_id, $item_no)
    {
        // 所属インデックスを取得
        $query = "SELECT index_id ".
                 "FROM ".DATABASE_PREFIX."repository_position_index ".
                 "WHERE item_id = ? ".
                 "AND item_no = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $item_id;
        $params[] = $item_no;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        if(count($result) < 1)
        {
            $result = array();
        }
        return $result;
    }
    
    /**
     * Prefix of JaLC DOI to check if they are registered
     * JaLC DOIのプレフィックスが登録されているかをチェックする
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function existJalcdoiPrefix()
    {
        $repositoryHandleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
        $prefix = $repositoryHandleManager->getJalcDoiPrefix();
        if(isset($prefix) && strlen($prefix) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Prefix of CrossRef DOI to check if they are registered
     * Cross Refのプレフィックスが登録されているかをチェックする
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function existCrossrefPrefix()
    {
        $repositoryHandleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
        $prefix = $repositoryHandleManager->getCrossRefPrefix();
        if(isset($prefix) && strlen($prefix) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Prefix of DataCite DOI to check if they are registered
     * DataCiteのプレフィックスが登録されているかをチェックする
     *
     * @param int $item_id Item id アイテムID
     * @param int $item_no Item serial number アイテム通番
     * @return boolean Result 結果
     */
    public function existDatacitePrefix()
    {
        $repositoryHandleManager = BusinessFactory::getFactory()->getBusiness("businessHandlemanager");
        $prefix = $repositoryHandleManager->getDataCitePrefix();
        if(isset($prefix) && strlen($prefix) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Check whether the item type which can be a DOI can grant
     * DOI付与可能になり得るアイテムタイプであるかを調べる
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Can grant 付与可能
     */
    public function checkDoiGrantItemtype($item_type_id, $type)
    {
        $nii_type = $this->getNiiType($item_type_id);
        $result = $this->canRegistDoi($nii_type, $type);
        if(!$result)
        {
        	return false;
        }
        switch($nii_type)
        {
            case RepositoryConst::NIITYPE_JOURNAL_ARTICLE:
                return $this->checkDoiGrantItemtypeForJournalArticle($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_ARTICLE:
                return $this->checkDoiGrantItemtypeForArticle($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_PREPRINT:
                return $this->checkDoiGrantItemtypeForPreprint($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_DEPARTMENTAL_BULLETIN_PAPER:
                return $this->checkDoiGrantItemtypeForDepartmentalBulletinPaper($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_THESIS_OR_DISSERTATION:
                return $this->checkDoiGrantItemtypeForThesisOrDissertation($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_CONFERENCE_PAPER:
                return $this->checkDoiGrantItemtypeForConferencePaper($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_BOOK:
                return $this->checkDoiGrantItemtypeForBook($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_TECHNICAL_REPORT:
                return $this->checkDoiGrantItemtypeForTechnicalReport($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_RESEARCH_PAPER:
                return $this->checkDoiGrantItemtypeForResearchPaper($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_LEARNING_MATERIAL:
                return $this->checkDoiGrantItemtypeForLearningMaterial($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_DATA_OR_DATASET:
                return $this->checkDoiGrantItemtypeForDataOrDataset($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_SOFTWARE:
                return $this->checkDoiGrantItemtypeForSoftware($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_PRESENTATION:
                return $this->checkDoiGrantItemtypeForPresentation($item_type_id, $type);
                break;
            
            case RepositoryConst::NIITYPE_OTHERS:
                return $this->checkDoiGrantItemtypeForOthers($item_type_id, $type);
                break;
            
            default:
                return false;
        }
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * When the item type of NII type is a Journal Article, necessary mapping or meta data exists
     * アイテムタイプのNII typeがJournal Articleである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForJournalArticle($item_type_id, $type)
    {
        $can_grant = false;
        if($type == self::TYPE_JALC_DOI)
        {
            $isExistJalcdoiPrefix = $this->existJalcdoiPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            $spage_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_SPAGE);
            $biblio_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_BIBLIO);
            
            if($isExistJalcdoiPrefix &&
               _REPOSITORY_JALC_DOI == true &&
               count($fulltexturl_attr_id_array) > 0 &&
               (count($spage_attr_id_array) > 0 ||
               count($biblio_attr_id_array) > 0))
            {
                $can_grant = true;
            }
        }
        else if($type == self::TYPE_CROSS_REF)
        {
            $isExistCrossrefPrefix = $this->existCrossrefPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            $publisher_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_PUBLISHER);
            $jtitle_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_JTITLE);
            $issn_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_ISSN);
            $spage_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_SPAGE);
            $biblio_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_BIBLIO);
            $publisher_lang_array = $this->getDisplayLangTypeInArray($publisher_attr_id_array);
            $jtitle_lang_array = $this->getDisplayLangTypeInArray($jtitle_attr_id_array);
            
            if($isExistCrossrefPrefix &&
               _REPOSITORY_JALC_CROSSREF_DOI == true &&
               count($fulltexturl_attr_id_array) > 0 &&
               count($publisher_attr_id_array) > 0 &&
               in_array("english", $publisher_lang_array) &&
               (count($jtitle_attr_id_array) > 0 &&
               in_array("english", $jtitle_lang_array) ||
               count($biblio_attr_id_array) > 0) &&
               count($issn_attr_id_array) > 0 &&
               (count($spage_attr_id_array) > 0 ||
               count($biblio_attr_id_array) > 0))
            {
                $can_grant = true;
            }
        }
        return $can_grant;
    }

    /**
     * When the item type of NII type is the Article, the necessary mapping or meta data exists
     * アイテムタイプのNII typeがArticleである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForArticle($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForJournalArticle($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Preprint, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがPreprintである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForPreprint($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForJournalArticle($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Departmental Bulletin Paper, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがDepartmental Bulletin Paperである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForDepartmentalBulletinPaper($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForJournalArticle($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Thesis or Dissertation, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがThesis or Dissertationである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForThesisOrDissertation($item_type_id, $type)
    {
        $can_grant = false;
        if($type == self::TYPE_JALC_DOI)
        {
            $isExistJalcdoiPrefix = $this->existJalcdoiPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            
            if($isExistJalcdoiPrefix &&
               _REPOSITORY_JALC_DOI == true &&
               count($fulltexturl_attr_id_array) > 0)
            {
                $can_grant = true;
            }
        }
        else if($type == self::TYPE_LIBRARY_JALC_DOI)
        {
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            
            if(_REPOSITORY_JALC_DOI == true &&
               count($fulltexturl_attr_id_array) > 0)
            {
                $can_grant = true;
            }
        }
        return $can_grant;
    }

    /**
     * When the item type of NII type is Conference Paper, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがConference Paperである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForConferencePaper($item_type_id, $type)
    {
        $can_grant = false;
        if($type == self::TYPE_JALC_DOI)
        {
            $isExistJalcdoiPrefix = $this->existJalcdoiPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            
            if($isExistJalcdoiPrefix &&
               _REPOSITORY_JALC_DOI == true &&
               count($fulltexturl_attr_id_array) > 0)
            {
                $can_grant = true;
            }
        }
        else if($type == self::TYPE_CROSS_REF)
        {
            $isExistCrossrefPrefix = $this->existCrossrefPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            $publisher_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_PUBLISHER);
            $isbn_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_ISBN);
            $publisher_lang_array = $this->getDisplayLangTypeInArray($publisher_attr_id_array);
            
            if($isExistCrossrefPrefix &&
               _REPOSITORY_JALC_CROSSREF_DOI == true &&
               count($fulltexturl_attr_id_array) > 0 &&
               count($publisher_attr_id_array) > 0 &&
               in_array("english", $publisher_lang_array) &&
               count($isbn_attr_id_array) > 0)
            {
                $can_grant = true;
            }
        }
        return $can_grant;
    }

    /**
     * When the item type of NII type is Book, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがBookである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForBook($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForConferencePaper($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Technical Report, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがTechnical Reportである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForTechnicalReport($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForConferencePaper($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Research Paper, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがResearch Paperである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForResearchPaper($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForConferencePaper($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Learning Material, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがLearning Materialである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForLearningMaterial($item_type_id, $type)
    {
        $can_grant = false;
        if($type == self::TYPE_JALC_DOI)
        {
            $isExistJalcdoiPrefix = $this->existJalcdoiPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            
            if($isExistJalcdoiPrefix &&
               _REPOSITORY_JALC_DOI == true &&
               count($fulltexturl_attr_id_array) > 0)
            {
                $can_grant = true;
            }
        }
        return $can_grant;
    }

    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * When the item type of NII type is Data or Dataset, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがData or Datasetである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForDataOrDataset($item_type_id, $type)
    {
        $can_grant = false;
        if($type == self::TYPE_JALC_DOI)
        {
            $isExistJalcdoiPrefix = $this->existJalcdoiPrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            $creator_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_CREATOR);
            $publisher_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_PUBLISHER);
            
            if($isExistJalcdoiPrefix &&
               _REPOSITORY_JALC_DOI == true &&
               count($fulltexturl_attr_id_array) > 0 &&
               count($creator_attr_id_array) > 0 &&
               count($publisher_attr_id_array) > 0)
            {
                $can_grant = true;
            }
        }
        else if($type == self::TYPE_DATACITE)
        {
            $isExistDatacitePrefix = $this->existDatacitePrefix();
            $fulltexturl_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_FULL_TEXT_URL);
            $creator_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_CREATOR);
            $publisher_attr_id_array = $this->getAttributeIdFromJuNii2Mapping($item_type_id,
                self::JUNII2_MAPPING_PUBLISHER);
            $creator_lang_array = $this->getDisplayLangTypeInArray($creator_attr_id_array);
            $publisher_lang_array = $this->getDisplayLangTypeInArray($publisher_attr_id_array);
            
            if($isExistDatacitePrefix &&
               _REPOSITORY_JALC_DATACITE_DOI == true &&
               count($fulltexturl_attr_id_array) > 0 &&
               count($creator_attr_id_array) > 0 &&
               in_array("english", $creator_lang_array) &&
               count($publisher_attr_id_array) > 0 &&
               in_array("english", $publisher_lang_array))
            {
                $can_grant = true;
            }
        }
        return $can_grant;
    }

    /**
     * When the item type of NII type is Software, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがSoftwareである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForSoftware($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForDataOrDataset($item_type_id, $type);
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--

    /**
     * When the item type of NII type is Presentation, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがPresentationである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForPresentation($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForLearningMaterial($item_type_id, $type);
    }

    /**
     * When the item type of NII type is Others, of the necessary mapping or meta data exists
     * アイテムタイプのNII typeがOthersである時、必要なマッピングのメタデータが存在するか
     *
     * @param int $item_type_id Item type id アイテムタイプID
     * @param int $type Type タイプ
     *                  0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @return boolean Exist? 存在するか
     */
    private function checkDoiGrantItemtypeForOthers($item_type_id, $type)
    {
        return $this->checkDoiGrantItemtypeForLearningMaterial($item_type_id, $type);
    }
    
    // Add DataCite 2015/02/10 K.Sugimoto --start--
    /**
     * Specified NII type, determines whether it is possible DOI granted by DOI type
     * 指定したNII type、DOI種別でDOI付与できるかを判定する
     *
     * @param int $nii_type NIItype NIIタイプ
     *                      0:JaLC DOI, 1:Cross Ref, 2:国会図書館JaLC DOI, 3:DataCite
     * @param string $type Type タイプ
     * @return boolean Whether it is possible to grant 付与可能であるか
     */
    private function canRegistDoi($nii_type, $type)
    {
        $nii_type = strtolower($nii_type);
        
        if($type == self::TYPE_JALC_DOI)
        {
        	$doi = "jalc";
        }
        else if($type == self::TYPE_CROSS_REF)
        {
        	$doi = "crossref";
        }
        else if($type == self::TYPE_LIBRARY_JALC_DOI)
        {
        	$doi = "multiple_resolution";
        }
        else if($type == self::TYPE_DATACITE)
        {
        	$doi = "datacite";
        }
        
        // DOI付与フラグを取得
        $query = "SELECT ".$doi." ".
                 "FROM ".DATABASE_PREFIX."repository_doi_flag ".
                 "WHERE nii_type = ? ".
                 "AND is_delete = ? ;";
        $params = array();
        $params[] = $nii_type;
        $params[] = 0;
        $result = $this->executeSql($query, $params);
        
        if(count($result) > 0 && $result[0][$doi] == 1)
        {
            return true;
        }
        else
        {
        	return false;
        }
    }
    // Add DataCite 2015/02/10 K.Sugimoto --end--
    
    /**
     * To get the display language from the item attribute array
     * アイテム属性配列から表示言語を取得する
     *
     * @param array $attr_id_array Item attribute list アイテム属性配列
     *                             array["item_type_id"|"attribute_id"|"show_order"|"attribute_name"|"attribute_short_name"|"input_type"|"is_required"|"plural_enable"|"line_feed_enable"|"list_view_enable"|"hidden"|"junii2_mapping"|"dublin_core_mapping"|"lom_mapping"|"lido_mapping"|"spase_mapping"|"display_lang_type"|"ins_user_id"|"mod_user_id"|"del_user_id"|"ins_date"|"mod_date"|"del_date"|"is_delete"]
     * @return array Language list 言語一覧
     *               array[$ii]
     */
    private function getDisplayLangTypeInArray($attr_id_array)
    {
        $display_lang_type_array = array();
        for($num = 0; $num < count($attr_id_array); $num++)
        {
            $display_lang_type_array[] = $attr_id_array[$num]["display_lang_type"];
        }

        return $display_lang_type_array;
    }
}
?>