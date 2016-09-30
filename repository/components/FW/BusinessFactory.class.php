<?php

/**
 * Business factory abstract class
 * ビジネスファクトリー基底クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: BusinessFactory.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Business factory class
 * ビジネスファクトリークラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class BusinessFactory
{
    /**
     * Logger object
     * ロガーオブジェクト
     *
     * @var object
     */
    public $logger = null;
    /**
     * Session
     * セッション
     *
     * @var Session
     */
    protected $Session = null;
    /**
     * DB object
     * DBオブジェクト
     *
     * @var object
     */
    protected $Db = null;
    /**
     * Execute start date
     * 処理開始時間
     *
     * @var string
     */
    protected $accessDate = null;
    /**
     * Object list
     * オブジェクト配列
     *
     * @var array
     */
    protected $instanceList = array();
    /**
     * Factory object
     * ファクトリークラスオブジェクト
     *
     * @var BusinessFactory
     */
    protected static $instance = null;

    /**
     * BusinessFactory constructor.
     * コンストラクタ
     *
     * @param Session $_session session セッション
     * @param object $_db DB object DBオブジェクト
     * @param string $_accessDate execute start date 処理開始時間
     */
    protected function __construct($_session, $_db, $_accessDate)
    {
        $this->Session = $_session;
        $this->Db = $_db;
        $this->accessDate = $_accessDate;
    }
    
    /**
     * get factory object
     * ファクトリーオブジェクト取得
     * 
     * @return BusinessFactory business factory ファクトリーオブジェクト
     */
    public static function getFactory()
    {
        return self::$instance;
    }

    /**
     * End process
     * 終了処理
     */
    public function uninitialize()
    {
        for($ii = 0; $ii < count($this->instanceList); $ii++) {
            $this->instanceList[$ii]->finalizeBusiness();
        }

        $this->instanceList = null;
        self::$instance = null;
    }

    /**
     * Get business object
     * ビジネスオブジェクト取得
     *
     * @param string $businessName business class name ビジネスクラス名
     */
     protected function getBusiness($businessName) {}
}
?>
