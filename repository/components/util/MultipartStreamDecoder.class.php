<?php

/**
 * Read a common class of the uploaded file in a multi-part
 * マルチパートでアップロードされたファイルの読み込み共通クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: MultipartStreamDecoder.class.php 42605 2015-04-02 01:02:01Z yuya_yamazawa $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics,
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------
/**
 * Stream operation common classes
 * Stream操作共通クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/IO/FileStream.class.php';

/**
 * Exception class
 * 例外基底クラス
 */
require_once WEBAPP_DIR.'/modules/repository/components/FW/AppException.class.php';

/**
 * Read a common class of the uploaded file in a multi-part
 * マルチパートでアップロードされたファイルの読み込み共通クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class Repository_Components_Util_MultipartStreamDecoder
{
    /**
     * Header, footer part of the boundary read maximum byte values
     * ヘッダー,フッター部の境界読み込み最大バイト値
     * @var int
     */
    const READ_BOUNDARY_MAX_SIZE = 1024;

    /**
     * The maximum number of bytes read file contents
     * ファイル内容最大読み込みバイト数
     * @var int
     */
    const READ_FILE_MAX_SIZE = 4096;

    /**
     * To decode the uploaded files in a multi-part
     * 1. Argument checking
     * 2. The first reading of the line of the file. Acquisition of Boundary
     * 3. Decode the file
     * 4. Delete in the case of illegal data
     * マルチパートでアップロードされたファイルをデコードする
     * 1.引数チェック
     * 2.ファイルの最初の行の読み込み。Boundaryの取得
     * 3.ファイルのデコード
     * 4.不正データの場合の削除
     * 
     * @param FileStream $readFileStream FileStream object open the upload data アップロードデータを開いたFileStreamオブジェクト
     * @param string $oututFile Output destination path 出力先パス
     * @return array File name list ファイル名リスト
     */
    public static function decodeMultiPartFile($readFileStream,$oututFile)
    {
        // ファイルの読み込みに失敗していた場合または出力先パスが指定されていなかった場合false
        if($readFileStream === false){
            throw new AppException("There is no streamData");
        }else if(!isset($oututFile)){
            throw new AppException("isset false outputFilePath");
        }

        // streamからboundaryを取得
        $boundary = Repository_Components_Util_MultipartStreamDecoder::readHeadBoundary($readFileStream,$tmp_buffer_data);

        // ファイルをデコードする
        $readContinueFlag = true;
        $deleteFlag = false;
        $fileList = array();
        while ($readContinueFlag){
            $readContinueFlag = Repository_Components_Util_MultipartStreamDecoder::decodePartFile($readFileStream,$oututFile,$boundary,$tmp_buffer_data,$decodedFile,$deleteFlag);
            if(isset($decodedFile)){
                array_push($fileList, $decodedFile);
            }
        }
        $readFileStream->close();

        // 不正データがある場合はファイルをすべて削除
        if($deleteFlag === true)
        {
            $path_parts = pathinfo($oututFile);
            $dir = $path_parts['dirname'];
            foreach ($fileList as $fileName)
            {
                unlink($dir."/".$fileName);
            }
        }

        return $fileList;
    }

    /**
     * Read up to boundary value of the header
     * ヘッダーの境界値までを読み込む
     * 
     * @param FileStream $readFileStream FileStream object open the upload data アップロードデータを開いたFileStreamオブジェクト
     * @param string $tmp_buffer_data Temporary storage buffer 一時保存用バッファ
     * @return string Boundary string バウンダリ文字列
     */
    private static function readHeadBoundary($readFileStream,&$tmp_buffer_data)
    {
        // READ_BOUNDARY_MAX_SIZE分読み込む
        $readStreamData = $readFileStream->read(self::READ_BOUNDARY_MAX_SIZE);

        // READ_BOUNDARY_MAX_SIZE分読み込んだデータから\r\nの開始位置を取得
        $result = preg_match("/--[0-9]+\\r\\n/", $readStreamData,$match);
        if($result == 0){
            throw new AppException("There is no Boundary");
        }

        // \r\nで分割
        $partData = explode("\r\n", $readStreamData, 2);

        // \r\nの後のデータをすべてメンバのbuffer(一時保持用)に移動
        $tmp_buffer_data = $partData[1];

        // Boundaryを返す
        return $partData[0];
    }

    /**
     * Reads the header part
     * ヘッダー部分を読み込む
     * 
     * @param FileStream $readFileStream FileStream object open the upload data アップロードデータを開いたFileStreamオブジェクト
     * @param string $oututFilePath Output destination path 出力先パス
     * @param string $tmp_buffer_data Temporary storage buffer 一時保存用バッファ
     * @param string $fileName File name ファイル名
     */
    private static function readHeader($readFileStream,$oututFilePath,&$tmp_buffer_data,&$fileName)
    {
        while (!$readFileStream->eof())
        {
            // READ_BOUNDARY_MAX_SIZE分読み込む
            $readStreamData = $readFileStream->read(self::READ_FILE_MAX_SIZE);

            // メンバのbufferと結合
            $joinData = $tmp_buffer_data.$readStreamData;

            // \r\n\r\nがあるか確認
            $boundaryStartPoint = strpos($joinData, "\r\n\r\n");
            if($boundaryStartPoint === false){
                // 読み込んだデータをbuffer(一時保持用)に保持
                $tmp_buffer_data = $joinData;

                // buffer(一時保持用)が4Kを超えた場合異常なデータとして例外を投げる
                $size = strlen($tmp_buffer_data);
                if($size > Repository_Components_Util_MultipartStreamDecoder::READ_FILE_MAX_SIZE)
                {
                    throw new AppException("There is no Header Info");
                }
            }
            else{
                // ヘッダー部分後のデータをメンバー(buffer(一時保持用))にセットする
                Repository_Components_Util_MultipartStreamDecoder::keepstreamDataAfterHeader($joinData, $oututFilePath,$tmp_buffer_data,$fileName);

                return;
            }
        }

        // \r\n\r\nがあるか確認
        $headerEndPoint = strpos($tmp_buffer_data, "\r\n\r\n");
        if($headerEndPoint === false){
            throw new AppException("There is no Header Info");
        }

        // ヘッダー部分後のデータをメンバー(buffer(一時保持用))にセットする
        Repository_Components_Util_MultipartStreamDecoder::keepstreamDataAfterHeader($tmp_buffer_data, $oututFilePath,$tmp_buffer_data,$fileName);
    }

    /**
     * To retain the data after the header part in a buffer (for temporary holding)
     * ヘッダー部分後のデータをバッファ(一時保持用)に保持する
     * 
     * @param string $readStreamData Read elaborate string 読込んだ文字列
     * @param string $oututFilePath Output destination path 出力先パス
     * @param string $tmp_buffer_data Temporary storage buffer 一時保存用バッファ
     * @param string $fileName File name ファイル名
     */
    private static function keepstreamDataAfterHeader($readStreamData,$oututFilePath,&$tmp_buffer_data,&$fileName)
    {
        // \r\n\r\nを区切りとしてデータを分ける
        $partData = explode("\r\n\r\n", $readStreamData, 2);

        // \r\nの後のデータをすべてメンバのbuffer(一時保持用)に移動
        $tmp_buffer_data = $partData[1];

        // ヘッダーからファイル名を取得
        $result = preg_match('/filename="([^"]+)"/', $partData[0],$match);
        if($result === false || $result == 0){
            throw new AppException("There is no Header Info");
        }
        // 出力先パスがディレクトリ名の時はヘッダー情報のファイル名をリストに詰める
        if(is_dir($oututFilePath))
        {
            $fileName = $match[1];
        }
        else{
            // 引数の出力先パスのファイル名をリストに詰める
            $path_parts = pathinfo($oututFilePath);
            $fileName = $path_parts["basename"];
        }
    }

    /**
     * Read the uploaded files, and outputs it to the specified path
     * アップロードされたファイルを読み込み、指定されたパスに出力する
     * 
     * @param FileStream $readFileStream FileStream object open the upload data アップロードデータを開いたFileStreamオブジェクト
     * @param string $oututFilePath Output destination path 出力先パス
     * @param string $boundary Boundary string バウンダリ文字列
     * @param string $tmp_buffer_data Temporary storage buffer 一時保存用バッファ
     * @param string $decodedFile File name that you want to decode デコードするファイル名
     * @param boolean $deleteFlag Delete flag of the decoded file デコードしたファイルの削除フラグ
     * @return boolean Decode resume flag in the file ファイルのデコード再開フラグ
     */
    private static function decodePartFile($readFileStream,$oututFilePath,$boundary,&$tmp_buffer_data,&$decodedFile,&$deleteFlag)
    {
        // ヘッダーの読み込み
        Repository_Components_Util_MultipartStreamDecoder::readHeader($readFileStream,$oututFilePath,$tmp_buffer_data,$decodedFile);

        // ファイルの出力先パスの取得
        $outputPath = Repository_Components_Util_MultipartStreamDecoder::outputFilePath($oututFilePath,$decodedFile);

        $outputFileStream = FileStream::open($outputPath, "w");

        // ファイルのデコード
        $result = Repository_Components_Util_MultipartStreamDecoder::decodeFile($readFileStream,$outputFileStream,$boundary,$tmp_buffer_data,$deleteFlag);

        $outputFileStream->close();

        return $result;
    }

    /**
     * Acquisition of the output destination path
     * 出力先パスの取得
     * 
     * @param $path Output destination path 出力先パス
     * @param $decodedFile File name ファイル名
     * @return string Output destination path 出力先パス
     */
    private static function outputFilePath($path,&$decodedFile)
    {
        $outputPath = $path;
        // 引数で渡されたパスがディレクトリパスならファイル名をくっつける
        if(is_dir($path))
        {
            $lastStr = mb_substr($path,-1);
            if($lastStr != "/"){
                $outputPath = $path."/".$decodedFile;
            }
            else{
                $outputPath = $path.$decodedFile;
            }
        }
        else
        {
            // ファイルが既に存在している場合はファイル名を入れ替える
            $path_parts = pathinfo($path);
            if(file_exists($path)){
                $dir = $path_parts['dirname'];
                $outputPath = $dir."/".$decodedFile;
            }
            else{
                $decodedFile = $path_parts['basename'];
            }
        }

        return $outputPath;
    }

    /**
     * Read the uploaded file data, to restore the original file data
     * アップロードされたファイルデータを読み込み、元のファイルデータを復元する
     * 
     * @param FileStream $readFileStream FileStream object open the upload data アップロードデータを開いたFileStreamオブジェクト
     * @param FileStream $outputFileStream FileStream object of the output destination 出力先のFileStreamオブジェクト
     * @param string $boundary Boundary string バウンダリ文字列
     * @param string $tmp_buffer_data Temporary storage buffer 一時保存用バッファ
     * @param boolean $deleteFlag Delete flag of the decoded file デコードしたファイルの削除フラグ
     * @return boolean Decode incomplete flag (false If you do not true in the case of performing again) デコード未了フラグ(再度行う場合はtrue 行わない場合はfalse)
     */
    private static function decodeFile($readFileStream,$outputFileStream,$boundary,&$tmp_buffer_data,&$deleteFlag)
    {
        // StreamDataを読み込んだ際、StreamデータのBoudanryサイズ分は信用できないためBoundaryサイズ分保持する
        $tmpStreamData = "";

        while (!$readFileStream->eof())
        {
            // READ_FILE_MAX_SIZE分読み込む
            $readStreamData = $readFileStream->read(self::READ_FILE_MAX_SIZE);

            // 読み込んだデータとbuffer(一時保持用),Boundaryサイズ分の保持データを結合する
            $joinData = $tmp_buffer_data.$tmpStreamData.$readStreamData;

            // Boundaryが存在するか確認
            $result = Repository_Components_Util_MultipartStreamDecoder::existBoundary($joinData,$outputFileStream,$boundary,$tmp_buffer_data,$tmpStreamData,$partDataAfterBoundary);
            if($result === false){
                // 再度読み込みを行う
                continue;
            }

            if(isset($partDataAfterBoundary)){
                // Boundary後のデータをbuffer(一時保持用)に保持する
                $tmp_buffer_data = $partDataAfterBoundary;

                // 読み込みを続ける
                return true;
            }
            else{
                // 読み込みを続けない
                return false;
            }
        }

        // EOFの場合に残っていたデータを出力する
        $result = Repository_Components_Util_MultipartStreamDecoder::outputFileRestData($outputFileStream,$boundary,$tmp_buffer_data);
        if($result === false){
            // 不正データの場合は削除を行うようにする
            $deleteFlag = true;
        }
        else{
            $deleteFlag = false;
        }

        // 読み込みを続けない
        return false;
    }

    /**
     * Whether or not the boundary string is present
     * バウンダリ文字列が存在するか否か
     * 
     * @param string $streamReadData Read data from Stream Streamから読み込んだデータ
     * @param FileStream $outputFileStream The output destination of the file pointer 出力先のファイルポインタ
     * @param string $boundary Boundary string バウンダリ文字列
     * @param string $tmp_buffer_data Temporary storage buffer 一時保存用バッファ
     * @param string $tmpStreamData Data to the end of the string from the data was cut Boudary size of read from Stream Streamから読み込んだデータから最後の文字列をバウンダリサイズ分切り取ったデータ
     * @param string $partDataAfterBoundary Data that exist after the boundary string バウンダリ文字列後に存在するデータ
     * @return boolean Boundary existence (true: boundary exists, false: boundary does not exist) バウンダリ存在有無(true:バウンダリが存在,false:バウンダリが存在しない)
     */
    private static function existBoundary($streamReadData,$outputFileStream,$boundary,&$tmp_buffer_data,&$tmpStreamData,&$partDataAfterBoundary)
    {
        // \r\n後のデータにboundaryがあるか確認
        $boundaryPoint = strpos($streamReadData, $boundary);
        if($boundaryPoint === false){
            $boundaryByteSize = strlen($boundary."\r\n");
            $streamReadDataSize = strlen($streamReadData);
            $outputReadDataSize = $streamReadDataSize - $boundaryByteSize;

            // Boundaryサイズ分後ろのデータを切り出しておく
            $tmpStreamData = substr($streamReadData, -$boundaryByteSize);

            // 確定データを出力
            $outputData = substr($streamReadData, 0 , $outputReadDataSize);

            // データを出力する
            $outputFileStream->write($outputData);

            // buffer(一時保持用)を空にする
            $tmp_buffer_data = "";

            // 再度読み込みを行う
            return false;
        }

        // Boundaryを区切りとしてデータを分ける
        $partData = explode($boundary, $streamReadData, 2);

        // Boudaryの後に文字列が続いている場合は次のデータ情報
        if(!isset($partData[1])){
            $partDataAfterBoundary = $partData[1];
        }

        // データを出力する
        $outputFileStream->write($partData[0]);

        return true;
    }

    /**
     * EOFの場合に残っていたデータを出力する
     * @param FileStream $outputFileStream 出力先のファイルポインタ
     * @param string $boundary Boundary値
     * @param string $tmp_buffer_data 一時保存用buffer
     * @throws AppException
     * @return boolean データの出力成功はtrue 失敗の場合はfalse
     */
    private static function outputFileRestData($outputFileStream,$boundary,&$tmp_buffer_data)
    {
        $boundarybeforePoint = strpos($tmp_buffer_data, "\r\n");
        if($boundarybeforePoint === false){
            throw new AppException("There is no Boundary");
        }

        // \r\nを区切りとしてデータを分ける
        $partData = explode("\r\n", $tmp_buffer_data, 2);

        // \r\n前のデータを出力する
        $outputFileStream->write($partData[0]);

        // \r\n後のデータにboundaryがあるか確認
        $boundaryPoint = strpos($partData[1], $boundary);
        if($boundaryPoint === false){
            return false;
        }

        return true;
    }
}
?>