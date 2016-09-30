<?php

/**
 * Action class for the e-mail address set to send a canned reports
 * 定型レポートを送付するメールアドレス設定用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Report.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * ZIP file manipulation library
 * ZIPファイル操作ライブラリ
 */
include_once MAPLE_DIR.'/includes/pear/File/Archive.php';
/**
 * Action base class for the WEKO
 * WEKO用アクション基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
/**
 * Common class file download
 * ファイルダウンロード共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryDownload.class.php';

/**
 * Action class for the e-mail address set to send a canned reports
 * 定型レポートを送付するメールアドレス設定用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Edit_Log_Report extends RepositoryAction
{
	// component
    /**
     * Session management objects
     * Session管理オブジェクト
     *
     * @var Session
     */
	var $Session = null;
    /**
     * Database management objects
     * データベース管理オブジェクト
     *
     * @var DbObject
     */
	var $Db = null;
	
	// member
	/**
	 * Mail address
	 * メールアドレス
	 *
	 * @var string
	 */
	var $address = null; 
	
	/**
	 * Set up an e-mail address
	 * メールアドレスを設定
	 *
	 * @return string Result 結果
	 */
	function execute()
	{
		try {
			// -----------------------------------------------
			// init
			// -----------------------------------------------
			// start action
			$result = $this->initAction();
			if ( $result === false ) {
				$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );
				$DetailMsg = null;
				sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
				$exception->setDetailMsg( $DetailMsg );
				$this->failTrans();
				throw $exception;
			}
			
			// -----------------------------------------------
			// get lang resource
			// -----------------------------------------------
			$this->setLangResource();
			$smarty = $this->Session->getParameter("smartyAssign");
			
			// -----------------------------------------------
			// check mail address
			// -----------------------------------------------
			$this->address = str_replace("\r\n", "\n", $this->address);
			$add = array();
			$add = explode("\n", $this->address);
			$this->address = "";
			for($ii=0; $ii<count($add); $ii++){
				if(strlen($add[$ii]) > 0){
					if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $add[$ii])){
						$this->address .= $add[$ii]."\n"; 
					}
				}
			}
			
			// -----------------------------------------------
			// entry report send mail address
			// -----------------------------------------------
			$query = "UPDATE ".DATABASE_PREFIX."repository_parameter ".
					" SET param_value = ?, ".
					" mod_user_id = ? ".
					" WHERE param_name = ?; ";
			$params = array();
			$params[] = $this->address;
			$params[] = $this->Session->getParameter("_user_id");
			$params[] = "log_report_mail";
			$result = $this->Db->execute($query, $params);
			if($result === false){
				echo "";
				exit();
			}
			
			// -----------------------------------------------
			// end action
			// -----------------------------------------------
			$result = $this->exitAction();
			if ( $result == false ){
				echo "";
				exit();
			}
			
			echo $this->address;
			$this->finalize();
			exit();
			
		}
		catch ( RepositoryException $Exception) {
			$this->logFile(
				"SampleAction",
				"execute",
				$Exception->getCode(),
				$Exception->getMessage(),
				$Exception->getDetailMsg() );
			$this->exitAction();
			return "error";
		}
	}
}
?>
