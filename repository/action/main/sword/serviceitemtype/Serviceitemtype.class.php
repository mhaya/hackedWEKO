<?php

/**
 * Action class for SCfW filter update
 * SCfWフィルタ更新用アクションクラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id:Servicedocument.class.php 4173 2008-10-31 08:35:00Z nakao $
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
 * Common classes for factory
 * ファクトリー用共通クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/Factory.class.php';

/**
 * Action class for SCfW filter update
 * SCfWフィルタ更新用アクションクラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Action_Main_Sword_Serviceitemtype extends RepositoryAction
{
    /**
     * The item type information registered in the WEKO output in XML format, and updates the filter with SCfW side
     * WEKOに登録されているアイテムタイプ情報をXML形式で出力し、SCfW側でフィルターを更新する
     */
    function executeApp()
    {
        $swordManager = Repository_Components_Factory::getComponent('Repository_Components_Swordmanager');
        
        // XML文字列作成
        $xml = "";
        $swordManager->createItemtypeXml($xml);
        
        // 出力
        header("Content-Type: text/xml; charset=utf-8");
        echo $xml;
        
        //アクション終了処理
        $result = $this->exitAction();     //トランザクションが成功していればCOMMITされる
        
        // 終了
        exit();
    }
    
}

?>
