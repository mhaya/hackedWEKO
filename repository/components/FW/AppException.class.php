<?php

/**
 * Exception class
 * 例外基底クラス
 *
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: AppException.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Exception abstract class
 * 例外基底クラス
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
abstract class App_Exception_PreviousNativeAbstract extends Exception
{
    /**
     * Exception recursive print flag
     * 例外再帰表示フラグ
     *
     * @var bool
     */
    public static $printPrevious = true;

    /**
     * Message to string
     * メッセージ文字列化
     *
     * @return string error message エラーメッセージ
     */
    public function __toString() {
        $result   = array();
        $result[] = sprintf("Exception '%s' with message '(%s) %s' in %s:%d", get_class($this), $this->code, $this->message, $this->file, $this->line);
        $result[] = '---[Stack trace]:';
        $result[] = $this->getTraceAsString();
        
        if (self::$printPrevious) {
            $previous = $this->getPrevious();
            if ($previous) {
                do {
                    $result[] = '---[Previous exception]:';
                    $result[] = sprintf("Exception '%s' with message '(%s) %s' in %s:%d", get_class($previous), $previous->getCode(), $previous->getMessage(), $previous->getFile(), $previous->getLine());
                    $result[] = '---[Stack trace]:';
                    $result[] = $previous->getTraceAsString();
                } while(method_exists($previous, 'getPrevious') && ($previous = $previous->getPrevious()));
            }
        }
        
        return implode("\r\n", $result);
    }
}

if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
    /**
     * Exception abstract class (PHP version 5.3.0 or more)
     * 例外基底クラス(PHP 5.3.0)
     *
     * @package WEKO
     * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
     * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
     * @access public
     */
    abstract class App_Exception_PreviousAbstract extends App_Exception_PreviousNativeAbstract {}
}
else {
    /**
     * Exception abstract class (PHP version less than 5.3.0 )
     * 例外基底クラス(PHP 5.3.0未満)
     *
     * @package WEKO
     * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
     * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
     * @access public
     */
    abstract class App_Exception_PreviousLegacyAbstract extends App_Exception_PreviousNativeAbstract {
        /**
         * Previous exception object
         * 直前の例外オブジェクト
         *
         * @var Exception
         */
        protected $previous;

        /**
         * App_Exception_PreviousLegacyAbstract constructor.
         * コンストラクタ
         *
         * @param string    $message  error message             エラーメッセージ
         * @param int       $code     error code                エラーコード
         * @param Exception $previous previous exception object 直前の例外オブジェクト
         */
        public function __construct($message, $code = 0, Exception $previous = null) {
            $this->previous = $previous;
            
            parent::__construct($message, $code);
        }

        /**
         * Get previous exception object
         * 直前の例外オブジェクト取得
         * @return Exception
         */
        public function getPrevious() {
            return $this->previous;
        }
    }

    /**
     * WEKO Exception abstract class
     * WEKO例外基底クラス
     *
     * @package WEKO
     * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
     * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
     * @access public
     */
    abstract class App_Exception_PreviousAbstract extends App_Exception_PreviousLegacyAbstract {}
}

/**
 * Exception class
 * 拡張例外クラス(PHP 5.3.0 未満でもインナーエクセプションを扱えるよう対応)
 *
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class AppException extends App_Exception_PreviousAbstract
{
    /**
     * error message list
     * エラーメッセージ一覧
     *
     * @var array array[$ii][$errorKey] エラーメッセージ配列
     */
    private $errors =array();

    /**
     * AppException constructor.
     * 例外を再定義し、メッセージをオプションではなくする
     *
     * @param string $message exception message 例外メッセージ
     * @param int $code error code エラーコード
     * @param Exception inner exception インナーエクセプションクラス
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        // 全てを正しく確実に代入する
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Add error
     * エラー追加
     *
     * @param string $errorKey error key 追加するエラーキー
     * @param array $errorParams errors エラー内容
     */
    public function addError($errorKey, $errorParams=array())
    {
        array_push($this->errors, array());
        $this->errors[count($this->errors)-1][$errorKey] = $errorParams;
    }
    
    /**
     * Check exists error key
     * エラーキーの存在チェック
     * 
     * @param string $key error key 検索するエラーキー
     * @return bool true/false 存在する/存在しない
     */
    public function existsError($key)
    {
        for($ii=0; $ii<count($this->errors); $ii++)
        {
            if(array_key_exists($key, $this->errors[$ii]))
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Error message list
     * エラーメッセージ一覧
     * 
     * @return array array[$ii][$errorKey] エラーメッセージ配列
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
?>