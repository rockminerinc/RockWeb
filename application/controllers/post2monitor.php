<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post2monitor extends CI_Controller {

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
		$data['ip']			= 	getip();
		$data['ipint']			= 	ip2long($data['ip']);
		//echo $data['ip']	;
		$data['dev_name']	= getconfig("./data/setting.inc.php", "dev_name", $type="string");
		$server = getconfig("./data/setting.inc.php", "monitor_url", $type="string");
		//echo $data['dev_name'];
		$data['dev_num']	= 	dev_num();
		$sumary = request('summary');
		$data['asc_mhs_5s']  	= 	$sumary['SUMMARY']['MHS 5s'];//$data_array[0];
		$data['asc_mhs_5m']  	= 	$sumary['SUMMARY']['MHS 5m'];//$data_array[1];
		$data['asc_mhs_15m']  	= 	$sumary['SUMMARY']['MHS 15m'];//$data_array[2];
		$data['asc_mhs_av']  	= 	$sumary['SUMMARY']['MHS av'];
		$data['asc_last_share_time']  	= 	$sumary['SUMMARY']['Last getwork'];
 
		$data['event_time']  	=time();
 		//var_dump($sumary);
		
 		//$url=$server."?ip=".$data['ip'].'&dev_name='.$data['dev_name'].'&ipint='.$data['ipint'].'&dev_num\='.$data['dev_num'].'\&asc_mhs_av\='.$data['asc_mhs_av'].'\&asc_mhs_5m\='.$data['asc_mhs_5m'].'\&asc_mhs_5s\='.$data['asc_mhs_5s'].'\&asc_mhs_15m\='.$data['asc_mhs_15m'].'\&asc_last_share_time\='.$data['asc_last_share_time'].'\&event_time\='.$data['event_time'];
 		//echo $url;

		$miner_data['ip'] = $data['ip'];
		$miner_data['ipint'] =$data['ipint'];
		$miner_data['dev_name'] =$data['dev_name'];
		$miner_data['dev_num'] =$data['dev_num'];
		$miner_data['asc_mhs_5s'] =$data['asc_mhs_5s'];
		$miner_data['asc_mhs_5m'] =$data['asc_mhs_5m'];
		$miner_data['asc_mhs_15m'] =$data['asc_mhs_15m'];
		$miner_data['asc_mhs_av'] =$data['asc_mhs_av'];
		$miner_data['asc_last_share_time'] =$data['asc_last_share_time'];
		$miner_data['event_time'] =$data['event_time'];

 		$miner_json = json_encode($miner_data);
		$url=$server."index.php?c=home&m=getdata&data=".$miner_json;

		//echo $this->curl->submit($server, $miner_data);
		//echo "dd";$url
		//$re=geturl($url);
		exec("sudo /usr/bin/lynx -source ".$url." &",$command,$output);
		//exec('wget '.$url.' > /dev/null &')
		//echo $url;
		//var_dump($re) ;//$re;
		var_dump($command);
		//var_dump($output);
		 
	}


	

}

