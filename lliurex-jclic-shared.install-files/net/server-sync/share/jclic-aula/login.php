
<div id="login" class="topbar">
    <form action="index.php" method="post">
        <input type="submit" class="topbarbutton" value="Go" />
        <input name="pass" type="password" class="topbarwidget" tabindex="2" required="required"></input>
        <div class="topbarwidget">
	<?php 
	include_once('get_locale.php');
	$locale=get_locale();
    	if ($locale=="valencia") echo "Contrassenya"; 
	else echo "Contraseña"; 
	?>
	</div>
        <input name="user" type="text" class="topbarwidget" tabindex="1" required="required"></input>
        <div class="topbarwidget">
	<?php 
    	if ($locale=="valencia") echo "Nom d'usuari"; 
	else echo "Nombre de usuario"; 
	?>
	</div>
    </form>
</div>
