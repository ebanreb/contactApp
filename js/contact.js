/*COnfiguracion del cliente de Nodejs*/
var socket = io.connect('http://contact-xanela.rhcloud.com:8000');

// on connection to server, ask for user's name with an anonymous callback
	socket.on('connect', function(){
		//var usuarioLocal = $('#refYou').val();
		var usuarioLocal = $.cookie('lUser');
		socket.emit('adduser', usuarioLocal);
		getContactos();
		getSolicitudesRec();
  		getNumSolicitudes();
  		if($.cookie('activeContact')){
          listMsg();
  		}
	});

	// listener, whenever the server emits 'updatechat', this updates the chat body
	socket.on('updatesolicitudes', function (username) {
		getContactos();
		getSolicitudes();
		getSolicitudesRec();
		getNumSolicitudes();
	});

	socket.on('updatechat', function (remite,contenido) {
		//var destinatario = $('#activeContact').val();
		var destinatario = $.cookie('activeContact');
		document.title = 'Contact! ¬¬';
		if(destinatario == remite){
		   addChatLine(remite,contenido);
           listMsg();
		}else{
		   //getContactos();
		   getNumMensajes(remite);
		}
		setLastMsg(remite,contenido,remite);

	});

	socket.on('ledUpdate', function (username, valor) {
		updateLed(username, valor);
	});

	socket.on('contactsUpdate', function (username, valor) {
		var destinatario = $.cookie('activeContact');
		if(destinatario == username){
			clearChat();
		}
		getContactos();
	});
/*Fin configuracion del cliente Nodejs*/

function updateLed(username, valor){
	var strid = username.replace(".","");
	var color = (valor == 1) ? "rgba(53,170,71,1)" : "#CCCCCC" ;
	$('li#'+strid+' div.led').css("background-color",color);
	//console.log(color);
}

// Live Search
// On Search Submit and Get Results
function search() {
	var query_value = $('input#search').val();
	//$('b#search-string').html(query_value);
	if(query_value !== ''){
		var url = "app/service.php?tag=search"; // El script a dónde se realizará la petición.
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: url,
			data: { query: query_value },
			cache: false,
			success: function(data){
				
				if(data['success']==0){
                        var html = "";
					
						html += '<a class="list-group-item" target="_blank" href="javascript:void(0);">';
						html += '<b>No Results Found.</b>';
						html += 'Sorry :(';
						html += '</a>';
						
		        }else{
		        	var dataJson = eval(data['usuarios']);
                    var html = "";
		            for(var i in dataJson){
		                //alert(dataJson[i].ID + " _ " + dataJson[i].P + " _ " + dataJson[i].NV);
		                //cadena += "<div class=\"mensaje\">" + dataJson[i].mensaje + "</div>";
		                
						html += '<a id="searchItem'+ dataJson[i].name + '" class="searchData list-group-item" href="javascript:void(0);" onclick="searchData(this)">';
						html += '<i class="fa fa-share"></i>&nbsp;<b>' + dataJson[i].name + '</b>';
						html += '<input type="hidden" id="refSolUser" value="' + dataJson[i].uuid + '"  />';
						html += '</a>';
						
		            }
		        }
                
				$("div#results").html(html);
				$("div#results").getNiceScroll().resize();
			}
		});
	}return false;    
}

function searchData(tthis){
	    var element = $(tthis).attr("id")
		var url = "app/service.php?tag=enviarsolicitud"; // El script a dónde se realizará la petición.
        //var remite_value = $('#refYou').val();
        var remite_value = $.cookie('lUser');
        var destinatario_value = $('#' + element + ' #refSolUser').val();
		$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { remite: remite_value, destinatario: destinatario_value },
				cache: false,
				success: function(data){
					
					  /*if(data['success']==1){
                               $("#respuesta").html('solicitud enviada :)'); // Mostrar la respuestas del script PHP.
                               socket.emit('sendsolicitud', destinatario_value);
			          }else if(data['success']==0){
			           	   	   $("#respuesta").html("Error al enviar solicitud :("); // Mostrar la respuestas del script PHP.
			          }*/
			          switch(data['success']){
			          	case '0':
			          	  $("#respuesta").html("Error al enviar solicitud :(");
			          	break;
			          	case '1':
			          	  $("#respuesta").html('solicitud enviada :)');
			          	  getSolicitudes();
			          	  socket.emit('sendsolicitud', destinatario_value);
			          	break;
			          	case '2':
			          	  $("#respuesta").html('ya hay pendiente una solicitud referente a este usuario o ya estais en contacto ¬¬');
			          	break;
			          }
			          $("#info").css("display","block");
				}
		});

		return false;
	}

//retorna el numero de solicitudes
function getNumSolicitudes(){
  	 var url = "app/service.php?tag=numeroSolicitudes"; // El script a dónde se realizará la petición.
        //var usuarioLocal = $('#refYou').val();
        var usuarioLocal = $.cookie('lUser');
		$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { usuarioLocal: usuarioLocal },
				cache: false,
				success: function(data){
					
					  //if(data['success']==1){
                               $('div#cabecera .misSd').html(data['nSolicitudes']);
			          //}
				}
		});

		return false;
}

//listado de solicitudes
function getSolicitudes() {
		
			var url = "app/service.php?tag=getSolicitudes"; // El script a dónde se realizará la petición.
			//var usuarioLocal = $('#refYou').val();
			var usuarioLocal = $.cookie('lUser');
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { usuarioLocal: usuarioLocal },
				cache: false,
				success: function(data){
					
					if(data['success']==0){
                            var html = "";
						
							html += '<li class="list-group-item">';
							html += '<b>' + data['message'] + '</b>';
							html += '</li>';
							
			        }else{
			        	var dataJson = eval(data['solicitudes']);
                        var html = "";
                        var cad = "";
			            for(var i in dataJson){
			           
			                if(dataJson[i].remite == usuarioLocal){
                                html += '<li class="list-group-item">';
								html += '<div><span class="fa fa-chevron-left"></span> para <b>' + dataJson[i].destinatarioName + '</b></div>';
								html += '<button id="' + dataJson[i].id + '" class="btn btn-rojo btn-solicitud cancelar btnCancelar" href="javascript:void(0);" onclick="borrarSolicitude(this)">cancelar</button>';
								html += '<input type="hidden" id="refSolUser' + dataJson[i].id + '" value="' + dataJson[i].destinatario + '"  />';
								html += '</li>';
			                }else{
                                html += '<li class="list-group-item">';
								html += '<div><span class="fa fa-chevron-right"></span> de <b>' + dataJson[i].remiteName + '</b></div>';
								html += '<button id="' + dataJson[i].id + '" class="btn btn-rojo btn-solicitud cancelar btnRechazar" href="javascript:void(0);" onclick="borrarSolicitude(this)">rechazar</button>';
								html += '<button id="' + dataJson[i].id + '" class="btn btn-azul btn-solicitud aceptar btnAceptar" href="javascript:void(0);" onclick="aceptarSolicitude(this)">aceptar</button>';
								html += '<input type="hidden" id="refSolUser' + dataJson[i].id + '" value="' + dataJson[i].remite + '"  />';
								html += '</li>';
			                }
								
			            }
			        }

					$("ul#solicitudes").html(html);
				}
			});

		return false;    
}

//listado de solicitudes recibidas
function getSolicitudesRec() {
		
			var url = "app/service.php?tag=getSolicitudesRec"; // El script a dónde se realizará la petición.
			//var usuarioLocal = $('#refYou').val();
			var usuarioLocal = $.cookie('lUser');
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { usuarioLocal: usuarioLocal },
				cache: false,
				success: function(data){
					
					if(data['success']==0){
                            var html = "";
						
							html += '<li class="list-group-item">';
							html += '<b>' + data['message'] + '</b>';
							html += '</li>';
							
			        }else{
			        	var dataJson = eval(data['solicitudes']);
                        var html = "";
                        var cad = "";
			            for(var i in dataJson){
			        
                                html += '<li class="list-group-item">';
								html += '<div><span class="fa fa-chevron-right"></span> de <b>' + dataJson[i].remiteName + '</b></div>';
								html += '<button id="' + dataJson[i].id + '" class="btn-small btn-rojo btn-solicitud cancelar btnRechazar" href="javascript:void(0);" onclick="borrarSolicitude(this)">rechazar</button>';
								html += '<button id="' + dataJson[i].id + '" class="btn-small btn-azul btn-solicitud aceptar btnAceptar" href="javascript:void(0);" onclick="aceptarSolicitude(this)">aceptar</button>';
								html += '<input type="hidden" id="refSolUser' + dataJson[i].id + '" value="' + dataJson[i].remite + '"  />';
								html += '</li>';
								
			            }
			        }

					$("ul#solicitudesRec").html(html);
					$("ul#solicitudesRec").getNiceScroll().resize();
				}
			});

		return false;    
}

//borrar solicitud
function borrarSolicitude(tthis){
	    var sid = $(tthis).attr("id");
  	    var url = "app/service.php?tag=borrarsolicitud"; // El script a dónde se realizará la petición.
        var pushTo = $('#refSolUser' + sid).val();
		$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { solicitud: sid },
				cache: false,
				success: function(data){
					
					  if(data['success']==1){
                            getSolicitudes();
                            getSolicitudesRec();
                            getNumSolicitudes();
			          	    socket.emit('sendsolicitud', pushTo);   
			          }
				}
		});

		return false;
}

//aceptar solicitud
function aceptarSolicitude(tthis){
	    var sid = $(tthis).attr("id");
  	    var url = "app/service.php?tag=aceptarsolicitud"; // El script a dónde se realizará la petición.
        var pushTo = $('#refSolUser' + sid).val();
		$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { solicitudId: sid },
				cache: false,
				success: function(data){
					
					  if(data['success']==1){
					  	    getContactos();
                            getSolicitudes();
                            getSolicitudesRec();
                            getNumSolicitudes();
			          	    socket.emit('sendsolicitud', pushTo);   
			          }
				}
		});

		return false;
}

//listado de contactos
function getContactos() {
		
			var url = "app/service.php?tag=allusers"; // El script a dónde se realizará la petición.
			//var usuarioLocal = $('#refYou').val();
			var usuarioLocal = $.cookie('lUser');
			//var destinatario = $('#activeContact').val();
			var destinatario = $.cookie('activeContact');
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { usuarioLocal: usuarioLocal },
				cache: false,
				success: function(data){
					
					if(data['success']==1){
			        	var dataJson = eval(data['usuarios']);
			        	socket.emit('siConectado', usuarioLocal, dataJson);
                        var html = "";
			            for(var i in dataJson){
			            	    var r = ~~(Math.random()*1000);
			            	    var foto = (dataJson[i].foto == 1) ? 'images/contact/min/' + dataJson[i].uuid + '.jpg?' + r : 'images/user_icon-40x40.jpg' ;
			                    var strid = dataJson[i].uuid.replace(".","");
			                    var classNodo = ( dataJson[i].uuid == destinatario ) ? 'active2' : '';
			                    var ultimoMsg = "";
			                    if(dataJson[i].ltsMensaje != null){
			                         ultimoMsg += ( dataJson[i].ltsRemite ==  dataJson[i].uuid) ? '<i class="fa fa-share"></i>' : '<i class="fa fa-reply"></i>';
			                         ultimoMsg += '&nbsp;' + dataJson[i].ltsMensaje;
			                    }

                                
                                html += '<li id="' + strid + '">';
								html += '<a id="' + dataJson[i].uuid + '" class="' + classNodo + '" href="">';
								html += '<div class="imgLst"><img src="' + foto + '" class="icContacto img-rounded"></div>';
								html += '<div class="datosLst"><span class="nameContact">' + dataJson[i].name + '</span>  <span class="misSd badge">' + dataJson[i].visto + '</span>';
								html += '<div class="lastMsg cortar">' + ultimoMsg +  '</div></div>';
								html += '</a>';
								html += '<div class="led"></div>';
								html += '</li>';

			            }//Fin for
			        }else{
			        	var html = "";
						
						html += '<li>';
						html += '<b>' + data['message'] + '</b>';
						html += '</li>';
			        }

                    /*
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
			        html+='<li id="53dfe779c189e289366886"><a id="53dfe779c189e2.89366886" class="" href=""><img src="images/contact/min/53dfe779c189e2.89366886.jpg?undefined" class="icContacto img-rounded">prueba1  <span class="misSd badge"></span></a><div class="led"></div></li>';
                    */
                    
					$("ul#listContactos").html(html);
					$(".sidebar-nav .navbar").getNiceScroll().resize();
				}
			});

		return false;    
}

//enviar mensaje
function sendMsg() {
		
			var url = "app/service.php?tag=submitmsg"; // El script a dónde se realizará la petición.
			//var usuarioLocal = $('#refYou').val();
			var usuarioLocal = $.cookie('lUser');
			//var destinatario = $('#activeContact').val();
			var destinatario = $.cookie('activeContact');
			var contenido = $("textarea#sendmsg").val();
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { remite: usuarioLocal,destinatario: destinatario, contenido: contenido },
				cache: false,
				success: function(data){
					if(data['success']==1){

						 $("textarea#sendmsg").val('');
						 addChatLine(usuarioLocal,contenido);
						 setLastMsg(usuarioLocal,contenido,destinatario);
						 socket.emit('sendmensaje', usuarioLocal,destinatario,contenido);
						 listMsg();

					}
				}
			});

		return false;    
}

//obtener los mensajes
var LIMITE = 50;
function listMsg() {
		    
		    var setLimit = LIMITE;
			var url = "app/service.php?tag=getMensajes"; // El script a dónde se realizará la petición.
			//var usuarioLocal = $('#refYou').val();
			var usuarioLocal = $.cookie('lUser');
			//var destinatario = $('#activeContact').val();
			var destinatario = $.cookie('activeContact');
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { remite: usuarioLocal,destinatario: destinatario,limit: setLimit},
				cache: false,
				success: function(data){
					var html = "";
					if(data['success']==1){
			        	var dataJson = eval(data['mensajes']);
			            for(var i in dataJson){
			            	    var image = ''; 
			            	    /*if( dataJson[i].id_remite != usuarioLocal ){
			            	    	var strid = destinatario.replace(".","");
			            	    	var rtFoto = $('li#'+strid+' img').attr('src');
			            	    	image = '<img src="'+rtFoto+'" class="icContacto img-rounded" />';
			            	    }*/
                                var classNodo = ( dataJson[i].id_remite == usuarioLocal ) ? 'nodoRemite' : 'nodoDestinatario';
                                var contenido = dataJson[i].contenido.replace(new RegExp("\n","g"), "<br>");
								html += '<div class="nodoChat ' + classNodo + '">' + image + contenido + '</div>';

			            }//Fin for
			        }

					$("div#contenido").html(html);
					$("div#chat div.panel-body").getNiceScroll().resize();
					$("div#chat div.panel-body").scrollTop($('div#chat div.panel-body')[0].scrollHeight);
					$("div#chat div.panel-body").jLinkify();

					$('textarea#sendmsg').css("display","block");
					//getContactos();
					//$("div#chat div.panel-body").animate({ scrollTop: $('div#chat div.panel-body')[0].scrollHeight}, 1000);
				}
			});

		return false;    
}

//obtener num mensajes sin ver
function getNumMensajes(uuid){
            var url = "app/service.php?tag=getNumMensajes"; // El script a dónde se realizará la petición.
			//var usuarioLocal = $('#refYou').val();
			var usuarioLocal = $.cookie('lUser');
			var destinatario = uuid;
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: url,
				data: { remite: uuid,destinatario: usuarioLocal},
				cache: false,
				success: function(data){
					var html = "";
					if(data['success']==1){
			        	var liId = uuid.replace(".","");
			        	$('li#'+liId+' span.misSd').html(data['visto']);
			        }

				}
			});

		return false;   
}

function setLastMsg(remite,contenido,element){
  var usuarioLocal = $.cookie('lUser');
  var liId = element.replace(".","");
  var ultimoMsg = ( remite !=  usuarioLocal) ? '<i class="fa fa-share"></i>' : '<i class="fa fa-reply"></i>';
  var tHtml = ultimoMsg + '&nbsp;' + contenido;
  $('li#'+liId+' div.datosLst div.lastMsg').html(tHtml);
}

function addChatLine(remite,contenido){
	//var usuarioLocal = $('#refYou').val();
	var usuarioLocal = $.cookie('lUser');
	var classNodo = ( remite == usuarioLocal ) ? 'nodoRemite' : 'nodoDestinatario';
    var cont = contenido.replace(new RegExp("\n","g"), "<br>");
	var html = $("div#contenido").html();
	var image = ''; 
    if( remite != usuarioLocal ){
    	var strid = remite.replace(".","");
    	/*var rtFoto = $('li#'+strid+' img').attr('src');
    	image = '<img src="'+rtFoto+'" class="icContacto img-rounded" />';*/
    }
	html += '<div class="nodoChat ' + classNodo + '">' + cont + '</div>';
	$("div#contenido").html(html);
	$("div#chat div.panel-body").getNiceScroll().resize();
	$("div#chat div.panel-body").scrollTop($('div#chat div.panel-body')[0].scrollHeight);
	$("div#chat div.panel-body").jLinkify();
}

function cabChat(id){
  var strid = id.replace(".","");
  var activeUserImg = $('li#'+strid+' img').attr('src');
  var activeUserName = $('li#'+strid+' .nameContact').html();
  var image = '<img src="'+activeUserImg+'" class="icContacto img-rounded" />';
  $("div#chat div.panel-heading").html(image+activeUserName);
}

function clearChat(){
	$("div#contenido").html('');
	$("div#chat div.panel-body").getNiceScroll().resize();
	$("div#chat div.panel-heading").html('');
}

//Declaracion de eventos
$(function(){
  
  $("div#chat div.panel-body").niceScroll({cursorcolor:"rgba(51,51,51,1)",cursoropacitymax:0.7,boxzoom:false});
  $(".sidebar-nav .navbar").niceScroll({cursorcolor:"rgba(51,51,51,1)",cursoropacitymax:0.7,boxzoom:false});
  $("div#results").niceScroll({cursorcolor:"rgba(51,51,51,1)",cursoropacitymax:0.7,boxzoom:false});
  $("ul#solicitudesRec").niceScroll({cursorcolor:"rgba(51,51,51,1)",cursoropacitymax:0.7,boxzoom:false});

  // Icon Click Focus
  $('.input-group-addon').click(function(){
		$('input#search').focus();
  });

  $("input#search").on("keyup", function(e) {
		// Set Timeout
		clearTimeout($.data(this, 'timer'));

		// Set Search String
		var search_string = $(this).val();

		// Do Search
		if (search_string == '') {
			$("div#results").fadeOut();
		}else{
			$("div#results").fadeIn();
			$(this).data('timer', setTimeout(search, 100));
		};
	});

	$("input#search").focusout(function () {
      $("div#results").fadeOut();
	});

	$("textarea#sendmsg").keydown(function(e){
	    // Enter was pressed without shift key
	    if (e.keyCode == 13 && !e.shiftKey)
	    {
	    	var contenido = $("textarea#sendmsg").val();
	    	contenido = contenido.replace(/^\s+|\s+$/g,'');
	    	e.preventDefault();
            if(contenido!="")
	           sendMsg();
	    }
    });

    $('ul#listContactos').on("click","li a",function(e){
    	e.preventDefault();

        //var estado = $('.navbar-header').css('display');
        var estado = $('div#chat').css('display');

    	//var last = $('#activeContact').val();
		var userId = $(this).attr("id");
		//$('#activeContact').val(userId);
		$.cookie('activeContact', userId, { path: '/' });
		$('ul#listContactos li a').removeClass("active2");

        cabChat(userId);

		listMsg();
		//$('textarea#sendmsg').css("display","block");
		getNumMensajes(userId);
		$(this).addClass("active2");
		//$('textarea#sendmsg').focus();
		
		//if(estado=="block"){
		if(estado=="none"){
           $(".sidebar-nav").css({'display':'none'});
           $("div#chat").css({'display':'block'});
		}
	});

	$(window).focus(function(){
		document.title = 'Contact!';
	});


	$('a#logout').click(function(e){
       e.preventDefault();
       //var usuarioLocal = $('#refYou').val();
       var usuarioLocal = $.cookie('lUser');
       var href = $(this).attr('href');
       socket.emit('siDesconectado', usuarioLocal);
       document.location = href;
	});

	$('button#enviarBj').click(function(e){
		e.preventDefault();
		var usuarioLocal = $.cookie('lUser');
		var url = "app/service.php?tag=bajaCuenta"; // El script a dónde se realizará la petición.
	    $.ajax({
	           type: "POST",
	           dataType: 'json',
	           url: url,
	           data: $("#formularioBajaCuenta").serialize(), // Adjuntar los campos del formulario enviado.
	           success: function(data)
	           {

                      $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
	           	      $("#info").css("display","block");
	           	      socket.emit('siBaja', usuarioLocal);
	           	      
	           	      if(data['success']==1){
	           	      	window.location = data['url'];
	           	      }

	           }
	         });
	});

});