<?php 

/**
 * Image library
 * 	create thumbs
 * 	auto resize & crop images
 * 	<example>
 *   K_Loader::load('Image');
	 $image = new K_Image('http://domogid.loc/images/image.jpg');
	 $image->saveThumb( 150, 150, ROOT_PATH.'/www/images/test.jpg', K_IMAGE_BOTTOM|K_IMAGE_RIGHT );
 *  </example>
 */

define('K_IMAGE_X_CENTER', 1);
define('K_IMAGE_Y_CENTER', 2);
define('K_IMAGE_TOP', 		4);
define('K_IMAGE_BOTTOM', 	8);
define('K_IMAGE_LEFT', 	16);
define('K_IMAGE_RIGHT', 	32);
define('K_IMAGE_CENTER', 	K_IMAGE_X_CENTER|K_IMAGE_Y_CENTER );

class K_Image {
	protected $data = null;
	protected $gd = null;
	
	protected $originalPath = null;
	protected $originalWidth = 0;
	protected $originalHeight = 0;
	protected $imageType = 0;
	
	public function __construct( $originalPath ) {		
		$this->setImage( $originalPath );
	}
	
	public function setImage( $path ) {
		
		$info = getimagesize ( $path );
		
		if ( $info ) {
			$this->imageType = $info['mime'];
			$this->originalWidth = $info[0];
			$this->originalHeight = $info[1];
			$this->originalPath = $path;
			
			switch ($this->imageType) {
				case 'image/gif':
						$this->gd = imagecreatefromgif ( $path );
					break;
				case 'image/jpeg':
						$this->gd = imagecreatefromjpeg ( $path );
					break;
				case 'image/png':
						$this->gd = imagecreatefrompng ( $path );
					break;
				case 'image/wbmp':
						$this->gd = imagecreatefromwbmp ( $path );
					break;
			}
		}
		return $info;
	}
	
	public function saveThumb( $width, $height, $path, $offsetType = K_IMAGE_CENTER ) {
		$widthCoeff = $this->originalWidth/$width; 
		$heightCoeff = $this->originalHeight/$height;
		
		$coeff = 1;
		if ( $heightCoeff < $widthCoeff ) {
			$coeff = $heightCoeff;
		} else {
			$coeff = $widthCoeff;
		}
		
		$sampleWidth = $width*$coeff;
		$sampleHeight = $height*$coeff;
		
		$offsetX = 0;
		// Offset X
		if ( $offsetType & K_IMAGE_LEFT ) {
			$offsetX = 0; 
		} elseif ( $offsetType & K_IMAGE_RIGHT ) {
			$offsetX = $this->originalWidth-$sampleWidth; 
		} else {
			$offsetX = ($this->originalWidth-$sampleWidth)/2;
		}
		
		$offsetY = 0;
		// Offset Y
		if ( $offsetType & K_IMAGE_TOP ) {
			$offsetY = 0;
		} elseif ( $offsetType & K_IMAGE_BOTTOM ) {
			$offsetY = $this->originalHeight-$sampleHeight;
		} else {
			$offsetY = ($this->originalHeight-$sampleHeight)/2;
		}
		
		$thumb = @imagecreatetruecolor( $width, $height );
		imageantialias ( $thumb , true );
				
		if ( imagecopyresized ( 
				$thumb, 
				$this->gd, 
				0, 0, 
				$offsetX , $offsetY, 
				$width , $height , 
				$sampleWidth , $sampleHeight ) ) 
		{
			imagejpeg( $thumb, $path );
			return TRUE;
		}
		return FALSE;
	}
	
	public static function support() {
	    if (extension_loaded('gd') and
	        imagetypes() & IMG_PNG and
	        imagetypes() & IMG_GIF and
	        imagetypes() & IMG_JPG and
	        imagetypes() & IMG_WBMP) {
	        return true;
	    } else {
	        return false;
	    }
	}
}

?>