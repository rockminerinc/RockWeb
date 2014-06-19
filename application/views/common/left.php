
    <div id="sidebar">
		<div class="well sidebar-nav">
			<ul class="nav nav-list ">
				<li <?php echo $this->router->fetch_method()=='index' ? ' class="active"' : '' ?> ><a href = "?c=home&m=index" >Home</a></li>
				<li <?php echo $this->router->fetch_method()=='pools' ? ' class="active"' : ''?> ><a href = "?c=home&m=pools" >Pools</a></li> 
				<li <?php echo $this->router->fetch_method()=='setip' ? ' class="active"' : ''?> ><a href = "?c=home&m=setip"  >Set IP</a></li> 
				<li <?php echo $this->router->fetch_method()=='setdns' ? ' class="active"' : ''?> ><a href = "?c=home&m=setdns"  >Set DNS</a></li> 
				<li <?php echo $this->router->fetch_method()=='setTimezone' ? ' class="active"' : ''?> ><a href = "?c=home&m=setTimezone"  >Sync Time</a></li> 

				<li <?php echo $this->router->fetch_method()=='CheckStatus' ? ' class="active"' : ''?> ><a href = "?c=home&m=CheckStatus" >Check Status</a></li> 
				<li  <?php echo $this->router->fetch_method()=='upgrade' ? ' class="active"' : ''?> ><a href = "?c=home&m=upgrade"  >Upgrade</a></li> 
				<li  <?php echo $this->router->fetch_method()=='reboot' ? ' class="active"' : ''?> ><a href = "?c=home&m=reboot"  >Reboot</a></li> 
			</ul>
 
		</div><!--/.well -->	
	
	</div>
