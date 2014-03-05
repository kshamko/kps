<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category VaselinEngine
 * @package Bel Classes
 */

/**
 * Image manupulation class. Using GD library
 * This class was taken from Mageno (http://www.magentocommerce.com/) and a little bit modified. * 
 */
class Kps_File_Image {
	protected $_requiredExtensions = Array ("gd" );
	
	public $fileName = null;
	public $imageBackgroundColor = 0;
	
	const POSITION_TOP_LEFT = 'top-left';
	const POSITION_TOP_RIGHT = 'top-right';
	const POSITION_BOTTOM_LEFT = 'bottom-left';
	const POSITION_BOTTOM_RIGHT = 'bottom-right';
	const POSITION_STRETCH = 'stretch';
	const POSITION_TILE = 'tile';
	
	protected $_fileType = null;
	protected $_fileMimeType = null;
	protected $_fileSrcName = null;
	protected $_fileSrcPath = null;
	protected $_imageHandler = null;
	protected $_imageSrcWidth = null;
	protected $_imageSrcHeight = null;
	protected $_watermarkPosition = null;
	protected $_watermarkWidth = null;
	protected $_watermarkHeigth = null;
	protected $_keepProportion = false;
	
	/**
	 * Open image file to work with 
	 *
	 * @param string $filename - path to file
	 */
	public function open($filename) {
		
		$this->_fileName = $filename;
		$this->getMimeType ();
		$this->_getFileAttributes ();
		
		switch ( $this->_fileType) {
			case IMAGETYPE_GIF :
				$this->_imageHandler = imagecreatefromgif ( $this->_fileName );
			break;
			
			case IMAGETYPE_JPEG :
				$this->_imageHandler = imagecreatefromjpeg ( $this->_fileName );
			break;
			
			case IMAGETYPE_PNG :
				$this->_imageHandler = imagecreatefrompng ( $this->_fileName );
			break;
			
			case IMAGETYPE_XBM :
				$this->_imageHandler = imagecreatefromxbm ( $this->_fileName );
			break;
			
			case IMAGETYPE_WBMP :
				$this->_imageHandler = imagecreatefromxbm ( $this->_fileName );
			break;
			
			default :
				throw new Exception ( "Unsupported image format." );
			break;
		}
	}	
	/**
	 * Save previously opened image 
	 *
	 * @param string $destination - new file path (if empty will be used an old one)
	 * @param string $newName - new file name (if empty will be used an old one)
	 */
	public function save($destination = null, $newName = null) {
		$fileName = (! isset ( $destination )) ? $this->_fileName : $destination;
		
		if (isset ( $destination ) && isset ( $newName )) {
			$fileName = $destination . "/" . $fileName;
		} elseif (isset ( $destination ) && ! isset ( $newName )) {
			$info = pathinfo ( $destination );
			$fileName = $destination;
			$destination = $info ['dirname'];
		} elseif (! isset ( $destination ) && isset ( $newName )) {
			$fileName = $this->_fileSrcPath . "/" . $newName;
		} else {
			$fileName = $this->_fileSrcPath . $this->_fileSrcName;
		}
		
		//$destinationDir = (isset ( $destination )) ? $destination : $this->_fileSrcPath;
		
		switch ( $this->_fileType) {
			case IMAGETYPE_GIF :
				imagegif ( $this->_imageHandler, $fileName );
			break;
			
			case IMAGETYPE_JPEG :
				imagejpeg ( $this->_imageHandler, $fileName);
			break;
			
			case IMAGETYPE_PNG :
				$this->_saveAlpha ( $this->_imageHandler );
				imagepng ( $this->_imageHandler, $fileName);
			break;
			
			case IMAGETYPE_XBM :
				imagexbm ( $this->_imageHandler, $fileName );
			break;
			
			case IMAGETYPE_WBMP :
				imagewbmp ( $this->_imageHandler, $fileName );
			break;
			
			default :
				throw new Exception ( "Unsupported image format." );
			break;
		}
	
	}
	/**
	 * Sends previously opend image to stream. (display it)
	 *
	 */
	public function display() {
		header ( "Content-type: " . $this->getMimeType () );
		switch ( $this->_fileType) {
			case IMAGETYPE_GIF :
				imagegif ( $this->_imageHandler );
			break;
			
			case IMAGETYPE_JPEG :
				imagejpeg ( $this->_imageHandler );
			break;
			
			case IMAGETYPE_PNG :
				imagepng ( $this->_imageHandler );
			break;
			
			case IMAGETYPE_XBM :
				imagexbm ( $this->_imageHandler );
			break;
			
			case IMAGETYPE_WBMP :
				imagewbmp ( $this->_imageHandler );
			break;
			
			default :
				throw new Exception ( "Unsupported image format." );
			break;
		}
	}	
	/**
	 * Resize previously opened image
	 *
	 * @param int $dstWidth - new width
	 * @param int $dstHeight - new height
	 */
	public function resize($dstWidth = null, $dstHeight = null) {
		if (! isset ( $dstWidth ) && ! isset ( $dstHeight )) {
			throw new Exception ( "Invalid image dimensions." );
		}
		
		if ($this->keepProportion ()) {
			
			$ratio = $this->_imageSrcWidth / $dstWidth;
		    //$ratioWidth = $this->_imageSrcWidth / $dstWidth;
			//$ratioHeight = $this->_imageSrcHeight / $dstHeight;
			
			/*if ($ratioWidth < $ratioHeight) {
				$width = $this->_imageSrcWidth / $ratioHeight;
				$height = $dstHeight;
			} else {
				$width = $dstWidth;
				$height = $this->_imageSrcHeight / $ratioWidth;
			}*/
			
			$width = $dstWidth;
			$height = $this->_imageSrcHeight/$ratio;
			
			if ($this->_imageSrcWidth < $width || $this->_imageSrcHeight < $height) {
				$width = $this->_imageSrcWidth;
				$height = $this->_imageSrcHeight;
			}
			
			$xOffset = 0;
			$yOffset = 0;
		} else {
			$xOffset = 0;
			$yOffset = 0;
			$width = $dstWidth;
			$height = $dstHeight;
		}
		
		$imageNewHandler = imagecreatetruecolor ( $width, $height );
		
		if ($this->_fileType == IMAGETYPE_PNG) {
			$this->_saveAlpha ( $imageNewHandler );
		}
		
		imagecopyresampled ( $imageNewHandler, $this->_imageHandler, $xOffset, $yOffset, 0, 0, $width, $height, $this->_imageSrcWidth, $this->_imageSrcHeight );
		
		$this->_imageHandler = $imageNewHandler;
		
		$this->refreshImageDimensions ();
	}
	/**
	 * Rotate previosly opened image
	 *
	 * @param int $angle - angle to rotate
	 */
	public function rotate($angle) {
		$this->_imageHandler = imagerotate ( $this->_imageHandler, $angle, $this->imageBackgroundColor );
		$this->refreshImageDimensions ();
	}
	/**
	 * Adds watermark to image
	 *
	 * @param string $watermarkImage - path to watermark image
	 * @param int $positionX - watermark left coordinate  
	 * @param int $positionY - wtermark top coordinate
	 * @param int $watermarkImageOpacity - opacity
	 * @param bool $repeat
	 */	
	public function watermark($watermarkImage, $positionX = 0, $positionY = 0, $watermarkImageOpacity = 30, $repeat = false) {
		list ( $watermarkSrcWidth, $watermarkSrcHeight, $watermarkFileType ) = getimagesize ( $watermarkImage );
		$this->_getFileAttributes ();
		switch ( $watermarkFileType) {
			case IMAGETYPE_GIF :
				$watermark = imagecreatefromgif ( $watermarkImage );
			break;
			
			case IMAGETYPE_JPEG :
				$watermark = imagecreatefromjpeg ( $watermarkImage );
			break;
			
			case IMAGETYPE_PNG :
				$watermark = imagecreatefrompng ( $watermarkImage );
			break;
			
			case IMAGETYPE_XBM :
				$watermark = imagecreatefromxbm ( $watermarkImage );
			break;
			
			case IMAGETYPE_WBMP :
				$watermark = imagecreatefromxbm ( $watermarkImage );
			break;
			
			default :
				throw new Exception ( "Unsupported watermark image format." );
			break;
		}
		
		if ($this->getWatermarkWidth () && $this->getWatermarkHeigth () && ($this->getWatermarkPosition () != self::POSITION_STRETCH)) {
			$newWatermark = imagecreatetruecolor ( $this->getWatermarkWidth (), $this->getWatermarkHeigth () );
			imagealphablending ( $newWatermark, false );
			$col = imagecolorallocate ( $newWatermark, 255, 255, 255 );
			imagefilledrectangle ( $newWatermark, 0, 0, $this->getWatermarkWidth (), $this->getWatermarkHeigth (), $col );
			imagealphablending ( $newWatermark, true );
			
			imagecopyresampled ( $newWatermark, $watermark, 0, 0, 0, 0, $this->getWatermarkWidth (), $this->getWatermarkHeigth (), imagesx ( $watermark ), imagesy ( $watermark ) );
			$watermark = $newWatermark;
		}
		
		if ($this->getWatermarkPosition () == self::POSITION_TILE) {
			$repeat = true;
		} elseif ($this->getWatermarkPosition () == self::POSITION_STRETCH) {
			$newWatermark = imagecreatetruecolor ( $this->_imageSrcWidth, $this->_imageSrcHeight );
			imagealphablending ( $newWatermark, false );
			$col = imagecolorallocate ( $newWatermark, 255, 255, 255 );
			imagefilledrectangle ( $newWatermark, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight, $col );
			imagealphablending ( $newWatermark, true );
			
			imagecopyresampled ( $newWatermark, $watermark, 0, 0, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight, imagesx ( $watermark ), imagesy ( $watermark ) );
			$watermark = $newWatermark;
		} elseif ($this->getWatermarkPosition () == self::POSITION_TOP_RIGHT) {
			$positionX = ($this->_imageSrcWidth - imagesx ( $watermark ));
			imagecopymerge ( $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx ( $watermark ), imagesy ( $watermark ), $watermarkImageOpacity );
		} elseif ($this->getWatermarkPosition () == self::POSITION_BOTTOM_RIGHT) {
			$positionX = ($this->_imageSrcWidth - imagesx ( $watermark ));
			$positionY = ($this->_imageSrcHeight - imagesy ( $watermark ));
			imagecopymerge ( $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx ( $watermark ), imagesy ( $watermark ), $watermarkImageOpacity );
		} elseif ($this->getWatermarkPosition () == self::POSITION_BOTTOM_LEFT) {
			$positionY = ($this->_imageSrcHeight - imagesy ( $watermark ));
			imagecopymerge ( $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx ( $watermark ), imagesy ( $watermark ), $watermarkImageOpacity );
		}
		
		if ($repeat === false) {
			imagecopymerge ( $this->_imageHandler, $watermark, $positionX, $positionY, 0, 0, imagesx ( $watermark ), imagesy ( $watermark ), $watermarkImageOpacity );
		} else {
			$offsetX = $positionX;
			$offsetY = $positionY;
			while ( $offsetY <= ($this->_imageSrcHeight + imagesy ( $watermark )) ) {
				while ( $offsetX <= ($this->_imageSrcWidth + imagesx ( $watermark )) ) {
					imagecopymerge ( $this->_imageHandler, $watermark, $offsetX, $offsetY, 0, 0, imagesx ( $watermark ), imagesy ( $watermark ), $watermarkImageOpacity );
					$offsetX += imagesx ( $watermark );
				}
				$offsetX = $positionX;
				$offsetY += imagesy ( $watermark );
			}
		}
		imagedestroy ( $watermark );
		$this->refreshImageDimensions ();
	}
	/**
	 * Crops part of previously opened image
	 *
	 * @param int $top
	 * @param int $bottom
	 * @param int $right
	 * @param int $left
	 */
	public function crop($top = 0, $bottom = 0, $right = 0, $left = 0) {
		if ($left == 0 && $top == 0 && $right == 0 && $bottom == 0) {
			return;
		}
		
		$newWidth = $this->_imageSrcWidth - $left - $right;
		$newHeight = $this->_imageSrcHeight - $top - $bottom;
		
		$canvas = imagecreatetruecolor ( $newWidth, $newHeight );
		
		if ($this->_fileType == IMAGETYPE_PNG) {
			$this->_saveAlpha ( $canvas );
		}
		
		imagecopyresampled ( $canvas, $this->_imageHandler, $top, $bottom, $right, $left, $this->_imageSrcWidth, $this->_imageSrcHeight, $newWidth, $newHeight );
		
		$this->_imageHandler = $canvas;
		$this->refreshImageDimensions ();
	}
	
	public function checkDependencies() {
		foreach ( $this->_requiredExtensions as $value ) {
			if (! extension_loaded ( $value )) {
				throw new Exception ( "Required PHP extension '{$value}' was not loaded." );
			}
		}
	}
	
	private function refreshImageDimensions() {
		$this->_imageSrcWidth = imagesx ( $this->_imageHandler );
		$this->_imageSrcHeight = imagesy ( $this->_imageHandler );
	}
	
	function __destruct() {
		imagedestroy ( $this->_imageHandler );
	}
	
	/*
     * Fixes saving PNG alpha channel
     */
	private function _saveAlpha($imageHandler) {
		$background = imagecolorallocate ( $imageHandler, 0, 0, 0 );
		ImageColorTransparent ( $imageHandler, $background );
		imagealphablending ( $imageHandler, false );
		imagesavealpha ( $imageHandler, true );
	}
	
	public function getMimeType() {
		if ($this->_fileType) {
			return $this->_fileType;
		} else {
			
			list ( $this->_imageSrcWidth, $this->_imageSrcHeight, $this->_fileType ) = getimagesize ( $this->_fileName );
			$this->_fileMimeType = image_type_to_mime_type ( $this->_fileType );
			
			return $this->_fileMimeType;
		}
	}
	
	public function setWatermarkPosition($position) {
		$this->_watermarkPosition = $position;
		return $this;
	}
	
	public function getWatermarkPosition() {
		return $this->_watermarkPosition;
	}
	
	public function setWatermarkWidth($width) {
		$this->_watermarkWidth = $width;
		return $this;
	}
	
	public function getWatermarkWidth() {
		return $this->_watermarkWidth;
	}
	
	public function setWatermarkHeigth($heigth) {
		$this->_watermarkHeigth = $heigth;
		return $this;
	}
	
	public function getWatermarkHeigth() {
		return $this->_watermarkHeigth;
	}
	
	/**
	 * Keep proportion after resize
	 *
	 * @param bool $flag
	 * @return Bel_File_Image
	 */
	public function setKeepProportion($flag) {
		$this->_keepProportion = $flag;
		return $this;
	}
	
	public function keepProportion() {
		return $this->_keepProportion;
	}
	
	protected function _getFileAttributes() {
		$pathinfo = pathinfo ( $this->_fileName );
		
		$this->_fileSrcPath = $pathinfo ['dirname'];
		$this->_fileSrcName = $pathinfo ['basename'];
	}
}