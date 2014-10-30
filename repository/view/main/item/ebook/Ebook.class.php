<?php
// --------------------------------------------------------------------
//
// $Id: Ebook.class.php 530 2012-07-30 08:21:05Z ivis $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';


/**
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
class Repository_View_Main_Item_Ebook extends RepositoryAction
{
    // Components
    public $Db = null;
    public $Session = null;
    
    // request parameter
    public $item_id = null;
    public $item_no = null;
    public $attribute_id = null;
    public $file_no = null;
    
    // member
    public $encode_baseurl = "";
    public $flash_url = "";
    public $option = "";
    public $size = "";
    public $block_id = null;
    public $page_id = null;
    public $pay = null;
    
    // Add get page count 2011/02/07 Y.Nakao --start--
    public $division = 0;
    // Add get page count 2011/02/07 Y.Nakao --end--
    
    // Fix download type for not download. --start--
    public $file_download_type = "";
    // Fix download type for not download. --end--
    
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {    
        try {
            // init action
            $result = $this->initAction();
            if ( $result === false ) {
                $exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );
                $DetailMsg = null;
                $exception->setDetailMsg( $DetailMsg );
                $this->failTrans();
                throw $exception;
            }
            // Add remove downloadInfo 2011/10/13 K.Matsuo
            $this->Session->removeParameter('repository'.$this->block_id.'FileDownloadKey');
            $this->Session->removeParameter("fromFlash");
            
            $flash_path = "";
            $flash_dir_path = $this->getFlashFolder( $this->item_id,
                                                     $this->attribute_id,
                                                     $this->file_no);
            if(strlen($flash_dir_path) > 0){
                if( file_exists($flash_dir_path.'/weko.swf') ){
                    $flash_path = $this->item_id."_".$this->attribute_id."_".$this->file_no.'/weko.swf';
                    $this->division = 0;
                } else if( file_exists($flash_dir_path.'/weko1.swf') ){
                    $flash_path = $this->item_id."_".$this->attribute_id."_".$this->file_no.'/weko1.swf';
                    $this->division = $this->getFlashPagecount( $this->item_id,
                                                                $this->attribute_id,
                                                                $this->file_no);
                }
            }
            $this->encode_baseurl = urlencode(BASE_URL);
            $tmp_flash_url = BASE_URL."/?action=repository_action_common_download".
                               "&item_id=".$this->item_id.
                               "&item_no=".$this->item_no.
                               "&attribute_id=".$this->attribute_id.
                               "&file_no=".$this->file_no.
                               "&block_id=".$this->block_id.
                               "&page_id=".$this->page_id.
                               "&flash=true".
                               "&flashpath=".$flash_path;
            if(strlen($this->pay)>0) { $tmp_flash_url .= '&pay='.$this->pay; }
            $this->flash_url = urlencode($tmp_flash_url);
            $this->option = urlencode(BASE_URL."/weko/ebook/OptionSetting.xml");	// Modify Directory specification BASE_URL K.Matsuo 2011/9/2
            $this->size = "";
            
            // modify check downloadtype K.Matsuo 2011/10/18 --start--
            require_once WEBAPP_DIR. '/modules/repository/validator/Validator_DownloadCheck.class.php';
            $validator = new Repository_Validator_DownloadCheck();
			$initResult = $validator->setComponents($this->Session, $this->Db);
			if($initResult === 'error'){
				return 'error';
			}
            $this->file_download_type = $validator->checkFileDownloadType();
            if($this->file_download_type == "1"){
                return "error";
            }
            // Select 
            $query = "SELECT * ".
                     "FROM ". DATABASE_PREFIX ."repository_file ".
                     "WHERE item_id = ? ".
                     "  AND item_no = ? ".
                     "  AND attribute_id = ? ".
                     "  AND file_no = ? ".
                     "  AND is_delete = 0";
            $params = array();
            $params[] = $this->item_id;
            $params[] = $this->item_no;
            $params[] = $this->attribute_id;
            $params[] = $this->file_no;
            $file_info = $this->Db->execute($query, $params);
            if ($file_info === null) {
                return "error";
            }
            // End when retrieval result is not one
            if (count($file_info) != 1) {
                return "error";
            }
			$fileStatus = $validator->checkDownloadStatus($file_info[0]);
			if($fileStatus == 'close'){
				// cannot download
				$this->file_download_type = 1;
			} else {				
				// 課金判定
				$result = $validator->checkFilePrice($this->item_id, $this->item_no
												   , $this->attribute_id, $this->file_no, 'download');
				if($result == 'close'){
					$this->file_download_type = 1;
				}
			}
            // modify check downloadtype K.Matsuo 2011/10/18 --end--
            
            // Add entry Flash log Y.Nakao 2011/03/01 --start--
            $result = $this->entryLog(6, $this->item_id, $this->item_no, $this->attribute_id, $this->file_no, '');
            // Add entry Flash log Y.Nakao 2011/03/01 --end--

            // exit action
            $result = $this->exitAction();
            if ( $result === false ) {
                $exception = new RepositoryException( "ERR_MSG_xxx-xxx3", 1 );
                throw $exception;
            }
            return 'success';

            
         } catch ( RepositoryException $Exception) {
            //アクション終了処理
            $result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
            // redirect
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".BASE_URL);
            
            return;
        }
    }
}
?>
