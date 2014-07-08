
<div id="content">

<?php echo form_open('c=home&m=setting'); ?>

<table align=" " border="0" cellspacing="0">
<tbody>
<tr>
	<td align="right">Device Name</td>
	<td align="left">
		<input name="dev_name" value="<?php echo $dev_name;?>" size="30" type="text" mouseev="true" keyev="true" >
	</td>
</tr>
<!--tr>
	<td align="right">Device ID</td>
	<td align="left">
		<input name="dev_id" value="<?php echo $dev_id;?>" size="30" type="text"></td>
</tr-->
<tr>
	<td align="right">Monitor URL</td>
	<td align="left">
		<input name="monitor_url" value="<?php echo $monitor_url;?>" size="30" type="text"></td>
</tr>

<tr>
	<td align="right">BTCKAN Device ID </td>
	<td align="left">
		<input name="btckan_id" value="<?php echo $btckan_id;?>" size="30" type="text"></td>
</tr>
 


<tr>
<td align="right"></td></tr>
<tr>
<td colspan="2" align="center">
<input type="button" value="Refresh" onclick="window.location.href=''" class="btn">
<input name="setting" value="Update" type="submit"  class="btn">
</td>
</tr></tbody></table>


  
</form>
</div>