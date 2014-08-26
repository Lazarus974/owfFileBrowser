<?php

class files_info_mp3 extends files_info {
	
	public function get_info($filename) {
		$ret = array();
		
		$finfo = new ID3($filename);
		
		$fields = array(
			"artist" => $this->ts("Artist"),
			"title" => $this->ts("Title"),
			"album" => $this->ts("Album"),
			"genre" => $this->ts("Genre"),
			"year" => $this->ts("Year"),
			"track" => $this->ts("Track"),
			"comment" => $this->ts("Comment"),
			"duration" => $this->ts("Duration"),
			"bitrate" => $this->ts("Bit Rate"),
			"compression" => $this->ts("Compression"),
			"samplerate" => $this->ts("Sample Rate"),
		);
		
		$artist = $finfo->getArtist();
		$title = $finfo->getTitle();
		$album = $finfo->getAlbum();
		$genre = $finfo->getGenre();
		$year = $finfo->getYear();
		$track = $finfo->getTrack();
		$comment = $finfo->getComment();
		$duration = $finfo->getDuration();
		$bitrate = $finfo->getBitrate();
		$compression = $finfo->getCompression();
		$samplerate = $finfo->getSamplerate();
		
		foreach($fields as $k => $v) {
			if($$k) {
				$ret[$k] = array(
					'name' => $v,
					'value' => $$k
				);
				if($k == "samplerate")
					$ret[$k]['value'] .= ' Khz';
			}
		}
		
		return $ret;
		
		//$start = filesize($filename) - 128;
		//$fp = fopen($filename, 'r');
		//fseek($fp, $start);
		//$tag = fread($fp, 3);
		//if($tag == "TAG") {
			//$title = trim(fread($fp, 30));
			//$artist = trim(fread($fp, 30));
			//$year = trim(fread($fp, 4));
			//$comment = trim(fread($fp, 30));
			//$genre = trim(fread($fp, 1));
			
			
		//}
		//fclose($fp);
		
		return $ret;
	}
}
