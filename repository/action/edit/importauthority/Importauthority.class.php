<?php

/**
 * Name authority import action class
 * 著者名典拠インポートアクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Importauthority.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Name authority common classes
 * 著者名典拠共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/NameAuthority.class.php';

/**
 * Name authority import action class
 * 著者名典拠インポートアクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Importauthority extends RepositoryAction
{
    // component
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
    public $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
    public $Db = null;
    
    // menber
    /**
     * DError message
     * エラーメッセージ
     *
     * @var string
     */
    private $error_msg = null;
    /**
     * Line num
     * 行数
     *
     * @var int
     */
    private $lineNum = 0;
    
    // Const
    /**
     * Author id
     * 著者ID
     *
     * @var string
     */
    const AUTHOR_ID = "author_id";
    /**
     * Language
     * 言語
     *
     * @var string
     */
    const LANGUAGE = "language";
    /**
     * Last name
     * 姓
     *
     * @var string
     */
    const FAMILY = "family";
    /**
     * Name
     * 名
     *
     * @var string
     */
    const NAME = "name";
    /**
     * Last name(read)
     * 姓(ヨミ)
     *
     * @var string
     */
    const FAMILY_RUBY = "family_ruby";
    /**
     * Name(read)
     * 名(ヨミ)
     *
     * @var string
     */
    const NAME_RUBY = "name_ruby";
    /**
     * External author id
     * 外部著者ID
     *
     * @var string
     */
    const EXTERNAL_AUTHOR_ID = "external_author_id";
    /**
     * Prefix ID
     * Prefix ID
     *
     * @var string
     */
    const PREFIX_ID = "prefix_id";
    /**
     * Suffix
     * Suffix
     *
     * @var string
     */
    const SUFFIX = "suffix";
    
    /**
     * Import file path
     * インポートファイルパス
     *
     * @var string
     */
    const IMPORT_FILE_NAME = "/import.tsv";
    
    /**
     * Constructor
     * コンストラクタ
     *
     * @param Session $session Session management objects Session管理オブジェクト
     * @param Dbobject $db Database management objects データベース管理オブジェクト
     */
    public function __construct($session, $db){
        if(isset($session)){
            $this->Session = $session;
        }
        if(isset($db)){
            $this->Db = $db;
        }
    }
    
    /**
     * Name authority import run
     * 著者名典拠インポート実行
     *
     * @return string Result 結果
     */
    function executeApp()
    {
        // get import.tsv file
        $this->lineNum = 0;
        $fileData = array();
        $tmpFile = WEBAPP_DIR.'/uploads/repository' . self::IMPORT_FILE_NAME;
        $fileData = $this->readFile($tmpFile);
        if($fileData === false)
        {
            $this->error_msg = "error file read";
        }
        $this->lineNum = count($fileData);
        
        if($this->lineNum > 0)
        {
            // divide by tab
            $dividedArray = array();
            $this->divideTsvToArray($fileData, $dividedArray);
        
            // create metadata for name authority
            $metadataForNameAuthority = array();
            $this->createMetadataForNameAuthority($dividedArray, $metadataForNameAuthority);
        
            // insert Name Authority
            $nameAuthority = new NameAuthority($this->Session, $this->Db );
            for ($nCnt = 0; $nCnt < $this->lineNum; $nCnt++)
            {
                if(count($metadataForNameAuthority[$nCnt]) < 1)
                {
                    continue;
                }
                if(strlen($metadataForNameAuthority[$nCnt][self::FAMILY]) < 1)
                {
                    continue;
                }
                $nameAuthority->entryNameAuthority($metadataForNameAuthority[$nCnt], $this->error_msg);
            }
        }
        
        if(strlen($this->error_msg) > 0) {
            echo $this->error_msg;
        }
        else
        {
            echo 'Successfully Import';
        }
        
        return 'success';
    }
    
	/**
     * Read file
     * ファイル読込
     *
     * @param string $tmpFile Temporary file path 一時ファイルパス
     * @return array[$ii]
     */
    public function readFile($tmpFile)
    {
        // file import and insert array
        $importFile = $tmpFile;
        $importArray = file($importFile);
        if ($importArray === false)
        {
            return false;
        }
        // replace new line character
        for ($ii=0; $ii < count($importArray); $ii++){
            $importArray[$ii] = str_replace(array("\r\n","\r","\n"), '', $importArray[$ii]); 
        }
        return $importArray;
    }
    
    /**
     * divide tsv to array
     * tsvファイルを分割する
     *
     * @param array $importArray Import data インポートデータ
     *                           array[$ii]
     * @param array $dividedArray Divide result 分割結果
     *                            array[$ii]
     */
    public function divideTsvToArray($importArray, &$dividedData)
    {
        $dividedData = array();
        $tmpArray = array();
        for($nCnt = 0; $nCnt < count($importArray); $nCnt++)
        {
            $tmpArray = explode("\t", $importArray[$nCnt]);
            array_push($dividedData, $tmpArray);
        }
    }
    
    /**
     * set metadata for name authority
     * this method expect the data array is
     * 0:name 1:family 2:e_mail_address 3:name_ruby 4:family_ruby 5orLater:external_author_id
     * 著者名典拠にメタデータを設定する
     *
     * @param dividedData Divided data 分割データ
     * @param metadataNameAuthority Name authority data 著者名典拠データ
     */
    public function createMetadataForNameAuthority($dividedData, &$metadataNameAuthority)
    {
        $metadataNameAuthority = array();
        $headerNum = count($dividedData[0]);
        $nameAuthorityIndex = 0;
        for($nCnt = 1; $nCnt < count($dividedData); $nCnt++)
        {
            $metadataNameAuthority[$nameAuthorityIndex][self::AUTHOR_ID] = 0;
            $metadataNameAuthority[$nameAuthorityIndex][self::LANGUAGE] = "";
            $metadataNameAuthority[$nameAuthorityIndex][self::NAME] = "";
            $metadataNameAuthority[$nameAuthorityIndex][self::FAMILY] = "";
            $metadataNameAuthority[$nameAuthorityIndex][self::NAME_RUBY] = "";
            $metadataNameAuthority[$nameAuthorityIndex][self::FAMILY_RUBY] = "";
            $metadataNameAuthority[$nameAuthorityIndex][self::EXTERNAL_AUTHOR_ID] = array();
            $dataNum = $headerNum;
            // when header number is bigger than data
            if($headerNum > count($dividedData[$nCnt]))
            {
                // loop by data number. else loop by header number
                $dataNum = count($dividedData[$nCnt]);
            }
            for($columnNum = 0; $columnNum < $dataNum; $columnNum++)
            {
                switch ($columnNum)
                {
                    // Name
                    case 0:
                        if(isset($dividedData[$nCnt][$columnNum]) && strlen($dividedData[$nCnt][$columnNum]) >= 1)
                        {
                            $metadataNameAuthority[$nameAuthorityIndex][self::NAME] = $dividedData[$nCnt][$columnNum];
                        }
                        break;
                    // Family
                    case 1:
                        if(isset($dividedData[$nCnt][$columnNum]) && strlen($dividedData[$nCnt][$columnNum]) >= 1)
                        {
                            $metadataNameAuthority[$nameAuthorityIndex][self::FAMILY] = $dividedData[$nCnt][$columnNum];
                        }
                        break;
                    // e Mail Address
                    case 2:
                        if(isset($dividedData[$nCnt][$columnNum]) && strlen($dividedData[$nCnt][$columnNum]) >= 1)
                        {
                            $tmpArray = array(
                                                self::PREFIX_ID => '0',
                                                self::SUFFIX => $dividedData[$nCnt][$columnNum]
                                            );
                            array_push($metadataNameAuthority[$nameAuthorityIndex][self::EXTERNAL_AUTHOR_ID], $tmpArray);
                        }
                        break;
                    // Name ruby
                    case 3:
                        if(isset($dividedData[$nCnt][$columnNum]) && strlen($dividedData[$nCnt][$columnNum]) >= 1)
                        {
                            $metadataNameAuthority[$nameAuthorityIndex][self::NAME_RUBY] = $dividedData[$nCnt][$columnNum];
                        }
                        break;
                    // Family ruby
                    case 4:
                        if(isset($dividedData[$nCnt][$columnNum]) && strlen($dividedData[$nCnt][$columnNum]) >= 1)
                        {
                            $metadataNameAuthority[$nameAuthorityIndex][self::FAMILY_RUBY] = $dividedData[$nCnt][$columnNum];
                        }
                        break;
                    // external author id without e_mail_address
                    default:
                        if(isset($dividedData[$nCnt][$columnNum]) && strlen($dividedData[$nCnt][$columnNum]) >= 1)
                        {
                            $prefixId = $columnNum - 4;
                            $tmpArray = array(
                                                self::PREFIX_ID => $prefixId,
                                                self::SUFFIX => $dividedData[$nCnt][$columnNum]
                                            );
                            array_push($metadataNameAuthority[$nameAuthorityIndex][self::EXTERNAL_AUTHOR_ID], $tmpArray);
                        }
                        break;
                }
            }
            $nameAuthorityIndex++;
        }
    }
}
?>
