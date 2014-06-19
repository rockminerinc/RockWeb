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

 		//$command = SUDO_COMMAND.'date -R';
		//exec( $command , $output );

		// check
		//if ( !empty( $output ) && count( $output ) > 0 ) 
		//{
			// match timezone
			//preg_match( '/\+0800/' , $output[0] , $match_zone );

			//if ( !empty( $match_zone[0] ) ) 
			//{
				//$zone = substr($output[0], -5);
			//}
		//} 
		//echo $zone;		
  		//date_default_timezone_set();
  		setTimezone('GMT');

	}


 

	public function index()
	{
		$this->data['debug']  = $this->input->get('debug');
		$this->data['sumary'] = request('summary');
		$this->data['pools'] = request('pools');
		$this->data['devss'] = request('devs');
		$this->data['title']= 'summary';
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


	public function upgrade()
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
				if(!is_dir("/home/pi/temp/"))
				{
					exec("mkdir /home/pi/temp/");
					exec("sudo chmod 777  /home/pi/temp/");
					 
				}
				$command='wget '.UPGRADE_PATH.'rockweb_'.$latest_version.'.zip -O /home/pi/temp/rockweb_'.$latest_version.'.zip';

				exec( $command , $output ,$result);

					showmsg('Downloading...Please Wait...20 seconds!',WEB_ROOT.'?c=home&m=upgrade','20');	


			}
			elseif ($step=='2') {
				# upgrade...

				$command 	= "sudo unzip -o /home/pi/temp/rockweb_".$latest_version.".zip -d /usr/share/nginx/www/";

				exec( $command , $output ,$result);
				
				showmsg('Upgrading...Please Wait...20 seconds!',WEB_ROOT,'20');	
				 
			}


		}
		else
		{
			exec("sudo chmod -R 777 /usr/share/nginx/www/*");
			exec("sudo chown -R pi /usr/share/nginx/www/*");

			$version_url	=	"https://raw.githubusercontent.com/rockminerinc/RockWeb/master/rockweb.ver";
			$this->data['latest_version'] 		=	file_get_contents($version_url);
			$this->data['current_version'] 		=	CURRENT_VERSION;
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






	public function pools()
	{

		$this->data['title']= 'pools';
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
			$content['api-description']='cgminer 4.3.3';
			$content['hotplug']='5';

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
			$this->data['freq'] = $data_arr2['anu-freq'];

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

		$this->form_validation->set_rules('setip', 'setip', 'trim|required|xss_clean');	

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
				
			}

			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('setip');	
			
			$this->load->view('common/footer');	

		}

		
	}
	
	public function setdns()
	{
		$this->data['title']= 'setdns';
		$this->form_validation->set_rules('setdns', 'setdns', 'trim|xss_clean');	
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


			
			//echo   
			$data = json_encode($content);
			$data=str_replace("\\/", "/",  $data);
			$file_pointer = fopen('/home/pi/cgminer.conf','w');
			if($file_pointer === false)
			{
				showmsg('/home/pi/cgminer.conf open error');
				exec('sudo chmod 777 /home/pi/cgminer.conf');
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
		$this->data['title']= 'setpools';
		$this->form_validation->set_rules('timezone', 'timezone', 'trim|xss_clean');	
		if($this->form_validation->run())
		{
			exec('sudo rdate -s '.TIME_SERVER);//同步时钟

			$timezone = $this->input->post('timezone', TRUE);
			$timezone_url = '/usr/share/zoneinfo/Etc/'.$timezone;
			//cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime
			exec('sudo cp '.$timezone_url.' /etc/localtime');
			exec('sudo rm -rf /var/www/data/hashrate.txt');
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

		// get current time
		//$cur = time();
		//$aryReturn['TIME'] = $cur;

		// check
		if ( !empty( $output ) && count( $output ) > 0 ) 
		{
			// match timezone
			//preg_match( '/\+0800/' , $output[0] , $match_zone );

			//if ( !empty( $match_zone[0] ) ) 
			//{
				$aryReturn['ZONE'] = $output[0];
			//}
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
			if(!file_exists("/usr/share/nginx/www/data/hashrate.txt"))
			{
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
					//showmsg('/var/www/data/hashrate.txt open error');
				}
				//else
				//{
					$file_pointer = fopen('/usr/share/nginx/www/data/hashrate.txt','a');
					$sumary = request('summary');
						

						 $date=date('Y-m-d H:i:s',time());
						 echo $date;
						 //$date=date('Y-m-d H:i:s');
						 $avg = $sumary['SUMMARY']['MHS av'];
						 $Time_5m = $sumary['SUMMARY']['MHS 5m'];
						 $Time_15m = $sumary['SUMMARY']['MHS 15m'];
						 $head = "date,5m,15m,av\n";
						 $data=$date.','.$Time_5m.','.$Time_15m.','.$avg."\n";
						 fwrite($file_pointer,$data);
						 fclose($file_pointer);
						 echo '200';
				//}

			}

 
			//}

	}


}
 
 