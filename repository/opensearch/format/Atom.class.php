<?php
// --------------------------------------------------------------------
//
// $Id: Atom.class.php 43084 2014-10-20 06:53:41Z yuko_nakao $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
require_once WEBAPP_DIR."/modules/repository/opensearch/format/FormatAbstract.class.php";

/**
 * repository search class
 * 
 */
class Repository_OpenSearch_Atom extends Repository_Opensearch_FormatAbstract
{
    const XMLNS_ATOM        = "http://www.w3.org/2005/Atom";
    const XMLNS_DC          = "http://purl.org/dc/elements/1.1/";
    const XMLNS_PRISM       = "http://prismstandard.org/namespaces/basic/2.0/";
    const XMLNS_OPEN_SEARCH = "http://a9.com/-/spec/opensearch/1.1/";
    const XMLNS_WEKO_LOG    = "/wekolog/";
    
    /**
     * コンストラクタ
     */
    public function __construct($session, $db)
    {
        parent::__construct($session, $db);
    }
    
    /**
     * make ATOM XML for open search 
     * 
     * @param array $result RepositorySearch $searchResult
     * @param array $searchResult search result
     * @return string
     */
    public function outputXml($request, $total, $sIdx, $searchResult)
    {
        ///// set data /////
        // Bug Fix WEKO-2014-050 K.Sugimoto 2014/08/04 --start--
        $this->total = $total;
        // Bug Fix WEKO-2014-050 K.Sugimoto 2014/08/04 --end--
        $this->startIndex = $sIdx;
        
        $xml = "";
        
        ///// header /////
        $xml .= $this->outputHeader($request);
        
        ///// request parameter /////
        $xml .= $this->outputParameter($request, $searchResult);
        
        ///// entry /////
        $xml .= $this->outputEntry($request, $searchResult);
        
        ///// footer /////
        $xml .= $this->outputFooter();
        
        return $xml;
    }
    
    /**
     * output header
     * 
     * @param array $result RepositorySearch $searchResult
     * @return string
     */
    private function outputHeader($request)
    {
        $lang = RepositoryConst::ITEM_ATTR_TYPE_LANG_JA;
        if($request[self::REQUEST_LANG] == RepositoryConst::ITEM_ATTR_TYPE_LANG_EN)
        {
            $lang = RepositoryConst::ITEM_ATTR_TYPE_LANG_EN;
        }
        
        $xml =  '<?xml version="1.0" encoding="UTF-8" ?>'.self::LF.
                '<feed'.
                '   xmlns="'.self::XMLNS_ATOM.'"'.self::LF.
                '   xmlns:dc="'.self::XMLNS_DC.'"'.self::LF.
                '   xmlns:prism="'.self::XMLNS_PRISM.'"'.self::LF.
                '   xmlns:opensearch="'.self::XMLNS_OPEN_SEARCH.'"'.self::LF.
                '   xmlns:wekolog="'.BASE_URL.self::XMLNS_WEKO_LOG.'"'.self::LF.
                '   xml:lang="'.$lang.'">'.self::LF.self::LF;
        return $xml;
    }
    
    /**
     * @param array $result RepositorySearch $searchResult
     * @param array $searchResult search result
     * @return string
     */
    private function outputParameter($request, $searchResult)
    {
        ///// request parameter string /////
        $requesturl = BASE_URL;
        if(substr($request_url, -1, 1)!="/"){
            $request_url .= "/";
        }
        $request_url .= "?".$_SERVER['QUERY_STRING'];
        
        ///// repository name /////
        $errorMsg = "";
        $this->RepositoryAction->getAdminParam("prvd_Identify_repositoryName", $repositoryName, $errorMsg);
        $repositoryName = RepositoryOutputFilter::string($repositoryName);
        if(strlen($repositoryName) == 0)
        {
            $repositoryName = "WEKO";
        }
        
        ///// feed title /////
        $feed_title = $repositoryName." OpenSearch";
        if(strlen($request[self::REQUEST_WEKO_ID]) > 0)
        {
            $feed_title .= " - "."WEKOID : ".$request[self::REQUEST_WEKO_ID];
        }
        if(strlen($request[self::REQUEST_KEYWORD]) > 0)
        {
            $feed_title .= " : ".$request[self::REQUEST_KEYWORD];
        }
        if(strlen($request[self::REQUEST_INDEX_ID]) > 0)
        {
            $feed_title .= " : ".$this->getIndexPath($request[self::REQUEST_INDEX_ID], ">");
        }
        
        ///// search date /////
        $DATE = new Date();
        $search_date = $DATE->format("%Y-%m-%dT%H:%M:%S%O");
        
        ///// output search request /////
        
        // feed title
        $xml .= '   <title>'.$this->RepositoryAction->forXmlChange($feed_title).'</title>'.$LF;
        
        // request url
        $xml .= '   <link href="'.$this->RepositoryAction->forXmlChange($request_url).'" />'.$LF;
        
        // request url
        $xml .= '   <id>'.$this->RepositoryAction->forXmlChange($request_url).'</id>'.$LF;
        
        // search date
        $xml .= '   <updated>'.$this->RepositoryAction->forXmlChange($search_date).'</updated>'.$LF;
        
        // search total
        $xml .= '   <opensearch:totalResults>'.$this->total.'</opensearch:totalResults>'.$LF;
        if($this->total > 0){
            // start no
            $xml .= '   <opensearch:startIndex>'.$this->startIndex.'</opensearch:startIndex>'.$LF;
            // disp item num
            $xml .= '   <opensearch:itemsPerPage>'.count($searchResult).'</opensearch:itemsPerPage>'.$LF.$LF;
        }
        
        return $xml;
    }
    
    
    /**
     * output entry
     * 
     * @param int $itemId
     * @param int $itemNo
     * @return string
     *
     */
    private function outputEntry($request, $searchResult)
    {
        $xml = "";
        $now_lang = $request[self::REQUEST_LANG];
        $display_lang = $this->getAlternativeLanguage();
        
        if(strlen($request[self::REQUEST_LOG_TERM]) > 0)
        {
            // add log data
            $this->getItemLogData($request, $searchResult);
        }
        
        ///// create XML /////
        for($ii=0;$ii<count($searchResult);$ii++)
        {
            $itemData = array();
            $itemData = $this->getOutputData($request, $searchResult[$ii]["item_id"], $searchResult[$ii]["item_no"]);

            if(strlen($request[self::REQUEST_LOG_TERM]) > 0)
            {
                // set log result
                $itemData[self::DATA_WEKO_LOG_TERM] = "";
                if(isset($searchResult[$ii][self::DATA_WEKO_LOG_TERM]))
                {
                    $itemData[self::DATA_WEKO_LOG_TERM] = $searchResult[$ii][self::DATA_WEKO_LOG_TERM];
                }
                $itemData[self::DATA_WEKO_LOG_VIEW] = "";
                if(isset($searchResult[$ii][self::DATA_WEKO_LOG_VIEW]))
                {
                    $itemData[self::DATA_WEKO_LOG_VIEW] = $searchResult[$ii][self::DATA_WEKO_LOG_VIEW];
                }
                $itemData[self::DATA_WEKO_LOG_DOWNLOAD] = "";
                if(isset($searchResult[$ii][self::DATA_WEKO_LOG_DOWNLOAD]))
                {
                    $itemData[self::DATA_WEKO_LOG_DOWNLOAD] = $searchResult[$ii][self::DATA_WEKO_LOG_DOWNLOAD];
                }
            }
            
            // 
            $xml .= '   <entry>'.self::LF;
            
            // title
            $xml .= '       <title>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_TITLE]).'</title>'.self::LF;
            // URL
            $xml .= '       <link href="'.$this->RepositoryAction->forXmlChange($searchResult[$ii]["uri"]).'" />'.self::LF;
            // swrc url
            $xml .= '       <link rel="alternate" type="text/xml" href="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_SWRC]).'" />'.self::LF;
            // oai-ore
            for($jj=0; $jj<count($itemData[self::DATA_OAIORE]); $jj++)
            {
                $xml .= '       <link rel="alternate" type="text/xml" href="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_OAIORE][$jj]).'" />'.self::LF;
            }
            // id => detail uri
            $xml .= '       <id>'.$this->RepositoryAction->forXmlChange($searchResult[$ii]["uri"]).'</id>'.self::LF;
            
            // suffix(weko id)
            if(strlen($itemData[self::DATA_WEKO_ID]) > 0)
            {
                $xml .= '       <dc:identifier>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_WEKO_ID]).'</dc:identifier>'.self::LF;
            }
            
            // mapping info
            $xml .= '       <prism:aggregationType>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_MAPPING_INFO]).'</prism:aggregationType>'.self::LF;

            // item type name
            $xml .= '       <dc:type>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_ITEM_TYPE_NAME]).'</dc:type>'.self::LF;
            
            // mime type
            for($jj=0; $jj<count($itemData[self::DATA_MIME_TYPE]); $jj++)
            {
                $xml .= '       <dc:format>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_MIME_TYPE][$jj]).'</dc:format>'.self::LF;
            }
            
            // file_id
            for($jj=0; $jj<count($itemData[self::DATA_FILE_URI]); $jj++)
            {
                $xml .= '       <dc:identifier>'.$this->RepositoryAction->forXmlChange("file_id:".$itemData[self::DATA_FILE_URI][$jj]).'</dc:identifier>'.self::LF;
                //enclosure file link
                //$xml .= '       <enclosure url="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_FILE_URI][$jj]).'" type="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_MIME_TYPE][$jj]).'" />'.self::LF;
                $xml .= '       <link rel="enclosure" title="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_FILE_NAME][$jj]).'" type="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_MIME_TYPE][$jj]).'" href="'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_FILE_URI][$jj]).'" />'.self::LF;
                
            }
            
            // creator
            for($jj=0;$jj<count($itemData[self::DATA_CREATOR]);$jj++)
            {
                if(strlen($itemData["creator_lang"][$jj]) == 0) {
                    $xml .= '       <author>'.self::LF;
                } else if($display_lang[$now_lang] == 1 || $itemData["creator_lang"][$jj] == $now_lang) {
                    $xml .= '       <author xml:lang="'.RepositoryOutputFilter::language($itemData["creator_lang"][$jj]).'">'.self::LF;
                } else {
                    continue;
                }
                $xml .= '           <name>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_CREATOR][$jj]).'</name>'.self::LF;
                $xml .= '       </author>'.self::LF;
            }
            
            // publisher
            for($jj=0;$jj<count($itemData[self::DATA_PUBLISHER]);$jj++)
            {
                if(strlen($itemData["publisher_lang"][$jj]) == 0) {
                    $xml .= '       <dc:publisher>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_PUBLISHER][$jj]).'</dc:publisher>'.self::LF; // 出版者
                } else if($display_lang[$now_lang] == 1 || $itemData["publisher_lang"][$jj] == $now_lang) {
                    $xml .= '       <dc:publisher xml:lang="'.RepositoryOutputFilter::language($itemData["publisher_lang"][$jj]).'">'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_PUBLISHER][$jj]).'</dc:publisher>'.self::LF;
                } else {
                    continue;
                }
            }
            
            // index name
            for($jj=0; $jj<count($itemData[self::DATA_INDEX_PATH]); $jj++)
            {
                $xml .= '       <dc:subject>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_INDEX_PATH][$jj]).'</dc:subject>'.self::LF;
            }
            
            // jtitle
            if(strlen($itemData[self::DATA_JTITLE]) > 0)
            {
                $xml .= '       <prism:publicationName>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_JTITLE]).'</prism:publicationName>'.self::LF;
            }
            
            // issn
            if(strlen($itemData[self::DATA_ISSN]) > 0)
            {
                $xml .= '       <prism:issn>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_ISSN]).'</prism:issn>'.self::LF;
            }
            
            // volume
            if(strlen($itemData[self::DATA_VOLUME]) > 0)
            {
                $xml .= '       <prism:volume>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_VOLUME]).'</prism:volume>'.self::LF;
            }
            
            // issue
            if(strlen($itemData[self::DATA_ISSUE]) > 0)
            {
                $xml .= '       <prism:number>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_ISSUE]).'</prism:number>'.self::LF;
            }
            
            // spage
            if(strlen($itemData[self::DATA_SPAGE]) > 0)
            {
                $xml .= '       <prism:startingPage>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_SPAGE]).'</prism:startingPage>'.self::LF;
            }
            
            // epage
            if(strlen($itemData[self::DATA_EPAGE]) > 0)
            {
                $xml .= '       <prism:endingPage>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_EPAGE]).'</prism:endingPage>'.self::LF;
            }
            
            // date of issued
            if(strlen($itemData[self::DATA_DATE_OF_ISSUED]) > 0)
            {
                $xml .= '       <prism:publicationDate>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_DATE_OF_ISSUED]).'</prism:publicationDate>'.self::LF;
            }
            
            // description
            for($jj=0; $jj<count($itemData[self::DATA_DESCRIPTION]); $jj++)
            {
                if(strlen($itemData["description_lang"][$jj]) == 0) {
                    $xml .= '       <content>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_DESCRIPTION][$jj]).'</content>'.self::LF;
                } else if($display_lang[$now_lang] == 1 || $itemData["description_lang"][$jj] == $now_lang) {
                    $xml .= '       <content xml:lang="'.RepositoryOutputFilter::language($itemData["description_lang"][$jj]).'">'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_DESCRIPTION][$jj]).'</content>'.self::LF;    // 抄録
                } else {
                    continue;
                }
            }
            // mod_date
            $mod_date = $this->RepositoryAction->changeDatetimeToW3C($itemData[self::DATA_MOD_DATE]);
            $xml .= '       <updated>'.$this->RepositoryAction->forXmlChange($mod_date).'</updated>'.self::LF;
            
            // log_term
            if(strlen($request[self::REQUEST_LOG_TERM]) > 0)
            {
                if(strlen($itemData[self::DATA_WEKO_LOG_TERM]) > 0)
                {
                    $xml .= '       <wekolog:terms>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_WEKO_LOG_TERM]).'</wekolog:terms>'.self::LF;
                }
                if(strlen($itemData[self::DATA_WEKO_LOG_VIEW]) > 0)
                {
                    $xml .= '       <wekolog:view>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_WEKO_LOG_VIEW]).'</wekolog:view>'.self::LF;
                }
                
                if(strlen($itemData[self::DATA_WEKO_LOG_DOWNLOAD]) > 0)
                {
                    $xml .= '       <wekolog:download>'.$this->RepositoryAction->forXmlChange($itemData[self::DATA_WEKO_LOG_DOWNLOAD]).'</wekolog:download>'.self::LF;
                }
            }
            
            // ins_date
            if(strlen($itemData[self::DATA_INS_DATE]) > 0)
            {
                $ins_date = $this->RepositoryAction->changeDatetimeToW3C($itemData[self::DATA_INS_DATE]);
                $xml .= '       <prism:creationDate>'.$this->RepositoryAction->forXmlChange($ins_date).'</prism:creationDate>'.self::LF;
            }
            
            // mod_date
            if(strlen($itemData[self::DATA_MOD_DATE]) > 0)
            {
                $mod_date = $this->RepositoryAction->changeDatetimeToW3C($itemData[self::DATA_MOD_DATE]);
                $xml .= '       <prism:modificationDate>'.$this->RepositoryAction->forXmlChange($mod_date).'</prism:modificationDate>'.self::LF;
            }
            
            $xml .= '   </entry>'.self::LF.self::LF;
        }
        return $xml;
    }
    
    /**
     * output footer
     *
     * @return string
     */
    private function outputFooter()
    {
        $xml = '</feed>';
        return $xml;
    }
    
}
?>