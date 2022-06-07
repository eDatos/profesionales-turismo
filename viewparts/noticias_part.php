<?php if (isset($noticias)) : ?>
<script src="https://rss2json.com/gfapi.js"></script>
<script src="js/noticiashelper.js"></script>
<!-- COMIENZO MENU BORDES CUADRADOS -->
<div class="cuadro" style="background-color:rgb(246, 245, 255);">
    <h3 class="titulo_3" style="margin-bottom:4px;">Últimas noticias</h3>
    <div class="subrayado"></div>
    <div id="feed">No hay ninguna noticia disponible.</div>
</div>
<script>
<?php if (isset($noticias)): ?>
	var feeds = <?= $noticias; ?>;
<?php else: ?>
	var feeds = '';
<?php endif; ?>

function chkDate(d)
{
	if(Object.prototype.toString.call(d)!=="[object Date]")
		return false;
	if(isNaN(d.getTime()))
		return false;
	return true;
}

function getDate(di)
{
	// Necesario para el IE porque no tolera el espacio entre fecha y hora en el formato ISO
	d=new Date(di);
	if(chkDate(d)==false)
	{
		di=di.replace(/\s/g,'T');
		d=new Date(di);
	}
	return d;
}

new NoticiasHelper().load(feeds, function (entradas)
{
	var newContent = $("<div></div>");
	
	for (var i = 0; i < entradas.length; i++)
	{
		var entry = entradas[i].entry;
		if (!entry)
			continue;
		var pubDate = getDate(entry.publishedDate);
		var ellink = "";
		if (entry.link)
			ellink = $("<a class='enlace' target='_blank'></a>").html(entry.title).attr('href', entry.link).attr('title', entry.title);
		else
			ellink = $("<span></span>").html(entry.title).attr('href', entry.link).attr('title', entry.title);
		var eldate = $("<div style='margin-top:10px;font-size:0.9em;'></div>").html("<strong>" + formatDate(pubDate) + " </strong>").append(ellink);
		
		newContent.append(eldate);
	}
	$('#feed').html(newContent.html());		
});
</script>
<!-- FIN MENU BORDES CUADRADOS -->
<?php endif; ?>	