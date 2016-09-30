<?php

/**
 * Add in caller class
 * アドイン呼び出しクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryAddinCaller.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Add in caller class
 * アドイン呼び出しクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class RepositoryAddinCaller extends Action
{
    /**
     * Add in file name last prefix
     * アドインファイルの後方プレフィックス文字列
     *
     */
    const ADDIN_FILENAME_LAST = '_Addin';
    
    /**
     * invoker class (WEKO action class) instance.
     * 呼び出し元クラス
     * 
     * @var RepositoryAction
     */
    private $invoker_ = null;
    
    /**
     * addin class instance.
     * アドインオブジェクト
     *
     * @var RepositoryAddinBaseAbstract
     */
    private $addin_ = null;
    
    /**
     * constructor
     * コンストラクタ
     * 
     * @param RepositoryAction $wekoAction caller class 呼び出し元クラス
     */
    public function RepositoryAddinCaller($wekoAction)
    {
        if($wekoAction != null)
        {
            $this->invoker_ = $wekoAction;
            $className = get_class($wekoAction) . RepositoryAddinCaller::ADDIN_FILENAME_LAST;
            $classPath = WEBAPP_DIR. '/modules/repository/files/addin/' . $className . '.class.php';
            
            if(file_exists($classPath))
            {
                require_once $classPath;
                $this->addin_ = new $className();
                $this->addin_->setInvoker($this->invoker_);
            }
        }
    }
    
    /**
     * pre execute
     * 実行前処理
     */
    public function preExecute()
    {
        if(isset($this->addin_))
        {
            $this->addin_->preExecute();
        }
    }
    
    /**
     * post execute
     * 実行後処理
     */
    public function postExecute()
    {
        if(isset($this->addin_))
        {
            $this->addin_->postExecute();
        }
    }
    
}
?>
