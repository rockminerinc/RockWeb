<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Show extends CI_Controller {

	/**
	 * Index Page for this controller.
	 */
	
    function __construct() 
	{
        parent::__construct();
 		$this->load->helper('functions');
  		$this->load->library('form_validation');
  
	}

 	public function index()
	{		 	

		$rack = $this->input->get('rack');

		$start=$this->input->get('start');
 	
		$end=$this->input->get('end');

		$save=$this->input->get('save');
 
 		$count = $end - $start+1;
 		if ($rack) {
 			$scan=1;
 		}
 		else
 			$scan=0;

 		$iplist=array();
 		$hashdatas = array();
 		$monitor_url = 'http://rockmonitor.sinaapp.com/';
		while ($count>0&&$scan ) {

			//$line=	fgets($handle);
			//$line=str_replace("\n","",$line);
			$ip = '192.168.'.$rack.'.'.$count;
			$url	=	'http://192.168.'.$rack.'.'.$count.':8000';
			$hashdatas[$ip] 	=$this->getMinerData($url);
			if($hashdatas[$ip]['hash'] != '<font color=red><b>timeout</b></font>')
			{
				//保存IP到云端
				$t1_data['ip'] = $ip;
				$t1_data['boards'] = $hashdatas[$ip]['num'];
				$t1_data['hash'] = $hashdatas[$ip]['hash'];
				if($save)
				$this->post_to_monitor($monitor_url,$t1_data);
			}
				//$iplist[] = $ip ;
			$count--;
		}



		//fclose($handle);
		//var_dump($hashdatas);

		//$this->data['sumary'] = request('summary');
		$this->data['title']= 'summary';
		$this->data['datas']= $hashdatas;
		$this->data['iplist']= json_encode($iplist);
		$this->load->view('common/header', $this->data);	
		//$this->load->view('common/leftt1');	
		$this->load->view('show');	
		
		$this->load->view('common/footer');		
		 
	}
 
	function post_to_monitor($monitor_url,$t1_data)
	{


		$miner_json = json_encode($t1_data);
  		
		$url=$monitor_url."index.php?c=home&m=gett1data&data=".$miner_json;
 
		$ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 1    //time out
					            ) 
					        ) 
			); 
		$re=file_get_contents($url, 0, $ctx);//($url);

		$commands_array = json_decode($re);

		foreach ($commands_array as $key => $value) {
			$ip = long2ip($value->ipint);

			switch ($value->command) {
				case 'reboot':
					$this->reboot_cmd_proc($ip);
					break;
				case 'setting':
					$data = (array) json_decode($value->para);
					//var_dump($data);
					$this->pool_cmd_proc($ip,$data);
				default:
					$this->reboot_cmd_proc($ip);
					break;
			}
 
			//$value->ipint;

		}
		var_dump($commands_array);

		//echo $re;
	}

function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
     } if(is_array($array)) {  
         foreach($array as $key=>$value) {  
             $array[$key] = object_array($value);  
             }  
     }  
     return $array;  
}  


	function test()
	{
		$config = array(
					'MURL'=>'us1.ghash.io',
					'MPRT'=>'3333',
					'USEF'=>'rockminer.6666',
					'GCLK'=>'260',
			
						);
		var_dump( json_encode($config));

	}

	function getMinerData($url)
	{
		$ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 1  //time out
					            ) 
					        ) 
			); 

		$url_statistics = $url.'/Statistics/';
		$htmDoc=@file_get_contents($url_statistics, 0, $ctx);//($url);

   	 	//$htmDoc = file_get_contents($url);  
 		//var_dump($htmDoc);
 		//exit;
 		//var_dump($htmDoc);
 		if($htmDoc)
 		{
	    	$content = strip_tags($this->getSubstr($htmDoc,'SettingsStatistics','*** (C)'));

	    	$arr = explode("&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspClock:270MHz&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp",$content);
	    	//var_dump($arr);
	    	$boards = explode('Board ', $arr[0]);
	    	$data['num'] = count($boards)-1;
	    	$data['hash']= @$this->getSubstr($arr[1],'Real performance:','GHs');


	    	//var_dump($data);
	    	 
    	}
    	else
    	{
    		$data['num']=0;
    		$data['hash']='<font color=red><b>timeout</b></font>';
    		
    	}

 
    	return $data;



	}



	function getSubstr($str, $leftStr, $rightStr)
	{
	    $left = strpos($str, $leftStr);
	    //echo '左边:'.$left;
	    $right = strpos($str, $rightStr,$left);
	    //echo '<br>右边:'.$right;
	    if($left < 0 or $right < $left) return '';
	    return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
	}


	function post_data()
	{
		$list_url = 'http://rockmonitor.sinaapp.com/?c=home&m=t1_iplist';
		$ip_array = json_decode(file_get_contents($list_url)) ;
		foreach ($ip_array as $key => $value) {
 				$monitor_url = 'http://rockmonitor.sinaapp.com/';
				$ip = $value;
				$url	=	'http://'.$ip.':8000';
				$hashdatas[$ip] 	=$this->getMinerData($url);
				if($hashdatas[$ip]['hash'] != '<font color=red><b>timeout</b></font>')
				{
					//保存IP到云端
					$t1_data['ip'] = $ip;
					$t1_data['boards'] = $hashdatas[$ip]['num'];
					$t1_data['hash'] = $hashdatas[$ip]['hash'];
 					$this->post_to_monitor($monitor_url,$t1_data);
				}
				echo $ip.'OK <br>';
			 
		}


		//var_dump($content);

	}


 	function reboot_cmd_proc($ip)
	{
		$data['update']='Update/Restart';
		$result = $this->post('http://'.$ip.':8000/Settings/Upload_Data', $data);
 		echo $result;
	}

	//$data array
 	function pool_cmd_proc($ip,$data)
	{
		//MURL MPRT USEF GCLK
		//$data = json_decode($json_data);
		$para = array();
		foreach ($data as $key => $value) {
			# code...
			$para[] = $key.'='.$value;
		}

		$post_data = implode('&',$para); 
		$post_data = $post_data.'&update=Update/Restart';
		//$data['update']='Update/Restart';
		var_dump($post_data);
		$result = $this->post('http://'.$ip.':8000/Settings/Upload_Data', $post_data);
 		echo $result;
	}


	function reboot()
	{

		$ip = $this->input->get('ip');
		$data['update']='Update/Restart';
		$result = $this->post('http://'.$ip.':8000/Settings/Upload_Data', $data);
		var_dump($result);
	}
	function post($url, $data)
	{	

		$post_data = implode('&',$data); 
		$post_data = $data;
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($post_data)));
	    $result = curl_exec($ch);
	    curl_close($ch);

	    return $result;
	}

}
 
 