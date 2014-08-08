<div id="content">

<form action="index.php" method="get" accept-charset="utf-8"  >
<input  name="c" value="show" type="hidden" class="btn">
<input name="m" value="index" type="hidden" class="btn">
网段<input  style="width:50px" name="rack" value="" size="10" type="text">
开始IP<input  style="width:50px"  name="start" value="" size="10" type="text">
结束IP<input   style="width:50px" name="end" value="" size="10" type="text">
 <input type="radio" name="save" value="1" />保存IP到云端


<input name="check" value="check" type="submit" class="btn">


</form>

 

<table class="table table-striped" style="margin-left:50px;width:60%">
<tr>
  <td>IP</td>
  <td>Boards</td>
  <td>HashRate</td>
  <td>Operate</td>
</tr>
 <?php foreach ($datas as $key=>$data): ?>

  <tr>
  	<td><a href="http://<?= $key ?>:8000" target="_blank"><?= $key ?></td>
  	<?php foreach ($data as $data2): ?>
  	<td>
  		<?= $data2 ?>
  	</td>	 
    <?php endforeach; ?>
    <td>
      <a href="<?= SITE_URL ?>/?c=show&m=reboot&ip=<?= $key ?>" target="_blank">Reboot
    </td>  

  </tr>



  <?php endforeach; ?>

   </table>
 
	</div>
 


 