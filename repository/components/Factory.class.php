<?php

/**
 * Common classes for factory
 * ファクトリー用共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Factory.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Const for WEKO class
 * WEKO用定数クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryConst.class.php';
/**
 * Pear date library
 * PEAR日付ライブラリ
 */
include_once WEBAPP_DIR. '/modules/repository/files/pear/Date.php';
/**
 * SCfW manager class
 * SCfW管理クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/Swordmanager.class.php';
/**
 * Export common class
 * エクスポート汎用クラス
 */
require_once WEBAPP_DIR. '/modules/repository/action/main/export/ExportCommon.class.php';
/**
 * Output TSV class
 * TSV出力クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/RepositoryOutputTSV.class.php';

/**
 * Common classes for factory
 * ファクトリー用共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Factory
{
    
    /**
     * get component
     * コンポーネント取得
     *
     * @param Session $Session Session object セッションオブジェクト
     * @param DbObjectAdodb $db DB object DBオブジェクト
     * @param string $TransStartDate process start date 処理開始時間
     * @return object components コンポーネント
     */
    public static function getComponent($entryName)
    {
        $instance = null;
        
        $container =& DIContainerFactory::getContainer();
        $Db =& $container->getComponent("DbObject");
        $Session =& $container->getComponent("Session");
        $DATE = new Date();
        $TransStartDate = $DATE->getDate().".000";
        
        switch($entryName)
        {
            case 'Repository_Components_Swordmanager':
                $instance = new Repository_Components_Swordmanager($Session, $Db, $TransStartDate);
                break;
            case 'ExportCommon':
                $instance = new ExportCommon($Db, $Session, $TransStartDate);
                break;
            case 'RepositoryOutputTSV':
                $instance = new RepositoryOutputTSV($Db, $Session);
                break;
            case 'Repository_Components_Loganalyzor':
                require_once WEBAPP_DIR. '/modules/repository/components/LogAnalyzor.class.php';
                $instance = new Repository_Components_Loganalyzor($Session, $Db, $TransStartDate);
                break;
            default:
                break;
        }
        return $instance;
    }
}
?>
