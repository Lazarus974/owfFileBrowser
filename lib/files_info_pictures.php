<?php

class files_info_pictures extends files_info {
	
	private $mime;
	
	public function set_mime($mime) { $this->mime = $mime; return $this; }
	
	public function get_info($filename) {
		$ret = array();
		
		$mime = $this->mime;
		// Get new sizes
		list($width, $height) = getimagesize($filename);
		
		if($width < 300 && $height < 300)
			$data = file_get_contents($filename);
		else {
			$ratio = min(300 / $width, 300 / $height);
			$newwidth = $width * $ratio;
			$newheight = $height * $ratio;
			
			$types = array("jpeg", "png", "gif");
			if(!in_array($mime, $types))
				return array();
			
			// Load
			$thumb = imagecreatetruecolor($newwidth, $newheight);
			$source = call_user_func("imagecreatefrom$mime", $filename);

			// Resize
			imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

			// Output and free memory
			//the resized image will be 400x300
			ob_start();
			call_user_func("image$mime", $thumb);
			$data = ob_get_clean();
			imagedestroy($thumb);
		}
		
		$ret['image'] = array(
			'name' => "Preview",
			'value' => "<img src='data:image/$mime;base64,".base64_encode($data)."' />",
			'html' => true
		);
		
		return $ret;
	}
}
