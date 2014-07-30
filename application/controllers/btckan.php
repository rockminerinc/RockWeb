<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Btckan extends CI_Controller 
{
    function __construct() 
	{
        parent::__construct();
		$this->load->helper('cgminerapi');
		// $this->load->helper('functions');
  		// $this->load->library('form_validation');		
	}


 


	public function get_miner_id()
	{
			$filename = "/usr/share/nginx/www/data/setting.txt";
					  $ctx = stream_context_create(array( 
					        'http' => array( 
					            'timeout' => 1    //设置超时
					            ) 
					        ) 
					    ); 

			$contents= file_get_contents($filename, 0, $ctx); 

			$data =	json_decode($contents);		

		return $data->btckan_id;
	}

	private function pack_head()
	{
		$head['minerId'] = $this->get_miner_id();
		$head['client']['name'] = 'Rock';
		$head['client']['ver'] = '0.1.1';

		return $head;
	}

	private function pack_status()
	{
		$status['summary'] = request_raw('summary');
		$status['pools'] = request_raw('pools');
		$status['coin'] = request_raw('coin');
		return $status;
	}

	private function post($url, $data)
	{
		$post_data = json_encode($data);
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

	public function send_status()
	{
		$data['head'] = $this->pack_head();
		$data['data']['status'] = $this->pack_status();
		//var_dump($data);
		$dev_id	=	$this->get_miner_id();
		if($dev_id=='')
			exit;
		else
		{
			$result = $this->post('http://miner.btckan.com/miner/miner_status', $data);
			var_dump($result);
			$this->status_rsp_proc($result);
		}

	}

	private function status_rsp_proc($rsp)
	{
		$cmd_proc_func = array('reboot' => 'reboot_cmd_proc',);

		$result = json_decode($rsp, true);

		if(!isset($result['data']['cmd']))
		{
			return;
		}

		$cmd = $result['data']['cmd'];
		if(!array_key_exists($cmd['operation'], $cmd_proc_func))
		{
			$data['cmd']['id'] = $cmd['id'];
			$data['cmd']['resultCode'] = 1;
			$data['cmd']['result'] = 'Invalid command.';

			$request['head'] = $this->pack_head();
			$request['data'] = $data;

			$result = $this->post('http://miner.btckan.com/miner/cmd_rsp', $request);
			return;
		}

		$this->$cmd_proc_func[$cmd['operation']]($cmd);
	}

	private function reboot_cmd_proc($cmd)
	{
		$output = array();
		exec('sudo reboot', $output, $resultCode);

		$data['cmd']['id'] = $cmd['id'];
		$data['cmd']['resultCode'] = $resultCode;
		$data['cmd']['result'] = implode('\r\n', $output);

		$request['head'] = $this->pack_head();
		$request['data'] = $data;

		$result = $this->post('http://miner.btckan.com/miner/cmd_rsp', $request);
		echo $result;
	}

	private function cgminer_cmd_proc($cmd)
	{
		if(trim($cmd['para']))
		{
			$opt = $cmd['operation'].' '.$cmd['para'];
		}
		else
		{
			$opt = $cmd['operation'];
		}

		$data['cmd']['result'] = request_raw($opt);
		$data['cmd']['id'] = $cmd['id'];

		$resultCode = explode(',', $data['cmd']['result']);
		$resultCode = explode('=', $resultCode[0]);
		$resultCode = ($resultCode[1] == 'S') ? 0 : 1;

		$data['cmd']['resultCode'] = $resultCode;

		$request['head'] = $this->pack_head();
		$request['data'] = $data;

		$result = $this->post('http://miner.btckan.com/miner/cmd_rsp', $request);
		echo $result;
	}

	public function test()
	{
		print_r(request_raw('summary'));
		print_r(request('summary'));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */