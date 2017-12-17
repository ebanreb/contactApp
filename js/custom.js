/* JS File */

// Start Ready
$(document).ready(function() {

	if($.cookie('activeContact'))
	{
  	  $.removeCookie('activeContact', { path: '/' });
    }
    
    $('.navbar-header').click(function(e){
				e.preventDefault();
				$('ul.dropdown-menu').toggle();
	});  

	$("#formularioDatos").submit(function(){
                
                if(validaFormCnf()){
				    var url = "app/service.php?tag=cnf"; // El script a dónde se realizará la petición.
				    $.ajax({
				           type: "POST",
				           dataType: 'json',
				           url: url,
				           data: $("#formularioDatos").serialize(), // Adjuntar los campos del formulario enviado.
				           success: function(data)
				           {

				           	   if(data['success']==1){
                                  $('input#confEmail').val('');
                                  $('input#password').val('');
			 					  $('input#confPassword').val('');
			 					  $('#modEmail').prop('checked', false);
			 					  $("div#group-email").fadeOut();
			 					  $('#modPass').prop('checked', false);
			 					  $("div#group-password").fadeOut();
			 					  $('#yo').html(data['name']);
				           	   }

				           	   $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
				           	   $("#info").css("display","block");
	   
				           }
				         });
			    } 

			    return false; // Evitar ejecutar el submit del formulario.
	});

	/*
	*imagen de perfil
	*/
	var tests = {
	      filereader: typeof FileReader != 'undefined',
	      dnd: 'draggable' in document.createElement('span'),
	      formdata: !!window.FormData,
	      progress: "upload" in new XMLHttpRequest
	    }, 
	    support = {
	      filereader: document.getElementById('filereader'),
	      formdata: document.getElementById('formdata'),
	      progress: document.getElementById('progress')
	    },
	    acceptedTypes = {
	      'image/png': true,
	      'image/jpeg': true,
	      'image/gif': true
	    }

	function previewfile(file) {
	  if (tests.filereader === true && acceptedTypes[file.type] === true) {
	    var reader = new FileReader();
	    reader.onload = function (event) {
	      var image = new Image();
	      image.src = event.target.result;
	      image.width = 250; // a fake resize
	      $(".drop-files-container").html(image);
	      //console.log(image);
	    };

	    reader.readAsDataURL(file);
	    //console.log(file);
	  }  else {
	    $(".drop-files-container").innerHTML += '<p>Uploaded ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '');
	    console.log(file);
	  }
	}


	function processFileUpload(droppedFiles) {
	         // add your files to the regular upload form
	    //var uploadFormData = new FormData($("#formularioFoto")[0]); 
	    if(droppedFiles.length > 0) { // checks if any files were dropped
	        for(f = 0; f < droppedFiles.length; f++) { // for-loop for each file dropped
	        	var uploadFormData = new FormData($("#formularioFoto")[0]); 
	            uploadFormData.append("user_file[]",droppedFiles[f]);  // adding every file to the form so you could upload multiple files
	            previewfile(droppedFiles[f]);
	            var status = new createStatusbar($(".drop-files-container")); //Using this we can set progress.
		        status.setFileNameSize(droppedFiles[f].name,droppedFiles[f].size);
		        sendFileToServer(uploadFormData,status);

	        }
	    }

	 // the final ajax call

	       /*$.ajax({
	        url : "app/service.php?tag=foto", // use your target
	        type : "POST",
	        dataType: 'json',
	        data : uploadFormData,
	        cache : false,
	        contentType : false,
	        processData : false,
	        success : function(data) {
	                 $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
					 $("#info").css("display","block");
	        }
	       });*/

	 }

	function sendFileToServer(formData,status)
	{
	    var uploadURL ="app/service.php?tag=foto"; //Upload URL
	    var extraData ={}; //Extra Data.
	    var jqXHR=$.ajax({
	            xhr: function() {
	            var xhrobj = $.ajaxSettings.xhr();
	            if (xhrobj.upload) {
	                    xhrobj.upload.addEventListener('progress', function(event) {
	                        var percent = 0;
	                        var position = event.loaded || event.position;
	                        var total = event.total;
	                        if (event.lengthComputable) {
	                            percent = Math.ceil(position / total * 100);
	                        }
	                        //Set progress
	                        status.setProgress(percent);
	                    }, false);
	                }
	            return xhrobj;
	        },
	    url: uploadURL,
	    type: "POST",
	    contentType:false,
	    processData: false,
	        cache: false,
	        dataType: 'json',
	        data: formData,
	        success: function(data){
	            status.setProgress(100);
	            
	            if(data['success']==1){
	            	var src = $(".drop-files-container img").attr("src");
	            	$('img#miFoto').attr("src",src);
	            	$('img#miFoto').attr("width",'40');
	            }

	            //$("#status1").append("File upload Done<br>");        
	            $("#respuesta").html(data['msg']); // Mostrar la respuestas del script PHP.
			    $("#info").css("display","block");
	        }
	    });
	 
	    status.setAbort(jqXHR);
	}

	var rowCount=0;
	function createStatusbar(obj)
	{
	     rowCount++;
	     var row="odd";
	     if(rowCount %2 ==0) row ="even";
	     this.statusbar = $("<div class='statusbar "+row+"'></div>");
	     this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
	     this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
	     this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
	     this.abort = $("<div class='abort'>cancelar</div>").appendTo(this.statusbar);
	     obj.after(this.statusbar);
	 
	    this.setFileNameSize = function(name,size)
	    {
	        var sizeStr="";
	        var sizeKB = size/1024;
	        if(parseInt(sizeKB) > 1024)
	        {
	            var sizeMB = sizeKB/1024;
	            sizeStr = sizeMB.toFixed(2)+" MB";
	        }
	        else
	        {
	            sizeStr = sizeKB.toFixed(2)+" KB";
	        }
	 
	        this.filename.html(name);
	        this.size.html(sizeStr);
	    }
	    this.setProgress = function(progress)
	    {      
	        var progressBarWidth =progress*this.progressBar.width()/ 100; 
	        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
	        if(parseInt(progress) >= 100)
	        {
	            this.abort.hide();
	        }
	    }
	    this.setAbort = function(jqxhr)
	    {
	        var sb = this.statusbar;
	        this.abort.click(function()
	        {
	            jqxhr.abort();
	            sb.hide();
	        });
	    }
	}
	/*
	*Fin imagen de perfil
	*/

    /*$("#modName").click(function() {  
        if($(this).is(':checked')) {  
              $("div#group-name").fadeIn();
        } else {  
             $("div#group-name").fadeOut();
        }  
    }); */ 

	$("#modEmail").click(function() {  
        if($(this).is(':checked')) {  
              $("div#group-email").fadeIn();
        } else {  
             $("div#group-email").fadeOut();
        }  
    });

    $("#modPass").click(function() {  
        if($(this).is(':checked')) {  
              $("div#group-password").fadeIn();
        } else {  
             $("div#group-password").fadeOut();
        }  
    });

    $('#btnBaja').click(function(e){
		e.preventDefault();
		$('div#backPopAp').toggle(function(){
			$('div#bajaCuenta').toggle();
		});
    });

    $('button#closeBj').click(function(e){
		e.preventDefault();
		$('div#bajaCuenta').toggle(function(){
			$('div#backPopAp').toggle();
		});
	});

	/*$('button#enviarBj').click(function(e){
		e.preventDefault();
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

	           	      if(data['success']==1){
	           	      	window.location = data['url'];
	           	      }

	           }
	         });
	});*/

    /*$(".drop-files-container").on("drop", function(e) {
    	e.stopPropagation();
        e.preventDefault();
	    processFileUpload(e.originalEvent.dataTransfer.files); 
	    // forward the file object to your ajax upload method
	    return false;
	});

	$(".drop-files-container").on("dragover", function(e) {
    	e.stopPropagation();
        e.preventDefault();
	    $(this).addClass('hover')
	    return false;
	});*/

	$('input#imagen').on("change",function(){
		processFileUpload(this.files);
	});

    checkWidth();
    $(window).resize(checkWidth);

    $('ul.navbar-header').click(function(e) {
    	e.preventDefault();
		$('ul.dropdown-menu').toggle();
    });

});

function checkWidth() {
	    var $window = $(window);
        var windowsize = $window.width();
        var controlWidth01 = 1023;
    	var controlWidth02 = 767;

        if (windowsize < controlWidth01) {
           $('ul.dropdown-menu').css({'display':'none'});
           if(windowsize < controlWidth02){
               //var destinatario = $('#activeContact').val();
	           //if(destinatario!=""){
	           if($.cookie('activeContact')){
	           	  $(".sidebar-nav").css({'display':'none'});
	           	  $("div#chat").css({'display':'block'});
	           }
           }else{
        	 $(".sidebar-nav").css({'display':'block'});
        	 //$('ul.dropdown-menu').css({'display':'block'});
           }
        }else{
        	 $(".sidebar-nav").css({'display':'block'});
        	 $('ul.dropdown-menu').css({'display':'block'});
        }
}

function validaFormCnf(){
					$('div.errorform').html('');
				 	var name = $('input#nombre').val();
				 	var email = $('input#email').val();
				 	var confemail = $('input#confEmail').val();
				 	var pass = $('input#password').val();
				 	var confpass = $('input#confPassword').val();
				 	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

	                
	                if(name==''){
	                   $('div.name').html('el nombre es obligatorio >:(');
	                   return false;
	                }

	                if($('#modEmail').is(':checked')){
	                	if(email==''){
		                   $('div.email').html('el email es obligatorio! >:(');
		                   return false;
		                }else if(!emailReg.test(email)) {
				           $('div.email').html('el email no es correcto! >:(');
		                   return false;
				        }else if(email != confemail){
		                   $('div.email').html('los emails no coinciden! >:(');
		                   return false;
				        }
	                }

			        if($('#modPass').is(':checked')){
			        	if(pass==''){
		                   $('div.pass').html('el password es obligatorio! >:(');
		                   return false;
		                }/*else if(pass.length < 6) {
				           $('div.pass').html('el password debe tener un minimo de 6 caracteres! >:(');
		                   return false;
				        }*/else if(pass != confpass){
		                   $('div.pass').html('los passwords no coinciden! >:(');
		                   return false;
				        }
			        }

			        return true;
	}//FIn valida form conf

