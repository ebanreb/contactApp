$(document).ready(function() {
        	 //$.ajaxSetup({cache: false});

        	 if($.cookie('lUser')){
	   	   	  	$.removeCookie('lUser', { path: '/' });
	   	   	 }

        	 $("#formularioVerificacion").submit(function(){
                    
                    $("#formularioVerificacion button").css({
	                	'padding-left':'22px',
	                	'background-image': 'url("images/load03.gif")',
					    'background-position': '6px center',
					    'background-repeat': 'no-repeat'
	                });

				    var url = "app/service.php?tag=verificacion"; // El script a dónde se realizará la petición.
				    $.ajax({
				           type: "POST",
				           dataType: 'json',
				           url: url,
				           data: $("#formularioVerificacion").serialize(), // Adjuntar los campos del formulario enviado.
				           success: function(data)
				           {

				           	   $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
				           	   $("#info").css("display","block");

				           	   $("#formularioVerificacion button").css({
				                	'background-image': 'none',
				                	'padding-left':'12px'
				               });

				           	   if(data['success']==1){
				           	   	  $.cookie('lUser', data['lUser'], { path: '/' });
				           	   	  if($.cookie('activeContact')){
				           	   	  	$.removeCookie('activeContact', { path: '/' });
				           	   	  }
                                  window.location = data['url'];
				           	   }
	   
				           }
				         });

			    return false; // Evitar ejecutar el submit del formulario.
			 });
            
             $("#formularioRegistro").submit(function(){
                
                if(validaFormRegistro()){

                    $("#formularioRegistro button").css({
	                	'padding-left':'22px',
	                	'background-image': 'url("images/load03.gif")',
					    'background-position': '6px center',
					    'background-repeat': 'no-repeat'
	                });

				    var url = "app/service.php?tag=register"; // El script a dónde se realizará la petición.
				    $.ajax({
				           type: "POST",
				           dataType: 'json',
				           url: url,
				           data: $("#formularioRegistro").serialize(), // Adjuntar los campos del formulario enviado.
				           success: function(data)
				           {
				           	   /*if(data['success']==1){
	                               $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
				           	   }else if(data['success']==0){
				           	   	   $("#respuesta").html("Error de registro."); // Mostrar la respuestas del script PHP.
				           	   }*/

				           	   if(data['success']==1){
                                  limpiaCampos();
				           	   }

				           	   $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
				           	   $("#info").css("display","block");

				           	   $("#formularioRegistro button").css({
				                	'background-image': 'none',
				                	'padding-left':'12px'
				               });
	   
				           }
				         });
			    } 

			    return false; // Evitar ejecutar el submit del formulario.
			 });

             $('#showLog').click(function(e){
				e.preventDefault();
				$('div#formLogin').toggle();
			 });

             $('#recPass').click(function(e){
				e.preventDefault();
				$('div#backPopAp').toggle(function(){
					$('div#newPass').toggle();
				});
			 });

			 $('button#closeNewPass').click(function(e){
				e.preventDefault();
				$('div#newPass').toggle(function(){
					$('div#backPopAp').toggle();
				});
			 });

			 $('#acNewPass').click(function(e){
				e.preventDefault();
				$('div#newPass').toggle(function(){
					$('div#modPC').toggle();
				});
			 });

			 $('button#closeModPC').click(function(e){
				e.preventDefault();
				$('div#modPC').toggle(function(){
					$('div#newPass').toggle();
				});
			 });
             
             $("#formularioLogin").submit(function(){

             	var start = Date.now();
             	var dt = new timezoneJS.Date(start);
                var GMT = dt.getTimezoneOffset() / 60;
                $('input#userGMT').val(GMT);

                $("#formularioLogin button").css({
                	'padding-left':'22px',
                	'background-image': 'url("images/load03.gif")',
				    'background-position': '6px center',
				    'background-repeat': 'no-repeat'
                });
 
			    var url = "app/service.php?tag=login"; // El script a dónde se realizará la petición.
			    $.ajax({
			           type: "POST",
			           dataType: 'json',
			           url: url,
			           data: $("#formularioLogin").serialize(), // Adjuntar los campos del formulario enviado.
			           success: function(data)
			           {
			           	   if(data['success']==1){
			           	   	  $.cookie('lUser', data['lUser'], { path: '/' });
			           	   	  if($.cookie('activeContact')){
			           	   	  	$.removeCookie('activeContact', { path: '/' });
			           	   	  }
                              window.location = data['url'];
			           	   }

			           	   $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
			           	   $("#info").css("display","block");

			           	   $("#formularioLogin button").css({
			                	'background-image': 'none',
			                	'padding-left':'12px'
			               });
			               
			           }
			         });

			    return false; // Evitar ejecutar el submit del formulario.
			});

            $("#formularioPassWord").submit(function(){

                $("#formularioPassWord button.btn-azul").css({
                	'padding-left':'22px',
                	'background-image': 'url("images/load03.gif")',
				    'background-position': '6px center',
				    'background-repeat': 'no-repeat'
                });
 
			    var url = "app/service.php?tag=recPass"; // El script a dónde se realizará la petición.
			    $.ajax({
			           type: "POST",
			           dataType: 'json',
			           url: url,
			           data: $("#formularioPassWord").serialize(), // Adjuntar los campos del formulario enviado.
			           success: function(data)
			           {
			           	   if(data['success']==1){
			           	   	 
			           	   }

			           	   $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
			           	   $("#info").css("display","block");

			           	   $("#formularioPassWord button.btn-azul").css({
			                	'background-image': 'none',
			                	'padding-left':'12px'
			               });
			               
			           }
			         });

			    return false; // Evitar ejecutar el submit del formulario.
			});

            $("#formularioPassWordCod").submit(function(){
                if(validaFormPassWordCod()){
                	$("#formularioPassWordCod button.btn-azul").css({
                	'padding-left':'22px',
                	'background-image': 'url("images/load03.gif")',
				    'background-position': '6px center',
				    'background-repeat': 'no-repeat'
	                });
	 
				    var url = "app/service.php?tag=passByCod"; // El script a dónde se realizará la petición.
				    $.ajax({
				           type: "POST",
				           dataType: 'json',
				           url: url,
				           data: $("#formularioPassWordCod").serialize(), // Adjuntar los campos del formulario enviado.
				           success: function(data)
				           {
				           	   if(data['success']==1){
				           	   	 $("#formularioPassWordCod input").val('');
				           	   	 $('div#modPC').toggle(function(){
									$('div#backPopAp').toggle();
								 });
				           	   }

				           	   $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
				           	   $("#info").css("display","block");

				           	   $("#formularioPassWordCod button.btn-azul").css({
				                	'background-image': 'none',
				                	'padding-left':'12px'
				               });
				               
				           }
				         });
                }
                
			    return false; // Evitar ejecutar el submit del formulario.
			});
            
});

function validaFormRegistro(){
				$('#formularioRegistro div.errorform').html('');
			 	var name = $('#formularioRegistro input#nombre').val();
			 	var email = $('#formularioRegistro input#email').val();
			 	var confemail = $('#formularioRegistro input#confEmail').val();
			 	var pass = $('#formularioRegistro input#password').val();
			 	var confpass = $('#formularioRegistro input#confPassword').val();
			 	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

                
                if(name==''){
                   $('#formularioRegistro div.name').html('el nombre es obligatorio >:(');
                   return false;
                }

                if(email==''){
                   $('#formularioRegistro div.email').html('el email es obligatorio! >:(');
                   return false;
                }else if(!emailReg.test(email)) {
		           $('#formularioRegistro div.email').html('el email no es correcto! >:(');
                   return false;
		        }else if(email != confemail){
                   $('#formularioRegistro div.email').html('los emails no coinciden! >:(');
                   return false;
		        }

		        if(pass==''){
                   $('#formularioRegistro div.pass').html('el password es obligatorio! >:(');
                   return false;
                }/*else if(pass.length < 6) {
		           $('div.pass').html('el password debe tener un minimo de 6 caracteres! >:(');
                   return false;
		        }*/else if(pass != confpass){
                   $('#formularioRegistro div.pass').html('los passwords no coinciden! >:(');
                   return false;
		        }

		        if($('#formularioRegistro #privacidad').prop('checked')!=true){
		        	$('#formularioRegistro div.priv').html('debes de leer y aceptar la política de privacidad! >:(');
                    return false;
		        }

		        return true;
}//FIn valida form redistro

function validaFormPassWordCod(){
				$('#formularioPassWordCod div.errorform').html('');
				var codigo = $('#formularioPassWordCod input#codigo').val();
			 	var email = $('#formularioPassWordCod input#email').val();
			 	var pass = $('#formularioPassWordCod input#modPassword').val();
			 	var confpass = $('#formularioPassWordCod input#confPassword').val();
			 	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

                if(email==''){
                   $('#formularioPassWordCod div.email').html('el email es obligatorio! >:(');
                   return false;
                }else if(!emailReg.test(email)) {
		           $('#formularioPassWordCod div.email').html('el email no es correcto! >:(');
                   return false;
		        }

		        if(pass==''){
                   $('#formularioPassWordCod div.pass').html('el password es obligatorio! >:(');
                   return false;
                }/*else if(pass.length < 6) {
		           $('div.pass').html('el password debe tener un minimo de 6 caracteres! >:(');
                   return false;
		        }*/else if(pass != confpass){
                   $('#formularioPassWordCod div.pass').html('los passwords no coinciden! >:(');
                   return false;
		        }

		        if(codigo==''){
                   $('#formularioPassWordCod  div.codigo').html('el codigo es obligatorio >:(');
                   return false;
                }

		        return true;
}//FIn valida form redistro

function limpiaCampos(){
	var name = $('input#nombre').val('');
 	var email = $('input#email').val('');
 	var confemail = $('input#confEmail').val('');
 	var pass = $('input#password').val('');
 	var confpass = $('input#confPassword').val('');
 	$('#privacidad').prop('checked', false);
 	$('#result').fadeOut();
 	$('div.errorform').html('');
}