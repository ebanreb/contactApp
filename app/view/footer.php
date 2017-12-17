<?php
  date_default_timezone_set("Europe/Madrid");
  setlocale (LC_TIME,"spanish", "es_ES@euro", "es_ES", "es");
  //$inicio=2014;
  $actual=date('Y');
  //$fecha = "";
  /*if ($inicio==$actual){
     $fecha = $inicio;
  } else {
     $fecha = "{$inicio}-{$actual}";
  }*/
?>
<footer>
	<ul>
		<li><a id="fPrivacidad" href="#">privacidad</a></li>
		<li>Contact!&nbsp;&copy;&nbsp;<?= $actual ?></li>
	</ul>
</footer>	 	
		 	