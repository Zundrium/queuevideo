$(document).ready(function() {
	var theme = $.cookie("theme");
	if(typeof theme !== "undefined") {
		loadTheme(theme, true);
	}
  $("#themes").find("a").click(function(event){
    event.preventDefault();
    loadTheme($(this).text().toLowerCase());
  });
});

function loadTheme(themeName, noAnimation) {
	if(typeof noAnimation === "undefined") {
		noAnimation = false;
	}
	if(!noAnimation) {
		$("body").animate({opacity:0}, {complete: function(){
	      $("#themeUrl").attr("href", "/css/" + themeName + "/bootstrap.min.css");
	      setTimeout(function() {
	      	$("body").animate({opacity:1});
	      }, 1000);
	    }});
	} else {
		$("#themeUrl").attr("href", "/css/" + themeName + "/bootstrap.min.css");	
	}
	$.cookie("theme", themeName);
}

function setHeights() {
	var menuHeight = $('.navbar-default').height();
	var formHeight = $("#sendMessage").height();
	var middleHeight = $(window).height() - menuHeight;
	var padding = 90;
	if($('.col-md-8 #player').length) {
		padding = 560;
	}
	$('#userList,#chatBox').css({
		height: middleHeight - padding
	});
	$('#fixedContainer').css({
		width: $('#fixedContainer').parent().width()
	});
}