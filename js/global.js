$(document).ready(function() {
    $('div#closeinfo').click(function(e){
				e.preventDefault();
		        $("#info").css("display","none");
		        $("#respuesta").html("");
	});

	$('#password').keyup(function(){
			    var valPass = $(this).val();
			    var obj = $('#result');
				$('#result').html(checkStrength($('#password').val(), obj));
				( valPass == '' ) ? $('#result').fadeOut() : $('#result').fadeIn();
	});

	$('#modPassword').keyup(function(){
			    var valPass = $(this).val();
			    var obj = $('#resultModP');
				$('#resultModP').html(checkStrength($('#modPassword').val(), obj));
				( valPass == '' ) ? $('#resultModP').fadeOut() : $('#resultModP').fadeIn();
	});

	$('a#polPrivacidad, a#fPrivacidad').click(function(e){
			e.preventDefault();
			$('div#backPopUp').css("display","block");
			$('div#popUpAviso').fadeIn();
			var ancho = $('div#popUpAviso').width();
		    $('div#popUpAviso').css('margin-left',(ancho/2)*-1);
	});

	$('div#backPopUp').click(function(){
            $('div#popUpAviso').css("display","none");
			$(this).fadeOut()
	});

	if (!$.cookie("msgcookie"))
	{
			$("body").prepend("<div class='msgcookie'><p>Esta web utiliza 'cookies' propias para ofrecerte una mejor experiencia y servicio. Al navegar o utilizar nuestros servicios el usuario acepta el uso que hacemos de las 'cookies'. <a href='#'>M&aacute;s informaci&oacute;n</a>&nbsp;|&nbsp;<a href='#' class='cerrar'>cerrar mensaje</a></p></div>");
			
			$("body").on("click", ".cerrar", function(e) {
				e.preventDefault();
				$.cookie('msgcookie', 'aceptado');
				$(".msgcookie").fadeOut();
			});

			$("body").on("click", ".msgcookie p a:not(.cerrar)", function(e) {
				e.preventDefault();
				$('div#backPopUp').css("display","block");
				$('div#popUpAviso').fadeIn();
				var ancho = $('div#popUpAviso').width();
		        $('div#popUpAviso').css('margin-left',(ancho/2)*-1);
			});
			
	}

	$(window).resize(function(){
	 var ancho = $('div#popUpAviso').width();
	 $('div#popUpAviso').css('margin-left',(ancho/2)*-1);
	});

});

function checkStrength(password, obj){
		 
		    //initial strength
		    var strength = 0
		 
		    //if the password length is less than 6, return message.
		    if (password.length < 6) {
		        obj.removeClass()
		        obj.addClass('short')
		        return 'demasiado corto'
		    }
		 
		    //length is ok, lets continue.
		 
		    //if length is 8 characters or more, increase strength value
		    if (password.length > 7) strength += 1
		 
		    //if password contains both lower and uppercase characters, increase strength value
		    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 1
		 
		    //if it has numbers and characters, increase strength value
		    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  strength += 1 
		 
		    //if it has one special character, increase strength value
		    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 1
		 
		    //if it has two special characters, increase strength value
		    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) strength += 1
		 
		    //now we have calculated strength value, we can return messages
		 
		    //if value is less than 2
		    if (strength < 2 ) {
		        obj.removeClass()
		        obj.addClass('weak')
		        return 'débil'
		    } else if (strength == 2 ) {
		        obj.removeClass()
		        obj.addClass('good')
		        return 'buena'
		    } else {
		        obj.removeClass()
		        obj.addClass('strong')
		        return 'fuerte'
		    }
}