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

function curl_access($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	if ($output === FALSE) {
	    return "cURL Error: " . curl_error($ch);
	}
	curl_close($ch);
	return $output;
}


function getip()
{
		@exec("ifconfig -a", $return_array);

		$temp_array = array();
		foreach ( $return_array as $value )
		{
			if ( preg_match_all( "/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i", $value, $temp_array ) )
			{
				$tmpIp = $temp_array[0];
				if ( is_array( $tmpIp ) ) $tmpIp = array_shift( $tmpIp );
				$ip_addr = $tmpIp;
				break;
			}
		}

		unset($temp_array);
		return $ip_addr;

}


function getmac()
{
		@exec("ifconfig -a", $return_array);

		$temp_array = array();
		foreach ( $return_array as $value )
		{
			if ( preg_match( "/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp_array ) )
			{
				$mac_addr = $temp_array[0];
				break;
			}
		}

		unset($temp_array);
		return $mac_addr;		
 }


//配置文件数据值获取。
//默认没有第三个参数时，按照字符串读取提取''中或""中的内容
//如果有第三个参数时为int时按照数字int处理。
function getconfig($file, $ini, $type="string")
{
	if ($type=="int")
	{
		$str = file_get_contents($file);
		$config = preg_match("/" . $ini . "=(.*);/", $str, $res);
		Return $res[1];
	}
	else
	{
		$str = file_get_contents($file);
		$config = preg_match("/" . $ini . "=\"(.*)\";/", $str, $res);
		if($res[1]==null)
		{	
			$config = preg_match("/" . $ini . "='(.*)';/", $str, $res);
		}
		Return $res[1];
	}
} 

//配置文件数据项更新
//默认没有第四个参数时，按照字符串读取提取''中或""中的内容
//如果有第四个参数时为int时按照数字int处理。
function updateconfig($file, $ini, $value,$type="string")
{

	$str = file_get_contents($file);
	$str2="";
	if($type=="int") 
	{	
		$str2 = preg_replace("/" . $ini . "=(.*);/", $ini . "=" . $value . ";", $str);
	}
	else 
	{
		$str2 = preg_replace("/" . $ini . "=(.*);/", $ini . "=\"" . $value . "\";", $str);
	}
	
	file_put_contents($file, $str2);
} 


function dev_num()
{
		// lsusb command
		$command = 'sudo lsusb';
		@exec( $command , $output );

		$dev_num=0;

		// check result
		if ( !empty( $output ) && count( $output ) > 0 )
		{
			// run command success
			//$aryReturn['COMMAND'] = 1;
			// find mill
			foreach ( $output as $usb )
			{
				//Bus 001 Device 004: ID 10c4:ea60 Cygnal Integrated Products, Inc. CP210x UART Bridge / myAVR mySmartUSB light


				preg_match( '/.*Bus\s(\d+)\sDevice\s(\d+).*Cygnal\sIntegrated\sProducts.*CP210x\sUART\sBridge.*/' , $usb , $match_usb );
				if ( !empty( $match_usb[1] ) && !empty( $match_usb[2] ) )
				{
					$dev_num ++;
				}
			}
		}
		return $dev_num;

	}

 

function time_tran($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>' Year',
        '2592000'=>' month',
        '604800'=>' week',
        '86400'=>' day',
        '3600'=>' hour',
        '60'=>' minutes',
        '1'=>' seconds'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.' ago ';
        }
    }
}

function timediff($timediff)
{
 
 
     $days = intval($timediff/86400);
     $remain = $timediff%86400;
     $hours = intval($remain/3600);
     $remain = $remain%3600;
     $mins = intval($remain/60);
     $secs = $remain%60;

     if($days)
     	$res = $days.' d ';

     if($hours)
     	$res .= $hours.' h ';


     if($mins)
     	$res .= $mins.' m ';

     if($secs)
     	$res .= $secs.' s ';
   
     //if
     //$res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
     return $res;
}


?>
