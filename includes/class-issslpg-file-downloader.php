<?php

class ISSSLPG_File_Downloader {

	public function download_file( $file_name, $file_extension, $content_type, $file_content ) {
		header( "Content-Disposition: attachment; filename={$file_name}.{$file_extension}" );
		header( "Content-Type: {$content_type}" );
		echo $file_content;
		exit();
	}

}