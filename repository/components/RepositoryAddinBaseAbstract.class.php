<?php

/**
 * Add in abstract class
 * アドイン基底クラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: RepositoryAddinBaseAbstract.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Add in abstract class
 * アドイン基底クラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class RepositoryAddinBaseAbstract
{
    
    /**
     * invoker class
     * 呼び出し元クラス
     *
     * @var object
     */
    protected $invoker = null;
    
    /**
     * Set invoker class
     * 呼び出し元クラスを設定する
     *
     * @param object $value caller class 呼び出し元クラス
     */
    public function setInvoker($value)
    {
        // invoker_ setting once.
        if(isset($value) && !isset($this->invoker))
        {
            // set invoker_
            $this->invoker = $value;
            
            // check Session components.
            if(!isset($this->invoker->Session))
            {
                // When nothing Session, setting Session components.
                $container =& DIContainerFactory::getContainer();
                $this->invoker->Session =& $container->getComponent("Session");
            }
            
            if(!isset($this->invoker->Db))
            {
                // When nothing Db, setting Db components.
                $container =& DIContainerFactory::getContainer();
                $this->invoker->Db =& $container->getComponent("DbObject");
            }
        }
    }
    
    /**
     * pre Execute
     * 実行前処理
     */
    public function preExecute()
    {
        
    }
    
    /**
     * post Execute
     * 実行後処理
     */
    public function postExecute()
    {
        
    }
    
}
?>
