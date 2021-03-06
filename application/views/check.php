<script language="javascript" type="text/javascript" src="static/js/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="static/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script language="javascript" type="text/javascript" src="static/js/html5shiv.min.js"></script>
<script language="javascript" type="text/javascript" src="static/js/respond.min.js"></script>
<![endif]-->
<style type="text/css">
	#check-result{

		width:50%;
		height: 200px;
	}
	.check-item{
		font-size:14px;
		line-height:18px;
	}
	.nav-pills>li.active>a.waiting-check, .nav-pills>li.active>a.waiting-check:hover, .nav-pills>li.active>a.waiting-check:focus{
		background-color:#666;
	}
	.nav-pills>li.active>a.check-error, .nav-pills>li.active>a.check-error:hover, .nav-pills>li.active>a.check-error:focus{
		background-color:#d2322d;
	}
	ul.nav-pills li{
		padding:2px 0;
	}
</style>



	<div id="content">
			 
				<div id="check-result">
				<ul class="nav nav-pills nav-stacked">
 
					<li class="active">
						<a class="check-item waiting-check" id="check-result-lsusb-2">
							<span class="badge pull-right">NULL</span>
							Devices number
						</a>
					</li>

					<li class="active">
						<a class="check-item waiting-check" id="check-result-date-1">
							<span class="badge pull-right">NULL</span>
							System Time
						</a>
					</li>
					<li class="active">
						<a class="check-item waiting-check" id="check-result-network-1">
							<span class="badge pull-right">NULL</span>
							Network
						</a>
					</li>
 
					<li class="active">
						<a class="check-item waiting-check" id="check-result-network-3">
							<span class="badge pull-right">NULL</span>
							Network delay
						</a>
					</li>
					<li class="active">
						<a class="check-item waiting-check" id="check-result-version-1">
							<span class="badge pull-right">NULL</span>
							Version
						</a>
					</li>

				</ul>

				
				</div>
				 <button id="button-check" class="btn btn-danger ">Start Check</button>
				
			 
		 
  	 
 
</div>

<script type="text/javascript">
var actions = {
	setting : {
		url_lsusb			: '/index.php?c=home&m=check_lsusb',
		url_date			: '/index.php?c=home&m=check_time_zone',
		url_network			: '/index.php?c=home&m=check_network',
		url_version			: '/index.php?c=home&m=check_version',

	},
	// send post to server
	sendPost : function( callback , tourl , senddata ){
		// set default data
		if ( typeof( senddata ) == 'undefined' ) senddata = {};

		$.ajax({
			type	: "GET",
			url		: tourl+"&rand="+Math.random(),
			data 	: senddata,
			success : function( r ){
				r = eval( '('+r+')' );
				eval( "actionSuccess."+callback+"( r )" );
			},
			fail	: function(){
				eval( "actionFail."+callback+"( r )" );
			}
		});
	}
};

var actionSuccess = {

	lsusbResult : function( data )
	{
		$('#check-result-lsusb-1,#check-result-lsusb-2').removeClass('waiting-check');
		checkOptionFinish( 'lsusb' );
		if ( data == null )	
		{
			actionFail.lsusbResult( data );
			return;
		}
		/*
		if ( data.COMMAND === 1 )
		{
			$('#check-result-lsusb-1 span').html( 'OK' );
		}
		else
		{
			$('#check-result-lsusb-1').addClass( 'check-error' );
			$('#check-result-lsusb-1 span').html( 'ERROR' );
		}*/

		if ( data.BLADES > 0 )
		{
			$('#check-result-lsusb-2 span').html( data.BLADES );
		}
		else
		{
			$('#check-result-lsusb-2').addClass( 'check-error' );
			$('#check-result-lsusb-2 span').html( '0' );
		}
	},

	dateResult : function( data )
	{
		$('#check-result-date-1,#check-result-date-2').removeClass('waiting-check');
		checkOptionFinish( 'date' );
		if ( data == null )	
		{
			actionFail.dateResult( data );
			return;
		}

		if ( data.ZONE != ''  )
		{
			$('#check-result-date-1 span').html( data.ZONE );
		}
		else
		{
			$('#check-result-date-1').addClass( 'check-error' );
			$('#check-result-date-1 span').html( 'ERROR' );
		}
		/*
		var d = new Date();
		cur = d.getTime()/1000;
		offset = d.getTimezoneOffset() * 60;
		cur = offset + cur + 8*3600;
		if ( data.TIME > 0 && cur - data.TIME < 30 && cur - data.TIME > -30 )
		{
			$('#check-result-date-2 span').html( 'OK' );
		}
		else
		{
			$('#check-result-date-2').addClass( 'check-error' );
			$('#check-result-date-2 span').html( 'ERROR' );
		}*/
	},

	
	versionResult : function( data )
	{
		$('#check-result-version-1').removeClass('waiting-check');
		checkOptionFinish( 'version' );
		if ( data == null )	
		{
			actionFail.versionResult( data );
			return;
		}

		if ( data.VERSION != ''  )
		{
			$('#check-result-version-1 span').html( data.VERSION );
		}
		else
		{
			$('#check-result-version-1').addClass( 'check-error' );
			$('#check-result-version-1 span').html( 'ERROR' );
		}
 
	},

	networkResult : function( data )
	{
		$('#check-result-network-1,#check-result-network-2,#check-result-network-3').removeClass('waiting-check');
		checkOptionFinish( 'network' );
		if ( data == null )	
		{
			actionFail.networkResult( data );
			return;
		}

		if ( data.NET === 1 )
		{
			$('#check-result-network-1 span').html( 'OK' );
		}
		else
		{
			$('#check-result-network-1').addClass( 'check-error' );
			$('#check-result-network-1 span').html( 'ERROR' );
		}
 
		if ( data.NET_DELAY != '' )
		{
			$('#check-result-network-3 span').html( data.NET_DELAY );
		}
		else
		{
			$('#check-result-network-3').addClass( 'check-error' );
			$('#check-result-network-3 span').html( 'ERROR' );
		}
	}
};

var actionFail = {

	lsusbResult : function( data )
	{
		checkOptionFinish( 'lsusb' );
		$('#check-result-lsusb-2').addClass( 'check-error' );
		$('#check-result-lsusb-2 span').html( 'ERROR' );
	},

	dateResult : function( data )
	{
		checkOptionFinish( 'date' );
		$('#check-result-date-1,#check-result-date-2').addClass( 'check-error' );
		$('#check-result-date-1 span,#check-result-date-2 span').html( 'ERROR' );
	},

	versionResult : function( data )
	{
		checkOptionFinish( 'version' );
		$('#check-result-version-1,#check-result-version-2').addClass( 'check-error' );
		$('#check-result-version-1 span,#check-result-version-2 span').html( 'ERROR' );
	},


	networkResult : function( data )
	{
		checkOptionFinish( 'network' );
		$('#check-result-network-1,#check-result-network-2,#check-result-network-3').addClass( 'check-error' );
		$('#check-result-network-1 span,#check-result-network-2 span,#check-result-network-3 span').html( 'ERROR' );
	}


};

function checkOptionFinish( tar )
{
	eval( "check_options."+tar+" = 1;" );
	for ( var option in check_options )
	{
		eval( "var tmp_val = check_options."+option+";" );
		if ( tmp_val === 0  )
			return;
	}

	ischecking = false;
	$( '#button-check' ).html( 'ReCheck' );
}

$('#button-find').click(function(){
	actions.sendPost( 'findResult' , actions.setting.url_find , {} );
});

$('#button-find-stop').click(function(){
	actions.sendPost( 'findStopResult' , actions.setting.url_find_stop , {} );
});

var ischecking = false;
var check_options = {
	lsusb	: 0,
	date	: 0,
	network	: 0,
	version	: 0

};

$('#button-check').click(function(){
	if ( ischecking === true )
		return;

	$('.check-item').removeClass( 'check-error' ).removeClass( 'waiting-check' ).addClass( 'waiting-check' );

	ischecking = true;
	for ( var option in check_options )
		eval( "check_options."+option+" = 0;" );
	
	$(this).html( 'Checking...' );
	$('.check-item span').html( 'Checking...' );

	actions.sendPost( 'lsusbResult' , actions.setting.url_lsusb , {} );
	actions.sendPost( 'dateResult' , actions.setting.url_date , {} );
	actions.sendPost( 'networkResult' , actions.setting.url_network , {} );
	actions.sendPost( 'versionResult' , actions.setting.url_version , {} );
});
</script>
