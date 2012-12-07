<?php

/**
 * Stream class
 *
 * Luki framework
 * Date 7.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

/**
 * Stream class
 *
 * @package Luki
 */
class Luki_Stream {

	private static $aMimeTypes = array(
		'ai' => 'application/postscript',
		'aif' => 'audio/aiff',
		'aiff' => 'audio/aiff',
		'avi' => 'video/msvideo',
		'bmp' => 'image/bmp',
		'cab' => 'application/vnd.ms-cab-compressed',
		'css' => 'text/css',
		'csv' => 'text/csv',
		'doc' => 'application/msword',
		'docx' => 'application/msword',
		'eps' => 'application/postscript',
		'exe' => 'application/x-msdownload',
		'flv' => 'video/x-flv',
		'gif' => 'image/gif',
		'htm' => 'text/html',
		'html' => 'text/html',
		'ico' => 'image/vnd.microsoft.icon',
		'jpe' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'js' => 'application/x-javascript',
		'json' => 'application/json',
		'mov' => 'video/quicktime',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mp3' => 'audio/mpeg3',
		'msi' => 'application/x-msdownload',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'pdf' => 'application/pdf',
		'php' => 'text/x-php',
		'png' => 'image/png',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppt' => 'application/vnd.ms-powerpoint',
		'ps' => 'application/postscript',
		'psd' => 'image/vnd.adobe.photoshop',
		'qt' => 'video/quicktime',
		'rar' => 'application/x-rar-compressed',
		'rtf' => 'application/rtf',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'swf' => 'application/x-shockwave-flash',
		'tar' => 'application/x-tar',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'txt' => 'text/plain',
		'wav' => 'audio/wav',
		'wmv' => 'video/x-ms-wmv',
		'xml' => 'application/xml',
		'xla' => 'application/vnd.ms-excel',
		'xlc' => 'application/vnd.ms-excel',
		'xld' => 'application/vnd.ms-excel',
		'xll' => 'application/vnd.ms-excel',
		'xlm' => 'application/vnd.ms-excel',
		'xls' => 'application/vnd.ms-excel',
		'xlt' => 'application/vnd.ms-excel',
		'xlw' => 'application/vnd.ms-excel',
		'zip' => 'application/zip'
	);

	public static function streamFile($sFile = NULL)
	{
		if(!empty($sFile) and is_file($sFile)) {
			$sMimeType = Luki_File::getMimeType($sFile);
			$sContent = file_get_contents($sFile);
			$sFileName = basename($sFile);
		}
		else {
			$sMimeType = 'text/plain';
			$sContent = 'Wrong file!';
			$sFileName = 'wrong_file.txt';
		}

		self::_streamToOutput($sMimeType, $sContent, $sFileName);
	}

	public static function streamData($sContent, $sFileName)
	{
		$sMimeType = self::_getMimeType($sFileName);

		self::_streamToOutput($sMimeType, $sContent, $sFileName);
	}

	private static function _streamToOutput($sMimeType, $sContent, $sFileName)
	{
		while (ob_get_level()) {
			ob_end_clean();
		}
		ob_start();

		header('Content-type: ' . $sMimeType);
		if(!headers_sent()) {
			header('Content-Disposition: attachment; filename="' . $sFileName . '"');
			header('Content-Length: ' . strlen($sContent));
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header('Pragma: public');

			echo $sContent;
		}

		ob_end_flush();
		exit;
	}

	/**
	 * Get MimeType for any extension
	 *
	 * @param unknown_type $sFile
	 * @return string
	 */
	private static function _getMimeType($sFileName)
	{
		$sReturn = '';
		preg_match("|\.([a-z0-9]{2,4})$|i", $sFileName, $afileSuffix);

		if(!empty(self::$aMimeTypes[$afileSuffix[1]])) {
			$sReturn = self::$aMimeTypes[$afileSuffix[1]];
		}
		else {
			$sReturn = "unknown/" . trim($afileSuffix[0], ".");
		}

		unset($sFileName, $afileSuffix);
		return $sReturn;
	}

}

# End of file