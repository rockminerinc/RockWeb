<?php

function showmsg($msg, $url_forward=WEB_ROOT, $second=5) 
{
	$CI =&get_instance();
  //语言
	//$CI->session->set_flashdata('msg', $msg);
	$message = $msg;
  //显示
 

	if($url_forward && empty($second)) 
	{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $url_forward");
	} 
	else 
	{
		if($url_forward) 
		{
			$message = "<a href=\"$url_forward\">$message</a><script>setTimeout(\"window.location.href ='$url_forward';\", ".($second*1000).");</script>";
		}
		
		$data['linkurl'] = $url_forward;
		$data['linkname'] = '';
		$data['title'] = $msg;
		$data['msg'] = $message;
		$data['second'] = $second;
 		
		//ob_start();
		
  		$CI->load->view('common/header',$data);	
  		$CI->load->view('common/left',$data);	
		$CI->load->view('msg');	
		$CI->load->view('common/footer');

		return;
		//if (ob_get_level() >  1)
		//{
		//@ob_end_clean();
		//@ob_end_flush(); 
		//}
		//exit();

  }
}

	/**
	 *  restart cgminer
	 */
	function doRestartCgminer2()
	{
		// find gpio thread
		//$command = 'sudo ls 2>&1';
		//$command = SUDO_COMMAND.'ps'.( SUDO_COMMAND === '' ? '' : ' -x' ).'|grep cgminer';
		 
		$command = SUDO_COMMAND.'ps aux|grep cgminer';
		exec( $command , $output ,$result);
		$pids = array();
		foreach ( $output as $r )
		{
			preg_match( '/\s*(\d+)\s*.*/' , $r , $match );
			if ( !empty( $match[1] ) ) $pids[] = $match[1];
		}
 
		exec( SUDO_COMMAND.'kill -9 '.implode( ' ' , $pids ) );

		 

		$cmd_start = 'sudo nohup  /home/pi/cgminer/cgminer --config /home/pi/cgminer.conf & 2>1&';
		//$command2 = '/usr/bin/lxterminal  --geometry=90x35 -e '.CGMINER_PATH.' --config '.CGMINER_CONFIG_ROOT.'  > /dev/null';
		 ///home/pi/cgminer/cgminer --config /home/pi/cgminer.conf

 		exec( $cmd_start , $output2 ,$result2);

 		var_dump($cmd_start);
 		var_dump($output2);
 		var_dump($result2);
		//echo '200';
		//exit;
		//showmsg('Restarting...')
		//return;
	}


 	function setTimezone($default) 
 	{
   	 $timezone = "";
    
   		//$command = SUDO_COMMAND.'date -R';
		//exec( $command , $output );

		// get current time
		//$cur = time();
		//$aryReturn['TIME'] = $cur;
   	 	$output = file_get_contents("/etc/localtime");
		// check
		if ( !empty( $output ) ) 
		{
			// match timezone
			preg_match( '/(?<=\<).*(?=\>)/' , $output , $match_zone );

			if ( !empty( $match_zone[0] ) ) 
			{

				$timezone  = 'Etc/'.$match_zone[0];
			}
			else 
				$timeznoe = 'Etc/UTC';
		}
    // echo $timezone;
     date_default_timezone_set($timezone);

	}

	function geturl($url){ 

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

	$result=curl_exec($ch); 
	curl_close($ch); 
	return $result;
	}
	
 
?>
