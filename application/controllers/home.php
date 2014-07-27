<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 */
	
    function __construct() 
	{
        parent::__construct();
		$this->load->helper('cgminerapi');
		$this->load->helper('functions');
  		$this->load->library('form_validation');
  		$this->init();
  		setTimezone('GMT');

	}


 	private function init()
 	{


			if(!file_exists("/usr/share/nginx/www/data/hashrate.txt"))
			{
				exec('sudo touch /usr/share/nginx/www/data/hashrate.txt');
				exec('sudo chmod 777 /usr/share/nginx/www/data/hashrate.txt');
				$file_pointer = fopen('/usr/share/nginx/www/data/hashrate.txt','a');
				$head = "date,5m,15m,av\n";
				fwrite($file_pointer,$head);
				fclose($file_pointer);
			}

			/*
			if(!file_exists("/usr/share/nginx/www/data/mac.txt"))
			{
				exec('sudo touch /usr/share/nginx/www/data/mac.txt');
				exec('sudo chmod 777 /root/.cubian-emac');
				$file_pointer = fopen('/root/.cubian-emac','w');
				$file_pointer = fopen('/root/.cubian-emac','w');
				$newmac = $this->generatemac();
				file_put_contents($file_pointer,$newmac);
				//fwrite($file_pointer,$newmac);
				//fclose($file_pointer);
				//@exec('sudo reboot');

			}*/


 	}

	public function index()
	{

			$lines = file('/etc/network/interfaces');
			foreach ($lines as $line_num => $line) 
			{
				$address = strstr($line, 'address');
				if($address)
				{
					$address_arr = explode(" ",$address);
					$this->data['ip_adress']=$address_arr['1'];
				}
			}

		$this->data['debug']  = $this->input->get('debug');
		$this->data['sumary'] = request('summary');
		$this->data['pools'] = request('pools');
		$this->data['devss'] = request('devs');

		$this->data['title']= $this->data['ip_adress'];
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');	
		$this->load->view('home');	
		
		$this->load->view('common/footer');		
		 
	}
	

	public function devs()
	{
		$this->data['r'] = request('devs');

 		$this->data['title']= 'devs';
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');	
		$this->load->view('devs');	
		
		$this->load->view('common/footer');	
	}

	private function generatemac()
	{

			$strTmp = '1234567890abcdef';
			$mac_str_1_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_1_p2 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_2_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_2_p2 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_3_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_3_p2 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_4_p1 = $strTmp{rand(0, strlen($strTmp)-1)};
			$mac_str_4_p2 = $strTmp{rand(0, strlen($strTmp)-1)};

			$mac_str_1 = $mac_str_1_p1.$mac_str_1_p2;
			$mac_str_2 = $mac_str_2_p1.$mac_str_2_p2;
			$mac_str_3 = $mac_str_3_p1.$mac_str_3_p2;
			$mac_str_4 = $mac_str_4_p1.$mac_str_4_p2;

			$aryMacData = explode( ':' , $old_mac );
			$aryMacData[count( $aryMacData )-4] = $mac_str_1;
			$aryMacData[count( $aryMacData )-3] = $mac_str_2;
			$aryMacData[count( $aryMacData )-2] = $mac_str_3;
			$aryMacData[count( $aryMacData )-1] = $mac_str_4;

			$new_mac = implode( ':' , $aryMacData );
			$newmac = '70:00'.$new_mac;
			return $newmac;
			//@exec("sudo fconfig eth0 down");
			//@exec("ifconfig eth0 hw ether ".$newmac);
			//@exec("ifconfig eth0 up ");

	}





	public function upgrade2()
	{
		//$this->data['r'] = request('devs');
		$this->data['title']= 'Upgrade';
			$this->form_validation->set_rules('version', 'version', 'trim|xss_clean');
			$this->form_validation->set_rules('latest_version', 'latest_version', 'trim|xss_clean');
			$this->form_validation->set_rules('step', 'step', 'trim|xss_clean');
		if($this->form_validation->run())
		{
			//showmsg('dd',WEB_ROOT.'?c=home&m=upgrade','20');	

			$version = $this->input->post('version', TRUE);
			$latest_version = $this->input->post('latest_version', TRUE);
			$step = $this->input->post('step', TRUE);
			//showmsg($step,WEB_ROOT.'?c=home&m=upgrade','20');	
			//echo $step;
			if($step=='1')
			{
				#download file
				@exec("sudo rm -rf /home/pi/temp/*");
				if(!is_dir("/home/pi/temp/"))
				{
					@exec("mkdir /home/pi/temp/");
					@exec("sudo chmod 777  /home/pi/temp/");
					 
				}

				$command='wget '.UPGRADE_PATH.'rockweb_'.$latest_version.'.zip -O /home/pi/temp/rockweb_'.$latest_version.'.zip &';

				exec( $command , $output ,$result);

					showmsg('Downloading...Please Wait...20 seconds!',WEB_ROOT.'?c=home&m=upgrade','20');	


			}
			elseif ($step=='2') {
				# upgrade...

				$command 	= "sudo unzip -o /home/pi/temp/rockweb_".$latest_version.".zip -d /usr/share/nginx/www/ &";

				exec( $command , $output ,$result);
				var_dump($output);
				//showmsg('Upgrading...Please Wait...20 seconds!',WEB_ROOT,'20');	
				 
			}


		}
		else
		{
			exec("sudo chmod -R 777 /usr/share/nginx/www/*");
			exec("sudo chown -R pi /usr/share/nginx/www/*");

			$version_url	=	"https://raw.githubusercontent.com/rockminerinc/RockWeb/master/rockweb.ver";
			$this->data['latest_version'] 		=	file_get_contents($version_url);
			$this->data['current_version'] 		=	CURRENT_VERSION;
			//$this->data['Downloaded Version'] 		=	basename("/home/pi/temp/rockweb_".$this->data['latest_version'].".zip");
			//echo $version ;
			if(CURRENT_VERSION<$this->data['latest_version'])
				$this->data['need_upgrade']=1;
			else
				$this->data['need_upgrade']=0;
			if (CURRENT_VERSION<$this->data['latest_version']) 
			{
				if(file_exists("/home/pi/temp/rockweb_".$this->data['latest_version'].".zip"))
				{
					$this->data['step']="2";
					$this->data['step_name']="Start Upgrade";
				}
				else
				{
					$this->data['step']="1";
					$this->data['step_name']="Download";
				}				
				# code...
			}
			else
			{
					$this->data['step']="0";
					$this->data['step_name']="No need to Upgrade";			
			}
			///echo $version ;
			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('upgrade');	
			
			$this->load->view('common/footer');	

		}		

	}

	public function setting()
	{
			if(!file_exists("/usr/share/nginx/www/data/setting.txt"))
			{
				exec('touch /usr/share/nginx/www/data/setting.txt');
				exec('sudo chmod 777 /usr/share/nginx/www/data/setting.txt');
 
			}
			else
			{
			
					//$file_pointer2 = fopen('/usr/share/nginx/www/data/realtime_hashrate.txt','w');

				$this->data['title']= 'setting';
				$this->form_validation->set_rules('dev_name', 'dev_name', 'trim|xss_clean');
		 
				$this->form_validation->set_rules('monitor_url', 'monitor_url', 'trim|xss_clean');
				$this->form_validation->set_rules('btckan_id', 'btckan_id', 'trim|xss_clean');

				if($this->form_validation->run())
				{
					$file_pointer = fopen('/usr/share/nginx/www/data/setting.txt','w');
					if($file_pointer === false)
					{
						exec('sudo chmod 777 /usr/share/nginx/www/data/setting.txt');
						$file_pointer = fopen('/usr/share/nginx/www/data/setting.txt','w');

					}


					$device['dev_name'] = $this->input->post('dev_name', TRUE);
					$device['monitor_url'] =$this->input->post('monitor_url', TRUE);
					$device['btckan_id'] =$this->input->post('btckan_id', TRUE);

					$data=json_encode($device);
					fwrite($file_pointer,$data);
					fclose($file_pointer);
					showmsg('Settings updated OK!');

				}
				else
				{
					$filename = "/usr/share/nginx/www/data/setting.txt";
					  $ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 1    //设置超时
					            ) 
					        ) 
					    ); 

					$contents= file_get_contents($filename, 0, $ctx); 

					//$contents = fread($file_pointer, filesize ($filename));
					//var_dump($contents);
					$this->data =	json_decode($contents);
					//$this->data['dev_name'] = getconfig("./data/setting.inc.php", "dev_name", $type="string");
					//$this->data['dev_id'] = getconfig("./data/setting.inc.php", "dev_id", $type="string");
					//$this->data['lang'] = getconfig("./data/setting.inc.php", "lang", $type="string");
					//$this->data['timezone'] = getconfig("./data/setting.inc.php", "timezone", $type="string");
					//$this->data['monitor_url'] = getconfig("./data/setting.inc.php", "monitor_url", $type="string");

					$this->load->view('common/header', $this->data);	
					$this->load->view('common/left');	
					$this->load->view('setting');	
					
					$this->load->view('common/footer');	

				}

			}
	}


	public function setting2()
	{
		//echo 'dd';
		//$var = updateconfig("./data/setting.inc.php", "kkk",'111');//
		//var_dump($var);
		@exec('sudo chmod 777 /usr/share/nginx/www/data/setting.inc.php');

		$this->data['title']= 'setting';
		$this->form_validation->set_rules('dev_name', 'dev_name', 'trim|xss_clean');
 
		$this->form_validation->set_rules('monitor_url', 'monitor_url', 'trim|xss_clean');

		if($this->form_validation->run())
		{
			$device['dev_name'] = $this->input->post('dev_name', TRUE);
			//$device['dev_id'] =$this->input->post('dev_id', TRUE);
			//$device['lang'] =$this->input->post('lang', TRUE);
			//$device['timezone'] =$this->input->post('timezone', TRUE);
			$device['monitor_url'] =$this->input->post('monitor_url', TRUE);
			updateconfig("./data/setting.inc.php", "dev_name",$device['dev_name']);//
			//updateconfig("./data/setting.inc.php", "dev_id",$device['dev_id']);//
			//updateconfig("./data/setting.inc.php", "lang",$device['lang']);//
			//updateconfig("./data/setting.inc.php", "timezone",$device['timezone']);//
			updateconfig("./data/setting.inc.php", "monitor_url",$device['monitor_url']);//
			showmsg('Settings updated OK!');

		}
		else
		{
			$this->data['dev_name'] = getconfig("./data/setting.inc.php", "dev_name", $type="string");
			//$this->data['dev_id'] = getconfig("./data/setting.inc.php", "dev_id", $type="string");
			//$this->data['lang'] = getconfig("./data/setting.inc.php", "lang", $type="string");
			//$this->data['timezone'] = getconfig("./data/setting.inc.php", "timezone", $type="string");
			$this->data['monitor_url'] = getconfig("./data/setting.inc.php", "monitor_url", $type="string");

			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('setting');	
			
			$this->load->view('common/footer');	

		}



	}


	public function post_to_monitor()
	{

 

		$data['ip']			= 	getip();
		$data['mac']			= 	getmac();
		//var_dump($data['mac']);
		$data['ipint']			= 	ip2long($data['ip']);
 
		$filename = "/usr/share/nginx/www/data/setting.txt";
		$ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 1    //设置超时
					            ) 
					        ) 
			); 
		$file_data = file_get_contents($filename, 0, $ctx);
		
		$contents=json_decode($file_data) ;
 		$server =$contents->monitor_url;

 		if(empty($server))
 		{
 			echo 'server is blank';
 			exit;
 		}

		$data['dev_name']	= $contents->dev_name;

 
		$data['dev_num']	= 	dev_num();
		$sumary = request('summary');
		$data['asc_elapsed']  		= 	$sumary['SUMMARY']['Elapsed'];//$data_array[0];
		$data['asc_mhs_5s']  	= 	$sumary['SUMMARY']['MHS 5s'];//$data_array[0];
		$data['asc_mhs_5m']  	= 	$sumary['SUMMARY']['MHS 5m'];//$data_array[1];
		$data['asc_mhs_15m']  	= 	$sumary['SUMMARY']['MHS 15m'];//$data_array[2];
		$data['asc_mhs_av']  	= 	$sumary['SUMMARY']['MHS av'];
		$data['asc_last_share_time']  	= 	$sumary['SUMMARY']['Last getwork'];
 
		$data['event_time']  	=	time();
  
		$miner_data['ip'] = $data['ip'];
		$miner_data['mac'] = $data['mac'];
		$miner_data['ipint'] =$data['ipint'];
		$miner_data['dev_name'] =$data['dev_name'];
		$miner_data['dev_num'] =$data['dev_num'];
		$miner_data['asc_mhs_5s'] =$data['asc_mhs_5s'];
		$miner_data['asc_mhs_5m'] =$data['asc_mhs_5m'];
		$miner_data['asc_mhs_15m'] =$data['asc_mhs_15m'];
		$miner_data['asc_mhs_av'] =$data['asc_mhs_av'];
		$miner_data['asc_last_share_time'] =$data['asc_last_share_time'];
		$miner_data['event_time'] =$data['event_time'];
		$miner_data['asc_elapsed'] =$data['asc_elapsed'];


		$devices = request('devs');

		foreach ($devices as $key => $dev) {
			if($key=="STATUS")
				continue;
			foreach ($dev as $key2 => $value) {
				if($key2=="Temperature")
					$temp_arry[]=$value;
			}
			
			# code...
		}

		$max_key = array_search(max($temp_arry),$temp_arry); 

		$miner_data['temperature'] = floor($temp_arry[$max_key]);//max Temperature

 		$miner_json = json_encode($miner_data);
  		
		$url=$server."index.php?c=home&m=getdata&data=".$miner_json;
		$btckan_url="http://localhost/index.php?c=btckan&m=send_status";
 		//var_dump($btckan_url);
		$ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 5    //time out
					            ) 
					        ) 
			); 
		$re=file_get_contents($url, 0, $ctx);//($url);
		//$re=geturl($url);//($url);
		$re2=geturl($btckan_url);//($url);
 		var_dump($re);
		//echo $re2;
  
	}

	public function pools()
	{

		$this->data['title']= 'pools';
		$this->form_validation->set_rules('pool_url1', 'pool_url1', 'trim|xss_clean');
		$this->form_validation->set_rules('pool_worker1', 'pool_worker1', 'trim|xss_clean');
		$this->form_validation->set_rules('pool_passwd1', 'pool_passwd1', 'trim|xss_clean');
		$this->form_validation->set_rules('pool_url2', 'pool_url2', 'trim|xss_clean');
		$this->form_validation->set_rules('pool_worker2', 'pool_worker2', 'trim|xss_clean');
		$this->form_validation->set_rules('frequency', 'frequency', 'trim|xss_clean');
		$this->form_validation->set_rules('fanspeed', 'fanspeed', 'trim|xss_clean');

		if($this->form_validation->run())
		{

			$pool1_datas['url'] = $this->input->post('pool_url1', TRUE);
			$pool1_datas['user'] =$this->input->post('pool_worker1', TRUE); 
			$pool1_datas['pass'] =$this->input->post('pool_passwd1', TRUE);
			
			$pool2_datas['url'] =$this->input->post('pool_url2', TRUE);
			$pool2_datas['user'] =$this->input->post('pool_worker2', TRUE);
			$pool2_datas['pass'] =$this->input->post('pool_passwd2', TRUE);
			//$pool2_datas['freq'] =$this->input->post('freq', TRUE);
			$frequency =$this->input->post('frequency', TRUE);
			$fanspeed =$this->input->post('fanspeed', TRUE);

			$content['pools']=array($pool1_datas,$pool2_datas); 
			$content['api-listen']=true;
			$content['api-port']='4028';
			$content['expiry']='120';
			$content['failover-only']=true;
			$content['log']='5';
			$content['no-pool-disable']=true;
			$content['queue']='2';
			$content['scan-time']='60';
			$content['worktime']=true;
			$content['shares']='0';
			$content['kernel-path']='/usr/local/bin';
			$content['api-allow']='W:0/0';
			$content['icarus-options']='115200:1:1';
			$content['api-description']='cgminer 4.3.3';
			$content['hotplug']='5';

			if($frequency	==	'')
				$frequency=320;

			if($fanspeed	==	'')
				$fanspeed=0;

			$content['rmu-auto']=$frequency;
			$content['rmu-fan']=$fanspeed;

			$data = json_encode($content);
			$data=str_replace("\\/", "/",  $data);
			$file_pointer = fopen('/etc/cgminer.conf','w');
			if($file_pointer === false)
			{
				//showmsg('/etc/cgminer.conf open error');
				exec('sudo chmod 777 /etc/cgminer.conf');
				$file_pointer = fopen('/etc/cgminer.conf','w');
			}
			else
			{
				fwrite($file_pointer,$data);
				fclose($file_pointer);
				showmsg('Settings updated OK! Must reboot to apply the new settings.','?c=home&m=reboot');
			}
 

		}
		else
		{
			$filename = "/etc/cgminer.conf";

		    $handle = fopen($filename, "r");

			if($handle === false)
			{
				//showmsg('/home/pi/cgminer.conf open error');
				exec('touch /etc/cgminer.conf');
				exec('sudo chmod 777 /etc/cgminer.conf');
				$handle = fopen($filename, "r");
			}

		    $contents = fread($handle, filesize ($filename));
		    fclose($handle);
			
			$data_arr = json_decode($contents);
			$data_arr2 = json_decode($contents,true);
			$pools_data=$data_arr->pools;
			$this->data['data_pool1'] = $pools_data[0];
			$this->data['data_pool2'] = $pools_data[1];
			
			$this->data['rmu_freq'] = $data_arr2['rmu-auto'];
			$this->data['rmu_fan'] = $data_arr2['rmu-fan'];
			//var_dump($data_arr2['rmu-auto']);
			$this->data['r'] = request('pools');
			
			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('pools');	
			
			$this->load->view('common/footer');	
		}

	}
	
	public function setip()
	{
		$this->data['title']= 'setip';

		$this->form_validation->set_rules('JMIP', 'JMIP', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('JMSK', 'JMSK', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('JGTW', 'JGTW', 'trim|required|xss_clean');	


		if($this->form_validation->run())
		{
			$content ="auto lo
auto eth0
allow-hotplug eth0
iface lo inet loopback
iface eth0 inet static\n";

			$JMIP=$this->input->post('JMIP', TRUE);
			$JMSK=$this->input->post('JMSK', TRUE);
			$JGTW=$this->input->post('JGTW', TRUE);

			$content .= 'address '.$JMIP."\n";
			$content .= 'netmask '.$JMSK."\n";
			$content .= 'gateway '.$JGTW."\n";
			$newmac = $this->generatemac();
			$content .= 'hwaddress ether '.$newmac."\n";


			$file_pointer = @fopen('/etc/network/interfaces','w'); 
			if($file_pointer === false)
			{

				showmsg('/etc/network/interfaces open error');
				exec('sudo chmod 777 /etc/network/interfaces');
			}   
			else
			{

				fwrite($file_pointer,$content);
				fclose($file_pointer);
				exec('sudo /etc/init.d/networking restart');
				showmsg('IP update OK!');
			}    

		}
		else
		{
			$lines = file('/etc/network/interfaces');
			foreach ($lines as $line_num => $line) 
			{
				$address = strstr($line, 'address');
				if($address)
				{
					$address_arr = explode(" ",$address);
					if($address_arr['1']!='ether')
					$this->data['ip_adress']=$address_arr['1'];
				}
				
				//mask
				$Mask = strstr($line, 'netmask');
				if($Mask)
				{
					$Mask_arr = explode(" ",$Mask);
					$this->data['mask']=$Mask_arr['1'];
				}
				//gateway
				$gateway = strstr($line, 'gateway');
				if($gateway)
				{
				$gateway_arr = explode(" ",$gateway);
				$this->data['gateway_id']=$gateway_arr['1'];
				//echo $gateway_id;
				}


				$macaddr = strstr($line, 'hwaddress');
				if($macaddr)
				{
				//$gateway_arr = explode(" ",$macaddr);
				$this->data['mac']=end($macaddr); 
				//echo $gateway_id;
				}
 
				
			}

 				/*
				$command = 'sudo cat /root/.cubian-emac';
    			@exec( $command , $output ,$result);
 				$this->data['mac']= $output[0];*/
 

			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('setip');	
			
			$this->load->view('common/footer');	

		}

		
	}
	
	public function setdns()
	{
		$this->data['title']= 'setdns';
		$this->form_validation->set_rules('PDNS', 'PDNS', 'trim|xss_clean');	
		$this->form_validation->set_rules('SDNS', 'SDNS', 'trim|xss_clean');	
		if($this->form_validation->run())
		{
			//showmsg('1');
			$PDNS=$this->input->post('PDNS', TRUE);
			$SDNS=$this->input->post('SDNS', TRUE);
			$content = 'nameserver '.$PDNS."\n";
			$content .= 'nameserver '.$SDNS."";

			$file_pointer = fopen('/etc/resolv.conf','w'); 
			if($file_pointer === false)
			{
				showmsg('/etc/resolv.conf open error');
				exec('sudo chmod 777 /etc/resolv.conf');
			}
			else
			{
				fwrite($file_pointer,$content);
				fclose($file_pointer);
				exec('sudo /etc/init.d/networking restart');
				showmsg('DNS updated OK!');
			}

		}
		else
		{
				$lines = file('/etc/resolv.conf');
				foreach ($lines as $line_num => $line) 
				{
					$nameserver = strstr($line, 'nameserver');
					if($nameserver)
					{
						$address_arr = explode(" ",$nameserver);
						$this->data['nameservers'][]=$address_arr['1'];
					}

				}
				$this->load->view('common/header', $this->data);	
				$this->load->view('common/left');	
				$this->load->view('setdns');	
				$this->load->view('common/footer');	

		}

	}
	public function setpools()
	{
		$this->data['title']= 'setpools';
		$this->form_validation->set_rules('setpools', 'setpools', 'trim|xss_clean');	
		if($this->form_validation->run())
		{

			$pool1_datas['url'] = $this->input->post('pool_url1', TRUE);
			$pool1_datas['user'] =$this->input->post('pool_worker1', TRUE); 
			$pool1_datas['pass'] =$this->input->post('pool_passwd1', TRUE);
			
			$pool2_datas['url'] =$this->input->post('pool_url2', TRUE);
			$pool2_datas['user'] =$this->input->post('pool_worker2', TRUE);
			$pool2_datas['pass'] =$this->input->post('pool_passwd2', TRUE);
			//$pool2_datas['freq'] =$this->input->post('freq', TRUE);

			$content['pools']=array($pool1_datas,$pool2_datas); 
			$content['api-listen']=true;
			$content['api-port']='4028';
			$content['expiry']='120';
			$content['failover-only']=true;
			$content['log']='5';
			$content['no-pool-disable']=true;
			$content['queue']='2';
			$content['scan-time']='60';
			$content['worktime']=true;
			$content['shares']='0';
			$content['kernel-path']='/usr/local/bin';
			$content['api-allow']='W:0/0';
			$content['icarus-options']='115200:1:1';
			$content['api-description']='cgminer 4.3.0';
			$content['hotplug']='5';
			$content['rmu-auto']='320';


			
			//echo   
			$data = json_encode($content);
			$data=str_replace("\\/", "/",  $data);
			$file_pointer = fopen('/etc/cgminer.conf','w');
			if($file_pointer === false)
			{
				showmsg('/etc/cgminer.conf open error');
				exec('sudo chmod 777 /etc/cgminer.conf');
			}
			else
			{
				fwrite($file_pointer,$data);
				fclose($file_pointer);
				exec('sudo service cgminer stop');
				sleep(3);
				exec('sudo service cgminer start');
				showmsg('Settings updated OK! ','?c=home&m=index');
			}
 

		}
		else
		{
			$filename = "/etc/cgminer.conf";
		    $handle = fopen($filename, "r");

		    $contents = fread($handle, filesize ($filename));
		    fclose($handle);
			
			$data_arr = json_decode($contents);
			$data_arr2 = json_decode($contents,true);
			$pools_data=$data_arr->pools;
			$this->data['data_pool1'] = $pools_data[0];
			$this->data['data_pool2'] = $pools_data[1];
 
			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('setpools');	
			$this->load->view('common/footer');		

		}

	}

 	public function setTimezone()
 	{
		$this->data['title']= 'setTimezone';

        if(!file_exists("/usr/share/nginx/www/data/timezone.txt"))
        {
                exec('touch /usr/share/nginx/www/data/timezone.txt');
                exec('sudo chmod 777 /usr/share/nginx/www/data/timezone.txt');
 
        }




		$this->form_validation->set_rules('timezone', 'timezone', 'trim|xss_clean');	
		if($this->form_validation->run())
		{
			exec('sudo rdate -s '.TIME_SERVER);//同步时钟

            $file_pointer = fopen('/usr/share/nginx/www/data/timezone.txt','w');
            if($file_pointer === false)
            {
                exec('sudo chmod 777 /usr/share/nginx/www/data/timezone.txt');
                $file_pointer = fopen('/usr/share/nginx/www/data/timezone.txt','w');

            }

			$timezone = $this->input->post('timezone', TRUE);
			$timezone_url = '/usr/share/zoneinfo/Etc/'.$timezone;
            //$data=json_encode($device);
            fwrite($file_pointer,$timezone);
            fclose($file_pointer);


			//cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
			exec('sudo cp '.$timezone_url.' /etc/localtime');
			exec('sudo rm -rf /usr/share/nginx/www/data/hashrate.txt');
			//exec('sudo  echo /dev/null > /usr/share/nginx/www/data/hashrate.txt');
			showmsg('Timezone set OK!');
		}
		else
		{
			$this->data['time'] = date('Y-m-d H:i:s',mktime());
			$this->data['timezone'] = date_default_timezone_get();
			//echo $this->data['timezone'] ;
			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('set_timezone');	
			$this->load->view('common/footer');		

		} 		

 	}

	public function CheckStatus()
	{
		$this->data['title']= 'CheckStatus';
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');
		$this->load->view('check');	
		$this->load->view('common/footer');	
	}

	public function reboot()
	{

		$this->form_validation->set_rules('reboot', 'reboot', 'trim|required|xss_clean');	

		if($this->form_validation->run())
		{

			//$exec('sudo reboot');
			$filename = "/usr/share/nginx/www/data/timezone.txt";
			 $ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 1    //设置超时
					            ) 
					        ) 
					    ); 

			$timezone= file_get_contents($filename, 0, $ctx); 
			$timezone_url = '/usr/share/zoneinfo/Etc/'.$timezone;
			//cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
			exec('sudo cp '.$timezone_url.' /etc/localtime');

			
			$command = 'sudo reboot 2>&1';

			exec( $command , $output ,$result);

			//var_dump($command);
			//var_dump($output);
			//var_dump($result);
			showmsg('Rebooting...Wait for 45s...',WEB_ROOT,'45');	


		}
		else
		{
		$this->data['title']= 'reboot';
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');	
		$this->load->view('reboot');	
		$this->load->view('common/footer');			

		}

		
		
	}

	public function upgrade()
	{


		$this->form_validation->set_rules('upgrade', 'upgrade', 'trim|required|xss_clean');	

		if($this->form_validation->run())
		{

 
			$command = 'sudo /root/upgrade.sh &';

			exec( $command , $output ,$result);
 			
			showmsg('Wait for upgrading...',WEB_ROOT,'45');	



		}
		else
		{
		$this->data['title']= 'Upgrade';
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');	
		$this->load->view('upgrade');	
		$this->load->view('common/footer');			

		}

		
		
	}


	public function test()
	{
		$r= request('privileged');
		var_dump($r);
	}

	public function usbstats()
	{
		$r= request('usbstats');
		var_dump($r);
	}

	public function stats()
	{
		$r= request('stats');
		var_dump($r);
	}



 

	public function switchpool()
	{
		$pid=$this->input->get('id', TRUE);
		request('privileged');
		if($pid != '')
		$this->data['r'] = request('switchpool|'.$pid);
		//var_dump($this->data['r']);
		showmsg('Pools switched OK!','?c=home&m=pools','1');
		//$this->load->view('common/header', $this->data);	
		//$this->load->view('common/left');	
		//$this->load->view('restartCgminer');	
		
		//$this->load->view('common/footer');		
	}

	public function save()
	{
		request('privileged');
		$this->data['r'] = request('save|/home/pi/cgminer.conf');
		var_dump($this->data['r']);
		//$this->load->view('common/header', $this->data);	
		//$this->load->view('common/left');	
		//$this->load->view('restartCgminer');	
		
		//$this->load->view('common/footer');		
	}

	//miners num
	public function check_lsusb()
	{
		// lsusb command
		$command = 'sudo lsusb';
		exec( $command , $output );

		$aryReturn = array( 'BLADES'=>0 );

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
					$aryReturn['BLADES'] ++;
				}
			}
		}

		echo json_encode( $aryReturn );
		exit;
	}


	/**
	 * timezone
	 */
	public function check_time_zone()
	{
		$aryReturn = array( 'ZONE'=>0 );

		// date command
		$command = SUDO_COMMAND.'date -R';
		exec( $command , $output );


		// check
		if ( !empty( $output ) && count( $output ) > 0 ) 
		{

				$aryReturn['ZONE'] = $output[0];

		}

		echo json_encode( $aryReturn );
		exit;
	}
 

	/**
	 * check network
	 */
	public function check_network()
	{
		$aryReturn = array( 'NET'=>0 , 'NET_DELAY'=>0 );

		// ping network
		$command_network = SUDO_COMMAND.'ping -c 1 -w 5 www.baidu.com';
		//$command_wiibox = SUDO_COMMAND.'ping -c 1 -w 5 www.google.com';

		exec( $command_network , $output_network );
		//exec( $command_wiibox , $output_wiibox );

		foreach ( $output_network as $data )
		{
			preg_match( '/.*time=(.*?)\sms.*/' , $data , $match_network );
			if ( !empty( $match_network[0] ) && !empty( $match_network[1] ) )
			{
				$aryReturn['NET'] = 1;
				$aryReturn['NET_DELAY'] = $match_network[1];
			}
		}
	


		echo json_encode( $aryReturn );
		exit;
	}

	/**
	 * about ip
	 */
	public function check_ip()
	{
		// get ip
		$os = DIRECTORY_SEPARATOR=='\\' ? "windows" : "linux";
		$mac_addr = new CMac( $os );
		$ip_addr = new CIp( $os );

		$aryReturn = array( 'IP'=>0 , 'MAC'=>0 );

		$aryReturn['IP'] = $ip_addr->ip_addr;
		$aryReturn['MAC'] = $mac_addr->mac_addr;

		echo json_encode( $aryReturn );
		exit;
	}

	public function check_version()
	{
 		$aryReturn = array( 'VERSION'=>0 );
		$aryReturn['VERSION'] = CURRENT_VERSION;

		echo json_encode( $aryReturn );
		exit;
	}

	public function SaveHashrate()
	{
			$savedata=0;

			if(!$savedata)
			exit;

			if(!file_exists("/usr/share/nginx/www/data/hashrate.txt"))
			{
				exec('touch /usr/share/nginx/www/data/hashrate.txt');
				exec('sudo chmod 777 /usr/share/nginx/www/data/hashrate.txt');
				$file_pointer = fopen('/usr/share/nginx/www/data/hashrate.txt','a');
				$head = "date,5m,15m,av\n";
				fwrite($file_pointer,$head);
				fclose($file_pointer);
			}
			else
			{
				$file_pointer = fopen('/usr/share/nginx/www/data/hashrate.txt','a');
				if($file_pointer === false)
				{
					exec('sudo chmod 777 /usr/share/nginx/www/data/hashrate.txt');

				}

					$file_pointer = fopen('/usr/share/nginx/www/data/hashrate.txt','a');
					//$file_pointer2 = fopen('/usr/share/nginx/www/data/realtime_hashrate.txt','w');

					$sumary = request('summary');

						 $date=date('Y-m-d H:i:s',time());
		 
						 $avg = $sumary['SUMMARY']['MHS av'];
						 $Time_5m = $sumary['SUMMARY']['MHS 5m'];
						 $Time_15m = $sumary['SUMMARY']['MHS 15m'];
						 $head = "date,5m,15m,av\n";
						 $data=$date.','.$Time_5m.','.$Time_15m.','.$avg."\n";
						 fwrite($file_pointer,$data);
						 fclose($file_pointer);


			}

	}


}
 
 