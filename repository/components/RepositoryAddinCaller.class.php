<?php
// --------------------------------------------------------------------
//
// $Id: RepositoryAddinCaller.class.php 535 2012-12-07 09:26:24Z ivis $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Addin relay class.
 * 
 * @package     WEKO
 * @access      public
 */
class RepositoryAddinCaller extends Action
{
    /**
     * Enter description here...
     *
     */
    const ADDIN_FILENAME_LAST = '_Addin';
    
    /**
     * invoker class (WEKO action class) instance.
     * 
     * @var Object or RepositoryAction
     */
    private $invoker_ = null;
    
    /**
     * addin class instance.
     *
     * @var RepositoryAddinBaseAbsract
     */
    private $addin_ = null;
    
    /**
     * default constructor
     * 
     * set invoker_, addin_
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
     * Enter description here...
     *
     */
    public function preExecute()
    {
        if(isset($this->addin_))
        {
            $this->addin_->preExecute();
        }
    }
    
    /**
     * Enter description here...
     *
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