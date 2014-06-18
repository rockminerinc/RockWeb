<div id="content">


 
 
 <table class="table table-striped" style="width:750px">
<tr style="font-weight:bold;">
 
	<td>GHS av</td><td>GHS 5s</td><td>GHS 5m</td><td>Accepted</td><td>Rejected</td><td>HW%</td><td>Rej%</td>
 
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
					
 				if($key=='Elapsed'||$key=='Last getwork'||$key=='Device Rejected%'||$key=='Difficulty Stale'||$key=='Difficulty Rejected'||$key=='Difficulty Accepted'||$key=='Local Work'||$key=='MHS 1m'||$key=='MHS 15m'||$key=='Getworks'||$key=='Hardware Errors'||$key=='Discarded'||$key=='Found Blocks'||$key=='Utility'||$key=='Stale'||$key=='Get Failures'||$key=='Remote Failures'||$key=='Network Blocks'||$key=='Total MH'||$key=='Work Utility'||$key=='Stratum URL'||$key=='Has GBT'||$key=='Pool Stale%'||$key=='Best Share')
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

  					if($key=='Device Hardware%'||$key=='Pool Rejected%')
 					{
 						//$key="Time";
 						$str = number_format($str,2);
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



<table class="table table-striped" style="width:750px">
<tr style="font-weight:bold;">
	<td>POOL</td><td>URL</td><td>Status</td><td>Priority</td><td>Last Share Time</td> 
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
 
					if($key!='POOL' && $key!='URL' && $key!='Status' && $key!='Priority' && $key!='Last Share Time'  )
						continue;

					
					
					if($key=='Last Share Time')
					{
						//$key="Time";
						$str = date('Y/m/d H:i:s',$str);
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


	</div>
 

