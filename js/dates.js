
function initDatepicker(ctl, ctldateformat)
{
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	ctl.datepicker( { 
		dateFormat: ctldateformat, 
		changeMonth: true, 
		changeYear: true, 
		maxDate: "+0D",
		onClose: function() {
			ctl.valid();
	    } 
	} );
}

function parseDate(value) 
{
    var adata = value.split('/');
    var dd = parseInt(adata[0], 10); // was mm (mese / month)
    var mm = parseInt(adata[1], 10); // was gg (giorno / day)
    var yyyy = parseInt(adata[2], 10); // was aaaa (anno / year)
    var xdata = new Date(yyyy, mm - 1, dd);
    return xdata;
}

function validDate(dia,mes,agno)
{
	value = dia + "/" + mes + "/" + agno;
    var check = false;
    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    if( re.test(value)){
        var adata = value.split('/');
        var dd = parseInt(adata[0],10); // was mm (mese / month)
        var mm = parseInt(adata[1],10); // was gg (giorno / day)
        var yyyy = parseInt(adata[2],10); // was aaaa (anno / year)
        var xdata = new Date(yyyy,mm-1,dd);
        if ( ( xdata.getFullYear() == yyyy ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == dd ) )
            check = true;
        else
            check = false;
    } else
        check = false;
    return check;
}

$(document).ready(function () {
	
	jQuery.validator.addMethod(
            "mayorque",
            function (value, element, param) {
                var target = $(param);
                return parseDate(value) > parseDate($(param).val());
            },
            "La fecha final debe ser mayor que la fecha de inicio"
        );

    jQuery.validator.addMethod(
            "rangomeses",
            function (value, element, param) {
                var rango = param;
                return $.inArray(parseDate(value).getMonth(),  rango) != -1;
            },
            "El mes de la fecha no está entre los meses permitidos"
        );
    
    jQuery.validator.addMethod(
            "dateUS",
            function (value, element) {
                var check = false;
                var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                if (re.test(value)) {
                    var adata = value.split('/');
                    var dd = parseInt(adata[0], 10); // was mm (mese / month)
                    var mm = parseInt(adata[1], 10); // was gg (giorno / day)
                    var yyyy = parseInt(adata[2], 10); // was aaaa (anno / year)
                    var xdata = new Date(yyyy, mm - 1, dd);
                    if ((xdata.getFullYear() == yyyy) && (xdata.getMonth() == mm - 1) && (xdata.getDate() == dd))
                        check = true;
                    else
                        check = false;
                } else
                    check = false;
                return this.optional(element) || check;
            },
            "La fecha no es válida"
        );

});