<div class="contenido">
	<div id="cnfPanel">
  		<h1><small><i class="fa fa-cog"></i>&nbsp;tus datos en contact!</small></h1>
  		<hr class="well-black"></hr>
	    <form role="form" method="post" id="formularioDatos">
			<div id="group-name" class="form-group">
				<label for="nombre">nombre</label><small> *</small>
				<input type="text" class="form-control" name="name" id="nombre" value="<?= $_SESSION['contactUser']['user']['name'];?>" autocomplete="off" required  placeholder="Enter name"/>
				<div class="nombre errorform"></div>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" id="modEmail" name="modEmail" value="1">
			    marca esta casilla si quieres modificar tu email
			  </label>
			</div>
			<div id="group-email">
				<div class="form-group">
					<label for="email">email</label><small> *</small>
					<input type="email" class="form-control" name="email" id="email"  value="<?= $_SESSION['contactUser']['user']['email'];?>" autocomplete="off" placeholder="Enter email"/>
				    <div class="email errorform"></div>
				</div>
				<div class="form-group">
					<label for="confEmail">confirma el email</label>
					<input type="email" class="form-control" name="confEmail" id="confEmail" autocomplete="off" placeholder="Enter conf email"/>
				</div>
			</div>
            <div class="checkbox">
			  <label>
			    <input type="checkbox" id="modPass" name="modPass" value="1">
			    marca esta casilla si quieres modificar el password
			  </label>
			</div>
			<div id="group-password">
				<div class="form-group">
					<label for="password">password</label><small> * </small><span id="result"></span>
					<input type="password" class="form-control" name="password" id="password" autocomplete="off" placeholder="Enter password"/>
				    <div class="pass errorform"></div>
				</div>
				<div class="form-group">
					<label for="confPassword">confirma el password</label>
					<input type="password" class="form-control" name="confPassword" id="confPassword" autocomplete="off" placeholder="Enter conf password"/>
				</div>
			</div>
			<button type="submit" class="btn btn-azul">enviar</button>
		</form>
		<hr class="well-black"></hr>
		<h1><small>cambia tu foto de perfil</small></h1>
        <div class="form-group">
                <label for="imagen" class="btn btn-naranja"><i class="fa fa-folder"></i>&nbsp;&nbsp;buscar foto...</label>
				<input class="examinar" type="file" name="user_file[]" id="imagen">
				<div id="status1"></div>
		</div>
		<form role="form" enctype="multipart/form-data" id="formularioFoto">
			<!--<span>o si lo prefieres...</span>-->
			<div class="drop-files-container">
				 <!--<div>arrastra y suelta la imagen</div>-->
			</div>
		</form>

		<hr class="well-black"></hr>
		<button id="btnBaja" class="btn-small btn-rojo"><i class="fa fa-exclamation-triangle"></i>&nbsp;quiero darme de baja...</button>
  </div>
</div>

<div id="bajaCuenta" class="modalPanel">
	<h1><small><i class="fa fa-exclamation-triangle"></i>&nbsp;Baja de cuenta</small></h1>
	<hr class="well-black"></hr>
	<p>¿ Seguro que quieres dar de baja tu cuenta en Contact! ?</p>
	<form role="form" method="post" id="formularioBajaCuenta">
		<input type="hidden" name="okBj" value="<?= $_SESSION['contactUser']['user']['uid']; ?>" />
		<button type="submit" id="enviarBj" class="btn btn-rojo">si</button>
		<button id="closeBj" class="btn btn-azul">no</button>
	</form>
</div>