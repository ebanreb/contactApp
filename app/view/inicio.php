<div class="contenido">

	<?php include_once "modulos/presentacion.php"; ?>

	<div id="formRegistro">
			<h1><small><i class="fa fa-sign-out"></i>
&nbsp;Registro!</small></h1>
			<hr class="well-black"></hr>
		<form role="form" method="post" id="formularioRegistro">
			<div class="form-group">
				<label for="nombre">nombre</label><small> *</small>
				<input type="text" class="form-control" name="name" id="nombre" autocomplete="off" required  placeholder="Nombre"/>
				<div class="nombre errorform"></div>
			</div>
			<div class="form-group">
				<label for="email">email</label><small> *</small>
				<input type="email" class="form-control" name="email" id="email" autocomplete="off" required  placeholder="Email"/>
			    <div class="email errorform"></div>
			</div>
			
			<div class="form-group">
				<label for="confEmail">confirma el email</label>
				<input type="email" class="form-control" name="confEmail" id="confEmail" autocomplete="off" required  placeholder="Confirmar Email"/>
			</div>
			<div class="form-group">
				<label for="password">contraseña</label><small> * </small><span id="result"></span>
				<input type="password" class="form-control" name="password" id="password" autocomplete="off" required  placeholder="Contraseña"/>
			    <div class="pass errorform"></div>
			</div>
			<div class="form-group">
				<label for="confPassword">confirma la contraseña</label>
				<input type="password" class="form-control" name="confPassword" id="confPassword" autocomplete="off" required  placeholder="Confirma Contraseña"/>
			</div>
			 <div class="checkbox">
			  <label>
			    <input type="checkbox" id="privacidad" name="privacidad" value="1">
			    <small> * </small>confirmo que he leído y acepto las condiciones de la <a id="polPrivacidad" href="">política de privacidad</a>
			  </label>
			  <div class="priv errorform"></div>
			</div>
			<div><small>* Campos obligatorios</small></div>
			<button type="submit" class="btn btn-azul">enviar</button>
		</form>
	</div>
    
    <div id="newPass" class="modalPanel">
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

    <div id="modPC" class="modalPanel">
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
