<?php
// --------------------------------------------------------------------
//
// $Id: Mappingconfirm.class.php 516 2012-05-22 09:30:55Z ivis $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// Set help icon setting 2010/02/10 K.Ando --start--
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryAction.class.php';
// Set help icon setting 2010/02/10 K.Ando --end--

/**
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
class Repository_View_Edit_Itemtype_Mappingconfirm extends RepositoryAction 
{
	// 使用コンポーネントを受け取るため
	var $Session = null;
	var $Db = null;
	// メタデータ表示用メンバ
	
    // Set help icon setting 2010/02/10 K.Ando --start--
    var $help_icon_display =  null;
    // Set help icon setting 2010/02/10 K.Ando --end--
	
	/**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
        
        // Add theme_name for image file Y.Nakao 2011/08/03 --start--
        $this->setThemeName();
        // Add theme_name for image file Y.Nakao 2011/08/03 --end--
        
        // Set help icon setting 2010/02/10 K.Ando --start--
        $result = $this->getAdminParam('help_icon_display', $this->help_icon_display, $Error_Msg);
		if ( $result == false ){
			$exception = new RepositoryException( ERR_MSG_xxx-xxx1, xxx-xxx1 );	//主メッセージとログIDを指定して例外を作成
            $DetailMsg = null;                              //詳細メッセージ文字列作成
            sprintf( $DetailMsg, ERR_DETAIL_xxx-xxx1);
            $exception->setDetailMsg( $DetailMsg );         //詳細メッセージ設定
            $this->failTrans();                             //トランザクション失敗を設定(ROLLBACK)
            throw $exception;
		}
        // Set help icon setting 2010/02/10 K.Ando --end--
    	
		return 'success';
    }
}
?>
