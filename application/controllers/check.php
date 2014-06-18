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
		
	}


 

	public function index()
	{
		//$this->data['sumary'] = request('summary');
		$this->data['title']= 'summary';
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');	
		$this->load->view('check_index');	
		$this->load->view('common/footer');		
		 
	}

	public function lsusb()
	{
		// lsusb command
		$command = 'sudo lsusb';
		exec( $command , $output );

		$aryReturn = array( 'COMMAND'=>0 , 'MILL'=>0 );
		// check result
		if ( !empty( $output ) && count( $output ) > 0 )
		{
			// run command success
			$aryReturn['COMMAND'] = 1;
			// find mill
			foreach ( $output as $usb )
			{
				//Bus 001 Device 004: ID 10c4:ea60 Cygnal Integrated Products, Inc. CP210x UART Bridge / myAVR mySmartUSB light


				preg_match( '/.*Bus\s(\d+)\sDevice\s(\d+).*Cygnal\sIntegrated\sProducts.*CP210x\sUART\sBridge.*/' , $usb , $match_usb );
				if ( !empty( $match_usb[1] ) && !empty( $match_usb[2] ) )
				{
					$aryReturn['MILL'] ++;
				}
			}
		}

		echo json_encode( $aryReturn );
		exit;
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

	public function pools()
	{
		$this->data['r'] = request('pools');
		$this->data['title']= 'pools';
		$this->load->view('common/header', $this->data);	
		$this->load->view('common/left');	
		$this->load->view('pools');	
		
		$this->load->view('common/footer');	
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
			$content['freq']=$this->input->post('freq', TRUE);;
			//$content['icarus-timing']='3.0=100';
			
			//echo   
			$data = json_encode($content);
			$data=str_replace("\\/", "/",  $data);
			$file_pointer = fopen('/home/pi/cgminer.conf','w');
			if($file_pointer === false)
			{
				showmsg('/home/pi/cgminer.conf open error');
			}
			else
			{
				fwrite($file_pointer,$data);
				fclose($file_pointer);
				showmsg('Pools updated OK!');
			}
 

		}
		else
		{
			$filename = "/home/pi/cgminer.conf";
		    $handle = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
		    
		    //通过filesize获得文件大小，将整个文件一下子读到一个字符串中
		    $contents = fread($handle, filesize ($filename));
		    fclose($handle);
			
			$data_arr = json_decode($contents);
			
			$pools_data=$data_arr->pools;
			$this->data['data_pool1'] = $pools_data[0];
			$this->data['data_pool2'] = $pools_data[1];
			$this->data['freq'] = $data_arr->freq;

			$this->load->view('common/header', $this->data);	
			$this->load->view('common/left');	
			$this->load->view('setpools');	
			$this->load->view('common/footer');		

		}

	}

 
	




	public function reboot()
	{

		$this->form_validation->set_rules('reboot', 'reboot', 'trim|required|xss_clean');	

		if($this->form_validation->run())
		{

			//$exec('sudo reboot');
			exec('sudo rdate -s tick.greyware.com');//同步时钟
			
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

 

	public function switchpool()
	{
		request('privileged');
		$this->data['r'] = request('switchpool');
		var_dump($this->data['r']);
		//$this->load->view('common/header', $this->data);	
		//$this->load->view('common/left');	
		//$this->load->view('restartCgminer');	
		
		//$this->load->view('common/footer');		
	}

	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */