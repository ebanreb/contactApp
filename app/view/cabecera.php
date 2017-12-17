<div class="backCab"></div>
<div id="cabecera">
  	    <div class="logo"> <h1><span class="icoWeb">C</span>ontact!&nbsp;<i class="fa fa-comments-o"></i></h1></div>
            
             <?php if(isset($_SESSION['contactUser'])){ ?>

		        <div id="main">

						<!-- Main Input -->
						<div class="input-group">
					      <span class="input-group-addon">
					      	 <span class="glyphicon glyphicon-user"></span>
					      </span>
					      <input  class="form-control" type="text" id="search" autocomplete="off" placeholder="busca contactos...">
					    </div>

						<!-- Show Results -->
						<!--<small id="results-text">Showing results for: <b id="search-string">Array</b></small>-->
						<div id="results" class="list-group"></div>


			    </div>

		  	    <div class="btn-group">

		  	        <div class="navbar-header">
					    <button type="button" class="btn-toggle">
					      <span class="icon-bar"></span>
					      <span class="icon-bar"></span>
					      <span class="icon-bar"></span>
					      <span class="misSd badge"></span>
					    </button>
					</div>
				    
				    <ul class="dropdown-menu">
				      <li><a href="<?= ROOT; ?>"><i class="fa fa-home"></i><span class="tx-menu">&nbsp;&nbsp;inicio</span></a></li>
				      <li>
				         <a href="<?= ROOT; ?>seccion,solicitudes/"><i class="fa fa-users"></i><span class="tx-menu">&nbsp;&nbsp;solicitudes</span>  <span class="misSd badge"></span></a>
				         <ul id="solicitudesRec"></ul>
				      </li>
				      <li><a href="<?= ROOT; ?>seccion,cnf/"><i class="fa fa-cog"></i><span class="tx-menu">&nbsp;&nbsp;mis datos</span></a></li>
				      <li><a id="logout" href="<?= ROOT; ?>action/logout/"><i class="fa fa-power-off"></i><span class="tx-menu">&nbsp;&nbsp;salir</span></a></li>
				    </ul>

		            <div class="us-box">
				      <?php
				        $fp = (file_exists(DOCUMENT_ROOT."images/contact/min/".$_SESSION['contactUser']['user']['uid'].".jpg")) ? "images/contact/min/".$_SESSION['contactUser']['user']['uid'].".jpg" : "images/user_icon-40x40.jpg" ; 
				      ?>
				      <img id="miFoto" src="<?= $fp; ?>?<?= rand(1,1000);?>" class="icContacto img-rounded"/>
				      <span id="yo"><?= $_SESSION["contactUser"]["user"]["name"]; ?></span>
				      <!--<span class="caret"><span class="misSd badge"></span></span>-->
				    </div>

				</div>
				
			<?php }else{ ?>

                <div id="btlogin">
		  	      <div id="linkLog"><a id="showLog" href="#"><i class="fa fa-user"></i>&nbsp;login!</a></div>
		  	      <div id="formLogin">
				    <form role="form" method="post" id="formularioLogin">
				    	<div class="form-group">
							<label for="email">email</label>
							<input type="email" class="form-control" name="email" id="emailLog" autocomplete="off" required  placeholder="Email" />
						</div>
						<div class="form-group">
							<label for="password">contraseña</label>
							<input type="password" class="form-control" name="password" id="passwordLog" autocomplete="off" required  placeholder="Contraseña" />
						</div>
						<button type="submit" class="btn btn-azul">enviar</button><a id="recPass" href="#">y mi contraseña era...?</a>
						<input type="hidden" name="GMT" value="0" id="userGMT" /> 
					</form>
				  </div>
		  	    </div>
		  	    <div class="clear"></div>
		  	    
			<?php } ?>
	    
</div>

<div id="info" class="bg-azul">
	 <div id="closeinfo"><a href="">x</a></div>
     <div id="respuesta"></div>
</div>