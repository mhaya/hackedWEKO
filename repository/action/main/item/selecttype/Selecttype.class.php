<?php
// --------------------------------------------------------------------
//
// $Id: Selecttype.class.php 270 2009-01-21 04:50:41Z ivis $
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
 * [[機能説明]]
 *
 * @package     [[package名]]
 * @access      public
 */
class Repository_Action_Main_Item_Selecttype
{
	// 使用コンポーネントを受け取るため
	var $session = null;
	
    /**
     * [[機能説明]]
     *
     * @access  public
     */
    function execute()
    {
    	//セッションの初期化
    	$this->session->removeParameter("item_type_all");
    	$this->session->removeParameter("item_attr_type");
    	$this->session->removeParameter("item_num_cand");
    	$this->session->removeParameter("option_data");
    	$this->session->removeParameter("item_num_attr");
    	$this->session->removeParameter("item_attr");
    	$this->session->removeParameter("base_attr");
    	$this->session->removeParameter("error_msg");  	
    	
    	$this->Session->removeParameter("all_group"); // 2008/08/12
    	$this->Session->removeParameter("user_group"); // 2008/08/12
    	
    	// change index tree 2008/12/03 Y.Nakao --start--
    	$this->Session->removeParameter("view_open_node_index_id_insert_item");
    	$this->Session->removeParameter("view_open_node_index_id_item_link");
    	// change index tree 2008/12/03 Y.Nakao --end--
    	
        return 'success';
    }
}
?>