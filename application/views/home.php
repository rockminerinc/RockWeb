<div id="content">


<?php if($debug):?>
	<div id="ugraphdiv2" style="width:750px; height:375px;"></div>

<script type="text/javascript">

	var blockUpdateA = 0;
	var blockUpdateB = 0;

	g2 = new Dygraph(document.getElementById("ugraphdiv2"),"data/hashrate.txt",{ 
		strokeWidth: 1.5,
		fillGraph: true,
		'5 minutes': { color: '#408000' },
		'15 minutes': { fillGraph: false, strokeWidth: 2.25, color: '#400080' },
		'Average': { fillGraph: false, strokeWidth: 2.25, color: '#008080' },
		labelsDivStyles: { border: '1px solid black' },
		title: 'Hashrate Graph(Local)',
		xlabel: 'Date',
		ylabel: 'Mh/sec',
		animatedZooms: true,
		drawCallback: function(dg, is_initial) {
		if (is_initial) {
				var rangeA = g2.xAxisRange();
				//g3.updateOptions( { dateWindow: rangeA } );
			} else {
				if (!blockUpdateA) {
					blockUpdateB = 1;
					var rangeA = g2.xAxisRange();
					//g3.updateOptions( { dateWindow: rangeA } );
					blockUpdateB = 0;
				}
			}
		}

	});
 
 
</script>
 <?php endif;?>
 
 <table class="table table-striped" style="width:90%">
<tr style="font-weight:bold;">
 
	<td>Elapsed</td><td>GHS av</td><td>GHS 5s</td><td>GHS 15m</td><td>Accepted</td><td>Rejected</td><td>Rej%</td>
 
</tr>
 <?php foreach ($sumary as $key=>$devs): ?>


 <?php
 if($key=='STATUS')continue;
 ?>
 

 				<tr  
 				<?php 
 				if($key=="Hardware Errors") 
 					echo ' class="error" ';
 				else if($key=="MHS av") 
 					echo ' class="success" '; 
 				?> 
 				>

             <?php foreach ($devs as $key=>$str): ?>
 				<?php
					
 				if( $key=='Last getwork'||$key=='Device Rejected%'||$key=='Difficulty Stale'||$key=='Difficulty Rejected'||$key=='Difficulty Accepted'||$key=='Local Work'||$key=='MHS 1m'||$key=='MHS 5m'||$key=='Getworks'||$key=='Hardware Errors'||$key=='Discarded'||$key=='Found Blocks'||$key=='Utility'||$key=='Stale'||$key=='Get Failures'||$key=='Remote Failures'||$key=='Network Blocks'||$key=='Total MH'||$key=='Work Utility'||$key=='Stratum URL'||$key=='Has GBT'||$key=='Pool Stale%'||$key=='Best Share'||$key=='Device Hardware%')
 					continue;

					
 					if($key=='When')
 					{
 						$key="Time";
 						$str = date('Y/m/d H:i:s',$str);
 					}
					
 					if($key=='Last getwork')
 					{
 						//$key="Time";
 						$str = date('Y/m/d H:i:s',$str);
 					}

  					if($key=='Pool Rejected%')
 					{
 						//$key="Time";
 						$str = number_format($str,2);
 					}

 					if($key == 'Elapsed')
 					{
 						$str = timediff($str);
 						

 					}
										
 					if($key=='MHS av'||$key=='MHS 5s'||$key=='MHS 1m'||$key=='MHS 5m'||$key=='MHS 15m')
 					{
 						//$key="Time";
 						$key=str_replace("MHS", "GHS", $key);
 						$str=floor($str*0.001);
 						$str .=" GHS";
 					}
										

 				?>



             

                 <td><?php echo $str; ?></td>
 				 
             <?php endforeach; ?>
             </tr>






       

 
  <?php endforeach; ?>

 </table>



<table class="table table-striped" style="width:90%">
<tr style="font-weight:bold;">
	<td>POOL</td><td>URL</td><td>Status</td><td>Priority</td><td>Worker</td><td>Last Share Time</td> 
</tr>
 
<?php foreach ($pools as $key=>$devs): ?>
<?php
if($key=='STATUS')continue;
?>

 <tr 
 				<?php 
 				if($key=="Hardware Errors") 
 					echo ' class="error" ';
 				else if($key=="MHS av") 
 					echo ' class="success" ';
 				
				else if($str=="Alive") 
 					echo ' class="success" ';
				 else if($str=="Dead") 
					echo ' class="error" '; 

				
 				?>
>
           <?php foreach ($devs as $key=>$str): ?>
				
				
				<?php
 
					if($key!='POOL' && $key!='URL'  && $key!='Status'&& $key!='User' && $key!='Priority' && $key!='Last Share Time'  )
						continue;

					
					
					if($key=='Last Share Time')
					{
						//$key="Time";
						$period =time()-$str;
						
						$str = time_tran($str);	
						//$str = date('Y/m/d H:i:s',$str);
						if($period>300)
							$str='<font color=gray><i>'.$str.'</i></font>';
						else
							$str='<font color=blue>'.$str.'</font>';

					}
 

										
					if($key=='MHS av'||$key=='MHS 5s'||$key=='MHS 1m'||$key=='MHS 5m'||$key=='MHS 15m')
					{
						//$key="Time";
						$str .= " MHS";
					}	
									
					 
				?>

				
               <td><?php echo $str; ?></td>
				 
            <?php endforeach; ?>
</tr>
       

 
 <?php endforeach; ?>
</table>



<table class="table table-striped" style="width:90%">


       <tr style="font-weight:bold;">
       	<td width=50px>ID</td><td>Temperature</td><td>GHS av</td><td>GHS 5s</td><td>GHS 15m</td>
       </tr>

<?php 
$dev_num=1;
foreach ($devss as $key=>$devs): ?>

<?php
if($key=='STATUS')
	continue;
 
?>

       <tr >

            <?php foreach ($devs as $key=>$str): ?>

            <?php
 
            	if($devs['Enabled']=='N')
            		break;
            ?>

				<?php
 
					 if($key=='ID')
					 {
					 	echo '<td>'.$dev_num.'</td> ';
					 		$dev_num++;
					 }
					  
					if($key!="Temperature"&&$key!="User"&&$key!="MHS av"&&$key!="MHS 5s"&&$key!="MHS 15m") 
						continue;
 

 					if($key=='Device Hardware%'||$key=='Pool Rejected%')
 					{
 						//$key="Time";
 						$str = number_format($str,2);
 					}

					if($key=='Temperature')
 					{
 						//$key="Time";
 						$str = floor($str);
 						if($str>55)
 							$str = '<span style="color:red;font-weight:bold;padding:5px 10px 5px 10px;background: #F3E86C;">'.$str.'</span>';

 					}

					if($key=='Device Rejected%')
 					{
 						//$key="Time";
 						$str = number_format($str,2);
 					}


 					if($key=='Enabled'&&$str=="N")
 					{
 						//$key="Time";
 						//continue;
 						$str = '<span style="color:red;font-weight:bold;">NO</span>';
 					}
										
 					if($key=='MHS av'||$key=='MHS 5s'||$key=='MHS 1m'||$key=='MHS 5m'||$key=='MHS 15m')
 					{
 						//$key="Time";
 						$key=str_replace("MHS", "GHS", $key);
 						$str=floor($str*0.001);
 						$str .=" GHS";
 					}					
			
					
				?>
			<td><?php echo $str; ?></td>
				 
            <?php endforeach; ?>
       </tr>      

 
 <?php endforeach; ?>
 </table>


	</div>
 

