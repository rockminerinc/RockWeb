
<div id="content">

<?php echo form_open('c=home&m=setip'); ?>

<table align=" " border="0" cellspacing="0">
<tbody>
<tr>
	<td align="right">IP</td>
	<td align="left">
		<input name="JMIP" value="<?php echo $ip_adress;?>" size="30" type="text" mouseev="true" keyev="true" >
	</td>
</tr>
<tr>
	<td align="right">Mask</td>
	<td align="left">
		<input name="JMSK" value="<?php echo $mask;?>" size="30" type="text"></td>
</tr>
<tr>
	<td align="right">Gateway</td>
	<td align="left">
		<input name="JGTW" value="<?php echo $gateway_id;?>" size="30" type="text">
	</td>
</tr>



<tr>
<td align="right"></td></tr>
<tr>
<td colspan="2" align="center">
<input type="button" value="Refresh" onclick="window.location.href=''" class="btn">
<input name="setip" value="Update" type="submit"  class="btn">
</td>
</tr></tbody></table>


  
</form>
</div>