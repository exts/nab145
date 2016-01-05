<?php
	Class Template
	{
		var $html;
		var $file;
		var $dir;
		function tp($e1 = __LINE__,$e2 = __FILE__)
		{	
			if( is_dir("./styles/" . $this->dir) )
			{
				if( file_exists("./styles/" . $this->dir . '/templates/' . $this->file) )
				{	
					$this->html = file_get_contents("./styles/" . $this->dir . '/templates/' . $this->file);
				}
				else
				{
					echo "The file \"" . $this->dir . '/templates/' . $this->file . "\" doesn't exist.<br /> On Line: " . $e1 . " In file: " . $e2;
				}
			}
			else
			{
				echo "This directory doesn't exist";
			}
		}
		function tr($temp = array())
		{
			if( is_array($temp) )	
			{
				foreach($temp as $replace => $val)
				{
					$this->html = str_replace("{" . $replace . "}", $val , $this->html);
				}
			}
		}
		function to()
		{
			return $this->html;
		}
		function _print()
		{
			print $this->html;
		}
	}
?>