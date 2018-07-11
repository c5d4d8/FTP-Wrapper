<?php

//Een wrapper class voor de php ftp module functies. Geschreven door Kadir.
	class FTP
	{
		public function connect($host, $username, $password, $port = null)
		{
			$this->host 		= $host;
			$this->username 	= $username;
			$this->password 	= $password;
			$this->port			= $port;
			@$this->connect 	= $this->port === null ? ftp_connect($this->host) : ftp_connect($this->host, $this->port);
			return !@ftp_login($this->connect, $this->username, $this->password) ? die("Error connecting to host.") : $this;
		}
		
		public function pull($path = '.', $pasv = false)
		{
			$this->path = $path;
			$this->pasv = $pasv ? ftp_pasv($this->connect, true) : false;
			$data 		= ftp_nlist($this->connect, $this->path);
			return !$data ? die("Error retrieving files from host") : $data;
		}
		
		public function download($file, $pasv = false)
		{
			$this->ffile 		= $file;
			@$this->trueFile 	= in_array('/', str_split($this->ffile), true) ? end(explode('/', $this->ffile)) : $this->ffile;
			$writeFile 			= fopen($this->trueFile, 'w');
			$this->pasv 		= $pasv ? ftp_pasv($this->connect, true) : false;
			@ftp_fget($this->connect, $writeFile, $this->ffile, FTP_BINARY) or die("Error downloading file from host.");
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($this->trueFile).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($this->trueFile));
			readfile($this->trueFile);
			fclose($writeFile);
			unlink($this->trueFile);
		}
		
		public function upload($file, $destination = false, $pasv = false)
		{
			$this->ffile 		= empty($_FILES) ? $file : $file['name'];
			$this->fileName		= empty($_FILES) ? $file : $file['tmp_name'];
			$this->pasv 		= $pasv ? ftp_pasv($this->connect, true) : false;
			$this->destination  = !$destination ? null : @ftp_chdir($this->connect, $destination);
			@ftp_put($this->connect, $this->ffile, $this->fileName, FTP_BINARY) or die("Error uploading file.");
		}
	}

	//$ftp = new FTP();

	//echo "<pre>";
	//print_r($ftp->connect('www.voorbeeld.com', 'gebruiker@voorbeeld.com', 'wachtwoord')->pull('forumproject'));
	//echo "</pre>";
	
	//$ftp->connect('www.voorbeeld.com', 'gebruiker@voorbeeld.com', 'wachtwoord')->download("forumproject/confirm.php");
	
	//$ftp->connect('www.voorbeeld.com', 'gebruiker@voorbeeld.com', 'wachtwoord');
	//$ftp->download('python.py');
	
	//$ftp->connect('www.voorbeeld.com', 'gebruiker@voorbeeld.com', 'wachtwoord')->upload('banana.txt', 'discordtest');

	//$ftp->connect('www.voorbeeld.com', 'gebruiker@voorbeeld.com', 'wachtwoord')->upload($_FILES['file'], 'discordtest');
?>
