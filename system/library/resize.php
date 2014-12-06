<?php
class Resize {
	private $file;
	private $image;
	private $info;
	private $http_image;

	public function __construct($register) {
		$this->http_image = HTTP_SERVER . 'image/';
	}

	public function load($src) {

		if(!file_exists($src)) return false;
			
		$info = getimagesize($src);

		switch( $info['mime'] ) {

			case 'image/gif':
				$image = imagecreatefromgif($src);
				break;
					
			case 'image/jpeg':
				$image = imagecreatefromjpeg($src);
				break;
					
			case 'image/png':
				$image = imagecreatefrompng($src);
				break;
					
			default:
				// Unsupported image type
				return false;
				break;
					
		}

		return array($image, $info);
	}

	private function isCache($old_image, $new_image) {
		$is_new_image = false;

		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
			$is_new_image = true;
		} else {
			$is_new_image = false;
		}

		return $is_new_image;
	}

	// Saves an image resource to file
	private function save($image, $filename, $type, $quality = null) {

		switch( $type ) {

			case 'image/gif':
				return imagegif($image, $filename);
				break;
					
			case 'image/jpeg':
				if( $quality == null ) $quality = 85;
				if( $quality < 0 ) $quality = 0;
				if( $quality > 100 ) $quality = 100;
				return imagejpeg($image, $filename, $quality);
				break;
					
			case 'image/png':
				if( $quality == null ) $quality = 9;
				if( $quality > 9 ) $quality = 9;
				if( $quality < 1 ) $quality = 0;
				return imagepng($image, $filename, $quality);
				break;
					
			default:
				// Unsupported image type
				return false;
				break;
					
		}

	}

	// Same as PHP's imagecopymerge() function, except preserves alpha-transparency in 24-bit PNGs
	private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){

		$cut = imagecreatetruecolor($src_w, $src_h);
		imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
		imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
		return imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);

	}

	// Converts a hex color value to its RGB equivalent
	private function hex2rgb($hex_color) {

		if( $hex_color[0] == '#' ) $hex_color = substr($hex_color, 1);
		if( strlen($hex_color) == 6 ) {
			list($r, $g, $b) = array(
					$hex_color[0] . $hex_color[1],
					$hex_color[2] . $hex_color[3],
					$hex_color[4] . $hex_color[5]
			);
		} elseif( strlen($hex_color) == 3 ) {
			list($r, $g, $b) = array(
					$hex_color[0] . $hex_color[0],
					$hex_color[1] . $hex_color[1],
					$hex_color[2] . $hex_color[2]
			);
		} else {
			return false;
		}

		return array(
				'r' => hexdec($r),
				'g' => hexdec($g),
				'b' => hexdec($b)
		);

	}

	// Produce a sepia-like effect
	public function sepia($src, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-sepia');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);

		imagefilter($original, IMG_FILTER_GRAYSCALE);
		imagefilter($original, IMG_FILTER_COLORIZE, 90, 60, 30);

		$this->makedir($dest_partial);
		$this->save($original, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	// Make image pixelized (requires PHP 5.3+)
	public function pixelate($src, $block_size, $advanced_pix = false, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-pixelate');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);

		imagefilter($original, 11, $block_size, $advanced_pix);

		$this->makedir($dest_partial);
		$this->save($original, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	// Make image smoother
	public function smooth($src, $level, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-smooth');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);

		imagefilter($original, IMG_FILTER_SMOOTH, $level);

		$this->makedir($dest_partial);
		$this->save($original, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	// Create a sketch effect
	public function sketch($src, $level = 1, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-sketch');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);

		for( $i = 0; $i < $level; $i++ ) imagefilter($original, IMG_FILTER_MEAN_REMOVAL);

		$this->makedir($dest_partial);
		$this->save($original, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	// Blur an image
	public function blur($src, $level = 1, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-blur');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);

		for( $i = 0; $i < $level; $i++ ) imagefilter($original, IMG_FILTER_GAUSSIAN_BLUR);

		$this->makedir($dest_partial);
		$this->save($original, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	// Resize an image to the specified dimensions
	public function resize($src, $new_width, $new_height, $resample = true, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-' . $new_width . 'x' . $new_height);
		$dest = DIR_IMAGE . $dest_partial;

		$isCached = $this->isCache($src, $dest_partial);
		if ($isCached) {

			list($original, $info) = $this->load(DIR_IMAGE . $src);

			$new = imagecreatetruecolor($new_width, $new_height);

			// Preserve alphatransparency in PNGs
			imagealphablending($new, false);
			imagesavealpha($new, true);

			if( $resample ) {
				imagecopyresampled($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			} else {
				imagecopyresized($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			}

			$this->makedir($dest_partial);
			$this->save($new, $dest, $info['mime'], $quality);
		}

		return $this->getExternalPath($dest_partial);
	}

	// Proportionally scale an image to fit the specified width
	public function resize_to_width($src, $new_width, $resample = true, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return DIR_IMAGE . $src;

		$dest_partial = $this->getCachePartialPath($src, '-' . $new_width);
		$dest = DIR_IMAGE . $dest_partial;

		$isCached = $this->isCache($src, $dest_partial);
		if ($isCached) {

			list($original, $info) = $this->load(DIR_IMAGE . $src);

			// Determine aspect ratio
			$aspect_ratio = $info[1] / $info[0];

			// Adjust height proportionally to new width
			$new_height = $new_width * $aspect_ratio;

			$new = imagecreatetruecolor($new_width, $new_height);

			// Preserve alphatransparency in PNGs
			imagealphablending($new, false);
			imagesavealpha($new, true);

			if( $resample ) {
				imagecopyresampled($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			} else {
				imagecopyresized($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			}

			$this->makedir($dest_partial);
			$this->save($new, $dest, $info['mime'], $quality);
		}

		return $this->getExternalPath($dest_partial);
	}

	// Proportionally scale an image to fit the specified height
	public function resize_to_height($src, $new_height, $resample = true, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-' . $new_height);
		$dest = DIR_IMAGE . $dest_partial;

		$isCached = $this->isCache($src, $dest_partial);
		if ($isCached) {

			list($original, $info) = $this->load(DIR_IMAGE . $src);

			// Determine aspect ratio
			$aspect_ratio = $info[1] / $info[0];

			// Adjust height proportionally to new width
			$new_width = $new_height / $aspect_ratio;

			$new = imagecreatetruecolor($new_width, $new_height);

			// Preserve alphatransparency in PNGs
			imagealphablending($new, false);
			imagesavealpha($new, true);

			if( $resample ) {
				imagecopyresampled($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			} else {
				imagecopyresized($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			}

			$this->makedir($dest_partial);
			$this->save($new, $dest, $info['mime'], $quality);
		}

		return $this->getExternalPath($dest_partial);
	}

	// Proportionally shrink an image to fit within a specified width/height
	public function shrink_to_fit($src, $max_width, $max_height, $resample = true, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-' . $max_width . 'x' . $max_height);
		$dest = DIR_IMAGE . $dest_partial;

		$isCached = $this->isCache($src, $dest_partial);
		if ($isCached) {

			list($original, $info) = $this->load(DIR_IMAGE . $src);

			// Determine aspect ratio
			$aspect_ratio = $info[1] / $info[0];

			// Make width fit into new dimensions
			if( $info[0] > $max_width ) {
				$new_width = $max_width;
				$new_height = $new_width * $aspect_ratio;
			} else {
				$new_width = $info[0];
				$new_height = $info[1];
			}

			// Make height fit into new dimensions
			if( $new_height > $max_height ) {
				$new_height = $max_height;
				$new_width = $new_height / $aspect_ratio;
			}

			$new = imagecreatetruecolor($new_width, $new_height);

			// Preserve alphatransparency in PNGs
			imagealphablending($new, false);
			imagesavealpha($new, true);

			if( $resample ) {
				imagecopyresampled($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			} else {
				imagecopyresized($new, $original, 0, 0, 0, 0, $new_width, $new_height, $info[0], $info[1]);
			}

			$this->makedir($dest_partial);
			$this->save($new, $dest, $info['mime'], $quality);
		}

		return $this->getExternalPath($dest_partial);
	}

	// Crop an image and optionally resize the resulting piece
	public function crop($src, $x1, $y1, $x2, $y2, $new_width = null, $new_height = null, $target = null, $resample = true, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-crop');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);

		// Crop size
		if( $x2 < $x1 ) list($x1, $x2) = array($x2, $x1);
		if( $y2 < $y1 ) list($y1, $y2) = array($y2, $y1);
		$crop_width = $x2 - $x1;
		$crop_height = $y2 - $y1;

		if( $new_width == null ) $new_width = $crop_width;
		if( $new_height == null ) $new_height = $crop_height;

		$new = imagecreatetruecolor($new_width, $new_height);

		// Preserve alphatransparency in PNGs
		imagealphablending($new, false);
		imagesavealpha($new, true);

		// Create the new image
		if( $resample ) {
			imagecopyresampled($new, $original, 0, 0, $x1, $y1, $new_width, $new_height, $crop_width, $crop_height);
		} else {
			imagecopyresized($new, $original, 0, 0, $x1, $y1, $new_width, $new_height, $crop_width, $crop_height);
		}

		if ($target != null) {
			$dest = DIR_IMAGE . $target;
			$dest_partial = $target;
		}

		$this->makedir($dest_partial);
		$this->save($new, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	// Trim the edges of a portrait or landscape image to make it square and optionally resize the resulting image
	public function square_crop($src, $new_size = null, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-' . $new_size . '-square');
		$dest = DIR_IMAGE . $dest_partial;

		$isCached = $this->isCache($src, $dest_partial);
		if ($isCached) {

			list($original, $info) = $this->load(DIR_IMAGE . $src);

			// Calculate measurements
			if( $info[0] > $info[1] ) {
				// For landscape images
				$x_offset = ($info[0] - $info[1]) / 2;
				$y_offset = 0;
				$square_size = $info[0] - ($x_offset * 2);
			} else {
				// For portrait and square images
				$x_offset = 0;
				$y_offset = ($info[1] - $info[0]) / 2;
				$square_size = $info[1] - ($y_offset * 2);
			}

			if( $new_size == null ) $new_size = $square_size;

			// Resize and crop
			$new = imagecreatetruecolor($new_size, $new_size);

			// Preserve alphatransparency in PNGs
			imagealphablending($new, false);
			imagesavealpha($new, true);

			imagecopyresampled($new, $original, 0, 0, $x_offset, $y_offset, $new_size, $new_size, $square_size, $square_size);

			$this->makedir($dest_partial);
			$this->save($new, $dest, $info['mime'], $quality);
		}

		return $this->getExternalPath($dest_partial);
	}

	public function landscape_crop($scr, $width, $height){
		$image = $this->resize_to_width($scr, $width);
		$image_info = $this->imageCacheInfo($scr, '-' . $width);
		if(!empty($image_info) && $image_info[1] > $image_info[0]){
			$y1 = $image_info[1]/2 - $height/2;
			$y2 = $y1 + $height;
			$image = $this->crop($this->getCachePartialPath($scr, '-' . $width), 0, $y1, $image_info[0], $y2);
		}

		$image = str_replace(" ", "%20", $image);

		return $image;
	}

	// Overlay an image on top of another image with opacity; works with 24-big PNG alpha-transparency
	public function watermark($src, $watermark_src, $position = 'center', $opacity = 50, $margin = 0, $quality = null) {

		if(!$src || !file_exists(DIR_IMAGE . $src)) return false;

		$dest_partial = $this->getCachePartialPath($src, '-watermark');
		$dest = DIR_IMAGE . $dest_partial;

		list($original, $info) = $this->load(DIR_IMAGE . $src);
		list($watermark, $watermark_info) = $this->load(DIR_IMAGE . $watermark_src);

		switch( strtolower($position) ) {

			case 'top-left':
			case 'left-top':
				$x = 0 + $margin;
				$y = 0 + $margin;
				break;
					
			case 'top-right':
			case 'right-top':
				$x = $info[0] - $watermark_info[0] - $margin;
				$y = 0 + $margin;
				break;
					
			case 'top':
			case 'top-center':
			case 'center-top':
				$x = ($info[0] / 2) - ($watermark_info[0] / 2);
				$y = 0 + $margin;
				break;
					
			case 'bottom-left':
			case 'left-bottom':
				$x = 0 + $margin;
				$y = $info[1] - $watermark_info[1] - $margin;
				break;
					
			case 'bottom-right':
			case 'right-bottom':
				$x = $info[0] - $watermark_info[0] - $margin;
				$y = $info[1] - $watermark_info[1] - $margin;
				break;
					
			case 'bottom':
			case 'bottom-center':
			case 'center-bottom':
				$x = ($info[0] / 2) - ($watermark_info[0] / 2);
				$y = $info[1] - $watermark_info[1] - $margin;
				break;
					
			case 'left':
			case 'center-left':
			case 'left-center':
				$x = 0 + $margin;
				$y = ($info[1] / 2) - ($watermark_info[1] / 2);
				break;
					
			case 'right':
			case 'center-right':
			case 'right-center':
				$x = $info[0] - $watermark_info[0] - $margin;
				$y = ($info[1] / 2) - ($watermark_info[1] / 2);
				break;
					
			case 'center':
			default:
				$x = ($info[0] / 2) - ($watermark_info[0] / 2);
				$y = ($info[1] / 2) - ($watermark_info[1] / 2);
				break;
					
		}

		$this->imagecopymerge_alpha($original, $watermark, $x, $y, 0, 0, $watermark_info[0], $watermark_info[1], $opacity);

		$this->makedir($dest_partial);
		$this->save($original, $dest, $info['mime'], $quality);

		return $this->getExternalPath($dest_partial);
	}

	public function makedir($new_path) {
		$path = '';
		$directories = explode('/', dirname(str_replace('../', '', $new_path)));

		foreach ($directories as $directory) {
			$path = $path . '/' . $directory;

			if (!file_exists(DIR_IMAGE . $path))
				@mkdir(DIR_IMAGE . $path, 0777);
		}
	}

	public function imageinfo($src) {
		list($original, $info) = $this->load(DIR_IMAGE . $src);

		return $info;
	}

	public function imageCacheInfo($src, $new_partial = '') {
		$dest_partial = $this->getCachePartialPath($src, $new_partial);
		list($original, $info) = $this->load(DIR_IMAGE . $dest_partial);

		return $info;
	}

	public function getCachePartialPath($src, $new_partial = ''){
		$info = pathinfo($src);
		$extension = $info['extension'];

		$dest_partial = utf8_substr($src, 0, utf8_strrpos($src, '.')) . $new_partial . '.' . $extension;
		$dest_partial = preg_match('/^cache/', $dest_partial) ? $dest_partial : 'cache/' . $dest_partial;

		return $dest_partial;
	}

	public function getExternalPath($src){
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return $this->http_image . $src;
		} else {
			return $this->http_image . $src;
		}
	}

	public function redirect($url, $statusCode = 303) {
		header('Location: ' . $url, true , $statusCode);
		die();
	}
}
?>
