<div id="content">

<table class="table table-striped" >

<?php $num=0;?>
<?php foreach ($r as $key=>$devs): ?>
<?php
if($key=='STATUS')continue;

if($num>0) continue;
?>

       <tr >

            <?php foreach ($devs as $key=>$str): ?>
				

				<?php

					if($key!="ID"&&$key!="Enabled"&&$key!="Temperature"&&$key!="MHS av"&&$key!="MHS 5s"&&$key!="Device Hardware%"&&$key!="Device Rejected%") 
						continue;
										
 					if($key=='MHS av'||$key=='MHS 5s'||$key=='MHS 1m'||$key=='MHS 5m'||$key=='MHS 15m')
 					{
 						//$key="Time";
 						$key=str_replace("MHS", "GHS", $key);
 						
 					}
 					if($key=='Device Hardware%')
 					{
 						//$key="Time";
 						$key='HW%';
 						 
 					}

 					if($key=='Pool Rejected%')
 					{
 						//$key="Time";
 						$key='Rej%';
 					}
										
				 
									
					
				?>
			<td <?php if($key=='ID'): ?>width=50px<?php endif;?> ><strong><?php echo $key; ?></strong></td></td>
			
            <?php endforeach; ?>
       </tr>
<?php $num++;?>	 
<?php endforeach; ?>


<?php foreach ($r as $key=>$devs): ?>

<?php
if($key=='STATUS')continue;
?>

 

       <tr >

            <?php foreach ($devs as $key=>$str): ?>
				<?php

					if($key!="ID"&&$key!="Enabled"&&$key!="Temperature"&&$key!="User"&&$key!="MHS av"&&$key!="MHS 5s"&&$key!="Device Hardware%"&&$key!="Device Rejected%") 
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
 					}

					if($key=='Device Rejected%')
 					{
 						//$key="Time";
 						$str = number_format($str,2);
 					}


 					if($key=='Enabled'&&$str=="N")
 					{
 						//$key="Time";
 						 
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