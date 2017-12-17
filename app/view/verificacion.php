<div class="contenido">

	<?php include_once "modulos/presentacion.php"; ?>

	<div id="formVf">
  		<h1><small><i class="fa fa-key"></i>&nbsp;verificación de cuenta!</small></h1>
  		<hr class="well-black"></hr>
	    <form role="form" method="post" id="formularioVerificacion">
	    	<div class="form-group">
				<label for="emailVeri">email</label>
				<input type="email" class="form-control" name="email" id="emailVeri" autocomplete="off" required  placeholder="Email" />
			</div>
			<div class="form-group">
				<label for="codVerificacion">codigo de verificación</label>
				<input type="password" class="form-control" name="codVr" id="codVerificacion" autocomplete="off" required  placeholder="Codigo de verificación" />
			</div>
			<button type="submit" class="btn btn-azul">enviar</button> 
		</form>
	</div>

	<div id="backPopAp"></div>
    
    <div id="newPass">
    	<h1><small><i class="fa fa-question"></i>&nbsp;Recuperar contraseña!</small></h1>
		<hr class="well-black"></hr>
		<form role="form" method="post" id="formularioPassWord">
			<div class="form-group">
				<label for="email">email</label>
				<input type="email" class="form-control" name="email" id="email" autocomplete="off" required  placeholder="Email"/>
			    <div class="email errorform"></div>
			</div>
			
			<button type="submit" id="enviarNewPass" class="btn btn-azul">enviar</button>
			<button id="closeNewPass" class="btn btn-rojo">cerrar</button>
			<button id="acNewPass" class="btn btn-naranja">introducir codigo</button>
		</form>
    </div>

    <div id="modPC">
    	<h1><small><i class="fa fa-question"></i>&nbsp;Recuperar contraseña!</small></h1>
		<hr class="well-black"></hr>
		<form role="form" method="post" id="formularioPassWordCod">
			<div class="form-group">
				<label for="email">email</label>
				<input type="email" class="form-control" name="email" id="email" autocomplete="off" required  placeholder="Email"/>
			    <div class="email errorform"></div>
			</div>

			<div class="form-group">
				<label for="modPassword">contraseña</label><small> * </small><span id="resultModP"></span>
				<input type="password" class="form-control valPass" name="password" id="modPassword" autocomplete="off" required  placeholder="Contraseña"/>
			    <div class="pass errorform"></div>
			</div>
			<div class="form-group">
				<label for="confPassword">confirma la contraseña</label>
				<input type="password" class="form-control" name="confPassword" id="confPassword" autocomplete="off" required  placeholder="Confirma Contraseña"/>
			</div>

			<div class="form-group">
				<label for="codigo">codigo</label>
				<input type="text" class="form-control" name="codigo" id="codigo" autocomplete="off" required  placeholder="Codigo"/>
			    <div class="codigo errorform"></div>
			</div>
			
			<button type="submit" id="enviarModPC" class="btn btn-azul">enviar</button>
			<button id="closeModPC" class="btn btn-rojo">cerrar</button>
		</form>
    </div>

</div>
