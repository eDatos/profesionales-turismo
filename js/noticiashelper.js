	
/**
 * Constructor de un helper para obtener noticas utilizando Google.Feeds.
 * @returns {NoticiasHelper}
 */
function NoticiasHelper()
{
}

/**
 * Inicializa la carga de las noticias de toda la lista de feeds indicados. Cada entrada de esta lista será un objeto
 * con las propiedades url y max, que indican la url de la que obtener los feeds y el númeroe de entradas a leer.
 * 
 * Cuado se terminan de cargar todos las entradas desde las distintas fuentes, se llama a la función endLoadingCallback, que
 * acepta un parámetro con la lista de entradas leídas.
 */
NoticiasHelper.prototype.load = function(listaFeeds, endloadingCallback)
{
	this.feedList = listaFeeds;
	this.entradas = [];
	this.num_loads = listaFeeds.length;
	this.endloadingCallback = endloadingCallback;
	
	var _this = this;
	
	google.load("feeds", "1", {callback : function() {
			_this.initialize();
		}
	});
};
	
NoticiasHelper.prototype.initialize = function () 
{
	var _this = this;
	for ( var i = 0; i < this.feedList.length; i++) 
	{
		var feed = new google.feeds.Feed(this.feedList[i].url);
		feed.setNumEntries(this.feedList[i].max);
		feed.load(function (feedIndex) {
			return function(result) {
					if (!result.error)
					{
						/// Las entradas con hasPriority a true tienen prioridad con respecto al del resto.
						_this.addEntries(result.feed.entries, (_this.feedList[feedIndex].hasPriority) ? 0 : 1);
					}
					_this.num_loads--;
					if (_this.num_loads <= 0)
					{
						/// Ya hemos procesado todas las entradas, procedemos a mostrarlas.
						_this.endLoading(_this.entradas);		
					}
				};
			}(i));
	}
};
	
NoticiasHelper.prototype.addEntries = function (entries, prioridad)
{
	for (var i = 0; i < entries.length; i++)
	{
		var entry = entries[i];
		this.entradas.push( {
			entry: entry,
			priority : prioridad
		});
	}		
};

NoticiasHelper.prototype.endLoading = function (entradas)
{
	entradas.sort(function( a,b ){
		
		if (a.priority != b.priority)
			return a.priority - b.priority;
		
		return new Date(a.entry.publishedDate) < new Date(b.entry.publishedDate); 
	});
	
	this.endloadingCallback(this.entradas);
	delete this.entradas;
	delete this.num_loads;
	delete this.feedList;
};

function ucFirst(string)
{
	var s = string.toLocaleLowerCase();
    return s.charAt(0).toUpperCase() + s.slice(1);
}

function formatDate(pubDate)
{
	var meses = ["ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC"];
	return pubDate.getDate() + " " + ucFirst(meses[ pubDate.getMonth() ]);
}