<?php

/**
 * Name authority import confirmation action class
 * 著者名典拠インポート確認アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Confirm.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
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
 */
require_once WEBAPP_DIR. '/modules/repository/action/edit/importauthority/Importauthority.class.php';


/**
 * Name authority import confirmation action class
 * 著者名典拠インポート確認アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Importauthority_Confirm extends RepositoryAction
{
    /**
     * execute insert authority into name_authority table
     * 著者名典拠テーブルへのインサート
     */
    function executeApp()
    {
        // error msg remove
        $this->Session->removeParameter("error_msg");
        $this->Session->removeParameter("importmode");
        
        // upload tsv file to tmp folder
        $tmp_file = $this->getFilePathAuthorTsv();
        if($tmp_file == false){
            // not tsv error
            $this->Session->setParameter("error_msg", "Import file is not the 'TSV' format.");
            return 'error';
        }
        
        
        ////////////////////////////////////////
        // insert author
        ////////////////////////////////////////
        $err_msg_array = array();
        $errIndex = 0;
        $successNum = 0;
        $importAuthority = new Repository_Action_Edit_Importauthority($this->Session, $this->Db);
        $fileData = $importAuthority->readFile($tmp_file);
        if($fileData == false){
            // not tsv error
            $this->Session->setParameter("error_msg", "Cannot read file.");
            return 'error';
        }
        $authorNum = count($fileData) - 1;
        if($authorNum < 1){
            // not tsv error
            $this->Session->setParameter("error_msg", "Nothing author data.");
            return 'error';
        }
        // divide tsv data by tab
        $dividedDataArray = array();
        $importAuthority->divideTsvToArray($fileData, $dividedDataArray);
        // create metadata for name authority
        $metadataForNameAuthority = array();
        $importAuthority->createMetadataForNameAuthority($dividedDataArray, $metadataForNameAuthority);
        // insert Name Authority
        $nameAuthority = new NameAuthority($this->Session, $this->Db );
        for ($nCnt = 0; $nCnt < $authorNum; $nCnt++)
        {
            if(strlen($metadataForNameAuthority[$nCnt][Repository_Action_Edit_Importauthority::FAMILY]) < 1)
            {
                // set error message
                $err_msg_array[$errIndex]['error_msg'] = "No family data on line ". ($nCnt+2);
                $err_msg_array[$errIndex]['status'] = "error";
                $err_msg_array[$errIndex]['mode'] = "INSERT";
                $errIndex++;
                continue;
            }
            // entry name authority
            $result = $nameAuthority->entryNameAuthority($metadataForNameAuthority[$nCnt], $errMsg);
            if($result === false)
            {
                // set error message
                $err_msg_array[$errIndex]['error_msg'] = $errMsg. " on line ". ($nCnt+2);
                $err_msg_array[$errIndex]['status'] = "error";
                $err_msg_array[$errIndex]['mode'] = "INSERT";
                $errIndex++;
                continue;
            }
            $successNum++;
        }
        
        // set message
        $this->Session->setParameter("items", $err_msg_array);
        $this->Session->setParameter("successnum", $successNum);
        $this->Session->setParameter("importmode", "nameauthority");
        // delete work file
        unlink($tmp_file);
        
        return 'success';
    }

    /**
     * get file path
     * ファイルパス取得
     */
    function getFilePathAuthorTsv(){
    
        // get upload file
        $tmp_file = $this->Session->getParameter("filelist");
        
        $dir_path = WEBAPP_DIR. "/uploads/repository/";
        $file_path = $dir_path . $tmp_file[1]['physical_file_name'];
        
        if($tmp_file[1]['extension'] != "tsv"){
            unlink($file_path);
            return false;
        }
        
        return $file_path;
    }
}
?>
