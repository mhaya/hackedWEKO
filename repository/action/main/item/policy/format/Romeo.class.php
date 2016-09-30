<?php

/**
 * Get Romeo policy class
 * Romeoポリシー情報取得クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Romeo.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 * Get Romeo policy class
 * Romeoポリシー情報取得クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Action_Main_Item_Policy_Romeo extends RepositoryAction
{
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

    /**
     * Romeo API URL
     * RomeoのAPIのURL
     */
    const ROMEO_API_URL = 'http://www.sherpa.ac.uk/romeo/api29.php';
    /**
     * Romeo API parameter jtitle
     * API用パラメータ(jtitle)
     */
    const ROMEO_API_JTITLE = '?jtitle=';
    /**
     * Romeo API parameter qtype
     * API用パラメータ(qtype)
     */
    const ROMEO_API_STARTS = '&qtype=starts';
    /**
     * Romeo API parameter issn
     * API用パラメータ(issn)
     */
    const ROMEO_API_ISSN = '?issn=';
    /**
     * Romeo API parameter version
     * API用パラメータ(version)
     */
    const ROMEO_API_VERSIONALL = '&version=all';
    /**
     * Romeo API parameter ak
     * API用パラメータ(ak)
     */
    const ROMEO_API_AK = '&ak=';
    
    /**
     * XML parser const tag
     * XML解釈用定数(tag)
     */
    const XML_PARSER_TAG       = "tag";
    /**
     * XML parser const tag
     * XML解釈用定数(type)
     */
    const XML_PARSER_TYPE      = "type";
    /**
     * XML parser const tag
     * XML解釈用定数(value)
     */
    const XML_PARSER_VALUE     = "value";
    /**
     * XML parser const tag
     * XML解釈用定数(open)
     */
    const XML_PARSER_OPEN      = "open";
    /**
     * XML parser const tag
     * XML解釈用定数(complete)
     */
    const XML_PARSER_COMPLETE  = "complete";
    /**
     * XML parser const tag
     * XML解釈用定数(close)
     */
    const XML_PARSER_CLOSE     = "close";
    /**
     * XML parser const tag
     * XML解釈用定数(attributes)
     */
    const XML_PARSER_ATTRIBUTE = "attributes";
    
    /**
     * Romeo API XML Tags journal
     * Romeo形式のXMLタグ名(journal)
     */
    const ROMEO_XML_TAG_JOURNAL           = "journal";
    /**
     * Romeo API XML Tags jtitle
     * Romeo形式のXMLタグ名(jtitle)
     */
    const ROMEO_XML_TAG_JTITLE            = "jtitle";
    /**
     * Romeo API XML Tags issn
     * Romeo形式のXMLタグ名(issn)
     */
    const ROMEO_XML_TAG_ISSN              = "issn";
    /**
     * Romeo API XML Tags language
     * Romeo形式のXMLタグ名(language)
     */
    const ROMEO_XML_TAG_LANGUAGE          = "language";
    /**
     * Romeo API XML Tags acquiring
     * Romeo形式のXMLタグ名(acquiring)
     */
    const POLICY_XML_TAG_ACQUIRING        = "acquiring";
    /**
     * Romeo API XML Tags journalId
     * Romeo形式のXMLタグ名(journalId)
     */
    const ROMEO_XML_JOURNAL_ID = "journalId";
    
    /**
     * Romeo register key
     * Romeo発番用キー
     */
    const ROMEO_REGISTER_KEY = 'E9DKYZC7MVE';
    
    /**
     * Repository_Action_Main_Item_Policy_Romeo constructor.
     * コンストラクタ
     *
     * @param Session $session Session management objects
     *                          Session管理オブジェクト
     * @param DbObjectAdodb $db Db management objects
     *                           DB管理オブジェクト
     */
    public function __construct($session, $db)
    {
        $this->Session = $session;
        $this->Db = $db;
    }
    
    /**
     * Get Romeo journal title list
     * Romeoから雑誌名一覧を取得する
     *
     * @param string $jtitleStr journal name 雑誌名
     * @return array return Romeo XML to array Romeoからの返却XML情報を配列に変換した情報
     *                array[$xml]
     */
    public function getRomeoJtitleList($jtitleStr)
    {
        /**
         * 下記を実施する。
         * 1.リクエストパラメーターjtitleのURLエンコードをデコードする。
         * 2.RomeoのAPIに著作権ポリシーを問合せる。
         * 3.2の問合せ結果をJSON形式に整形する。
         * 4.3のJSONを出力する。
         */

        //シングルバイトしかゆるさない
        if (strlen($jtitleStr) != mb_strlen($jtitleStr, 'UTF-8')) {
            return null;
        }
        
        // APIから雑誌名での検索結果を取得する。
        $jtitleStr = str_replace(' ', '%20', $jtitleStr);
        //$apiUrl   = self::ROMEO_API_URL.self::ROMEO_API_JTITLE.$jtitleStr.self::ROMEO_API_STARTS.'&ak=E9DKYZC7MVE';
        $apiUrl   = self::ROMEO_API_URL.self::ROMEO_API_JTITLE.$jtitleStr.self::ROMEO_API_STARTS;
        $response = $this->sendHttpRequest( $apiUrl );
        
        $xmlStr   = $response["body"];
        if( strlen( $xmlStr ) == 0)
        {
            return '';
        }
        
        // parse xml
        $vals = $this->parseXml( $xmlStr );
        
        $return_array = array();
        $temp_array = array(self::ROMEO_XML_JOURNAL_ID=>"", 
                            self::ROMEO_XML_TAG_JTITLE=>"", 
                            self::ROMEO_XML_TAG_ISSN=>"", 
                            self::POLICY_XML_TAG_ACQUIRING=>"Romeo"
                            );
        foreach($vals as $val)
        {
            switch($val[self::XML_PARSER_TAG]){
                //読み出し開始/終了
                case self::ROMEO_XML_TAG_JOURNAL:
                    if($val[self::XML_PARSER_TYPE] == self::XML_PARSER_OPEN){
                        //読み出し開始
                        $temp_array = array(
                                              self::ROMEO_XML_JOURNAL_ID=>"", 
                                              self::ROMEO_XML_TAG_JTITLE=>"", 
                                              self::ROMEO_XML_TAG_ISSN=>"", 
                                              self::POLICY_XML_TAG_ACQUIRING=>"Romeo"
                                              );
                    }
                    else if($val[self::XML_PARSER_TYPE] == self::XML_PARSER_CLOSE){
                        //読み出し終了
                        array_push($return_array, $temp_array);
                    }
                    break;
                
                //jtitle
                case self::ROMEO_XML_TAG_JTITLE:
                    $temp_array[self::ROMEO_XML_TAG_JTITLE] = $val[self::XML_PARSER_VALUE];
                    break;
                //issn
                case self::ROMEO_XML_TAG_ISSN:
                    $temp_array[self::ROMEO_XML_TAG_ISSN] = "";
                    if(isset($val[self::XML_PARSER_VALUE]))
                    {
                        $temp_array[self::ROMEO_XML_TAG_ISSN] = $val[self::XML_PARSER_VALUE]; 
                    }
                    break;
                default:break;
            }
        }
        
        return $return_array;
    }
    
    /**
     * Inquiring about the copyright policy in the API of Romeo.
     * RomeoのAPIに著作権ポリシーを問合せる。
     * 
     * @param string $issn target ISSN 検索対象のISSN
     * @return string Romeo string format XML Romeo形式のXML文字列
     */
    public function getRomeoXml( $issn )
    {
        /**
         * 1.下記APIから雑誌の著作権ポリシーを取得する。
         *   http://www.sherpa.ac.uk/romeo/api29.php?issn=[ISSN]&version=all
         * 2.1の取得結果を返す。
         */

        // initialize response string
        $xmlStr = "";
        
        // 1.APIから雑誌名での検索結果を取得する。
        //$apiUrl   = self::ROMEO_API_URL.self::ROMEO_API_ISSN.$issn.self::ROMEO_API_VERSIONALL.'&ak=E9DKYZC7MVE';
        $apiUrl   = self::ROMEO_API_URL.self::ROMEO_API_ISSN.$issn.self::ROMEO_API_VERSIONALL;
        $response = $this->sendHttpRequest( $apiUrl );
        $xmlStr   = $response["body"];
        
        // 3の取得結果を返す。
        return $xmlStr;
    }

    /**
     * http request send method
     * HTTPリクエストを送信する
     * 
     * @param string $reqUrl request URL リクエストURL
     * @return array response body レスポンス
     *                array["code"|"header"|"body"|"cookies"]
     */
    private function sendHttpRequest( $reqUrl )
    {
        // initialize response array
        $res = array( "code"=>"", "header"=>array(), "body"=>"", "cookies"=>array() );
        
        // make request parameter
        $option = array(
            "timeout" => "10",
            "allowRedirects" => true,
            "maxRedirects" => 3, 
        );
        
        // get proxy
        $proxy = $this->getProxySetting();
        if($proxy['proxy_mode'] == 1)
        {
            $option = array( 
                    "timeout" => "10",
                    "allowRedirects" => true, 
                    "maxRedirects" => 3,
                    "proxy_host"=>$proxy['proxy_host'],
                    "proxy_port"=>$proxy['proxy_port'],
                    "proxy_user"=>$proxy['proxy_user'],
                    "proxy_pass"=>$proxy['proxy_pass']
            );
        }
        
        // make http request
        $http = new HTTP_Request($reqUrl, $option);
        $http->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']);
        $response = $http->sendRequest();
        if (!PEAR::isError($response))
        {
            $res["code"]    = $http->getResponseCode();     // get ResponseCode(200etc.)
            $res["header"]  = $http->getResponseHeader();   // get ResponseHeader
            $res["body"]    = $http->getResponseBody();     // get ResponseBody
            $res["cookies"] = $http->getResponseCookies();  // get Cookie
        }
        
        // return response
        return $res;
    }
    
    /**
     * xml parse
     * XMLを解釈する
     *
     * @param string $str XML string XML文字列
     * @return array parse XML to array string XMLを解釈した配列情報
     *                array[$xml]
     */
    private function parseXML( $str )
    {
        $xmlParser = xml_parser_create();
        // パースした後のキー文字列大文字化を無効に
        xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($xmlParser, $str, $vals);
        xml_parser_free($xmlParser);
        return $vals;
    }
    
}
?>