if ( !Array.prototype.forEach ) {
  Array.prototype.forEach = function(fn, scope) {
    for(var i = 0, len = this.length; i < len; ++i) {
      fn.call(scope, this[i], i, this);
    }
  }
}

var ba = {
		currentFoto: 0,
		fotos: [],
		sels: [],
		container: null,
		banners : [],
		fdspeed : "slow",
		wtspeed : 3000,
		inId : null,
		init : function(banners, container, fds, wts) {
			var display="block";
			this.container = container;
			this.fdspeed = fds;
			this.wtspeed = wts;
			banners.forEach(function(element, index) {
				var db = $("<div style='position:absolute;'></div>")
							.append($("<img></img>").attr({ "src" : element.img }))
							.attr({ "id": "foto" + index } )
							.css("display", display)
							.data("link", element.link)
							.click(ba.banner_click);
				ba.container.append(db);
				ba.fotos.push("#foto" + index);
				var bs = $("<span></span>").attr("id", "sel" + index).addClass("selban").data("index", index).click(ba.sel_banner_click);
				$("#banner_sels").prepend(bs);
				ba.sels.push("#sel" + index);
				display="none";
			});			

			$(ba.sels[ba.currentFoto]).addClass("active");
			ba.inId = setInterval(ba.changeBanner, ba.wtspeed);
		},
		changeBanner : function() {

			$(ba.sels[ba.currentFoto]).removeClass("active");
			$(ba.fotos[ba.currentFoto]).fadeOut(ba.fdspeed); 
			$(ba.fotos[(ba.currentFoto + 1) % ba.fotos.length]).fadeIn(ba.fdspeed);
			ba.currentFoto = (ba.currentFoto + 1) % ba.fotos.length;
			$(ba.sels[ba.currentFoto]).addClass("active");		
		},
		stop : function() {
			if (ba.inId)
				clearInterval(ba.inId);
			ba.inId = null;
		},
		banner_click : function(event) {
			ba.stop();
			window.open($(this).data('link'));
		},
		sel_banner_click: function(event) {
			ba.stop();
			var ti = parseInt($(this).data('index'));
			$(ba.sels[ba.currentFoto]).removeClass("active");
			$(ba.fotos[ba.currentFoto]).fadeOut("fast"); 
			ba.currentFoto = ti;
			$(ba.fotos[ti]).fadeIn("fast");
			$(ba.sels[ti]).addClass("active");
			event.preventDefault();
		}
	};