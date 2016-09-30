<?php

/**
 * Repository Components Business SendMail
 * メール送信ビジネスクラス
 *
 * @package     WEKO
 */

// --------------------------------------------------------------------
//
// $Id: Sendmail.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Business logic abstract class
 * ビジネスロジック基底クラス
 */
require_once WEBAPP_DIR. '/modules/repository/components/FW/BusinessBase.class.php';
/**
 * Mailer class
 * メーラークラス
 */
require_once WEBAPP_DIR. '/components/mail/Main.class.php';

/**
 * Repository Components Business SendMail
 * メール送信ビジネスクラス
 *
 * @package     WEKO
 * @copyright   (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license     http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access      public
 */
class Repository_Components_Business_Sendmail extends BusinessBase
{
    /**
     * Language Japanese
     * 日本語
     */
    const LANG_JAPANESE = "japanese";
    /**
     * Language English
     * 英語
     */
    const LANG_ENGLISH = "english";
    /**
     * Language Chinese
     * 中国語
     */
    const LANG_CHINESE = "chinese";
    /**
     * Available Languages
     * 使用可能な言語
     *
     * @var array array[$ii]
     */
    private $allowedLang = array();

    /**
     * Set the available language
     * 使用可能な言語を設定する
     *
     */
    public function onInitialize() {
        // NC2のメーラーは日英中にしか対応していない
        $this->allowedLang[] = self::LANG_JAPANESE;
        $this->allowedLang[] = self::LANG_ENGLISH;
        $this->allowedLang[] = self::LANG_CHINESE;
    }

    /**
     * メール送信
     * Send mail
     * 
     * @param string $language    language 言語
     * @param array  $destination mail destination メール送信先
     *                             array[0]["email"|"handle"]
     * @param string $subject     mail subject メール件名
     * @param string $body        mail body メール本文
     * @param array  $attachment  attachment file 添付ファイル
     *                             array[0] file path ファイルパス
     *                                  [1] file name ファイル名（拡張子無し）
     *                                  [2] file name with extension ファイル名（拡張子有り）
     *                                  [3] encode style エンコード形式
     *                                  [4] content type Content-Type
     *                                  [5] binary flag バイナリフラグ
     *                                  [6] Content-Disposition Content-Disposition
     *                                  [7] Content-ID Content-ID
     * @return bool              true/false success/failed 成功/失敗
     * @throws AppException
     */
    public function sendMail($language, $destination, $subject, $body, $attachment=array()) {
        // メーラークラス
        $mailMain = new Mail_Main();
        
        // 送信先
        if(!(isset($destination[0]["email"]) && strlen($destination[0]["email"]) > 0) || 
           !(isset($destination[0]["handle"]) && strlen($destination[0]["handle"]) > 0)
          ) {
            $this->errorLog("", __FILE__, __CLASS__, __LINE__);
            throw new AppException("Mail-To is not set.");
        }
        // 言語情報のバリデート
        // NC2のメーラーは日英中にしか対応していないため、
        // その他の言語で来た場合は「english」で送信する
        if(!in_array($language, $this->allowedLang)) {
            $destination[0]["lang_dirname"] = self::LANG_ENGLISH;
        } else {
            $destination[0]["lang_dirname"] = $language;
        }
        
        $mailMain->setToUsers($destination);
        
        // メール件名
        $mailMain->setSubject($subject);
        
        // メール本文
        $mailMain->setBody($body);
        
        // 添付ファイル
        // ファイルの添付に必要な8つのパラメータが揃っている時のみ添付する(Doc参照)
        if(count($attachment) == 8) {
            $mailMain->_mailer->attachment = array($attachment);
        }
        
        // 送信
        $this->debugLog("Send to: ". $destination[0]["email"].", Subject: ".$subject, __FILE__, __CLASS__, __LINE__);
        $result = $mailMain->send();
        if(!$result) {
            $this->errorLog("[Failed] Send to: ". $destination["email"].", Subject: ".$subject, __FILE__, __CLASS__, __LINE__);
            throw new AppException("[Failed] Send to: ". $destination["email"].", Subject: ".$subject);
        }
        
        return true;
    }
}
?>