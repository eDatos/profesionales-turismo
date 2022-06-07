$(document).ready( function() {
	jQuery(".sindecimales").keydown(function(event) {  
	      // Allow: backspace, delete, tab and escape
	    if ( event.which == 46 || event.which == 8 || event.which == 9 || event.which == 27 || 
	         // Allow: Ctrl+A
	        (event.which == 65 && event.ctrlKey === true) || 
	         // Allow: home, end, left, right
	        (event.which >= 35 && event.which <= 39)) {
	             // let it happen, don't do anything
	             return;
	    }
	    else {
	        // Ensure that it is a number and stop the keypress
	        if ( event.shiftKey|| (event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105 ) ) 
	        {
	   			event.preventDefault(); 
	        }
	    }
	});

	jQuery(".condecimales").keydown(function(event) {  
	      // Allow: backspace, delete, tab and escape
	    if ( event.which == 46 || event.which == 8 || event.which == 9 || event.which == 27 || 
	         // Allow: Ctrl+A
	        (event.which == 65 && event.ctrlKey === true) || 
	         // Allow: home, end, left, right
	        (event.which >= 35 && event.which <= 39)) {
	             // let it happen, don't do anything
	             return;
	    }
	    else {
	        var pos=event.target.value.indexOf(',');
	        if(pos<0)
	        	pos=event.target.value.indexOf('.');
		    
	        // Admitimos la coma(188)/punto(190)/punto del teclado numérico(110) sólo si no hay ya un separador decimal.
	    	if(!event.shiftKey && !event.ctrlKey && (event.which == 110 || event.which == 190 || event.which == 188) && pos<0)
	    	{
	    		return;
	    	}
	    	
	        // Ensure that it is a number and stop the keypress
	        if ( event.shiftKey|| (event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105 ) ) 
	        {
	   			event.preventDefault(); 
	        }

	        // En este punto, se va a insertar un número.
	        if(pos>=0)
	        {
	        	// El separador decimal ya está presente
	        	var caretPos = event.target.selectionStart;
	        	if(caretPos>pos)
	        	{
	        		// Vamos a insertar una cifra decimal
	        		var ndecs=$(event.target).data('numdecimales');
	    	        if((ndecs!=null) && (ndecs.length!==0) && (!isNaN(ndecs)))
	    	        {
	    	        	ndecs=parseInt(ndecs);
	    	        	if(ndecs>0)
	    	        	{
	    	        		// Hay límite de cifras decimales
	            	        if((event.target.value.length-pos)>ndecs)
	            	        {
	            	        	// El límite de cifras decimales ya se ha alcanzado. Cancelamos la entrada.
	            	   			event.preventDefault(); 
	            	        }
	    	        	}
	    	        }
	        	}
	        }
	    }
	});
	
	jQuery(".negativo").keydown(function(event) {  
	      // Allow: backspace, delete, tab and escape
	    if ( event.which == 46 || event.which == 8 || event.which == 9 || event.which == 27 || 
	         // Allow: Ctrl+A
	        (event.which == 65 && event.ctrlKey === true) || 
	         // Allow: home, end, left, right
	        (event.which >= 35 && event.which <= 39)) {
	             // let it happen, don't do anything
	             return;
	    }
	    else {
	    	
	    	if(!event.shiftKey && !event.ctrlKey && (event.which == 109 || event.which == 189) && ((event.target.value.indexOf('-')<0 && event.target.selectionStart==0 && event.target.selectionEnd==0) || (event.target.selectionStart==0 && event.target.selectionEnd!=0)))
	    	{
	    		return;
	    	}
	    	
	        // Ensure that it is a number and stop the keypress
	        if ( event.shiftKey|| (event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105 ) ) 
	        {
	   			event.preventDefault(); 
	        }
	    }
	});	
	
	jQuery(".numero").bind('paste', function(event) {
		event.preventDefault();
	});
	
	jQuery(".numero").attr('autocomplete','off');
	
	jQuery(".condecimales").blur(function(event) {
		event.target.value = event.target.value.replace('.',',');
		if(event.target.value.endsWith(','))
			event.target.value = event.target.value.substring(0,event.target.value.length-1);
		else
		{
			var pos=event.target.value.indexOf(',');
			if(pos>=0)
			{
				var ndecs=$(event.target).data('numdecimales');
				if((ndecs!=null) && (ndecs.length!==0) && (!isNaN(ndecs)))
				{
					ndecs=parseInt(ndecs);
					if(ndecs>0)
					{
						if((event.target.value.length-pos)>ndecs)
						{
							event.target.value = event.target.value.substring(0,pos+ndecs+1);
						}
					}
				}
			}
		}
	});
	
    jQuery.validator.addMethod(
            "decimales",
            function(value, element) {
            	return this.optional(element) || value.match(/^[0-9]*([.,]([0-9]+))?$/);
            }, 
            "Por favor, escriba un número decimal válido."
    );

    $('input').keydown(enter2tab);
});

function enter2tab(e) {
    if (e.keyCode == 13) {
        cb = parseInt($(this).attr('tabindex'));

	    if ($(':input[tabindex=\'' + (cb + 1) + '\']') != null) {
	        $(':input[tabindex=\'' + (cb + 1) + '\']').focus();
	        e.preventDefault();
	
	        return false;
	    }
   }
}