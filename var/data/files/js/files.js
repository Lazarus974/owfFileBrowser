
function BandwidthCheck(event) {
	event.preventDefault();
	var el = $(this),
		url = el.data('url');
	el.toggleClass('disabled');
	el.Alt();
	
	$.post(url, {}, function(data) {
		$.post(url, {action: "get"}, function(data) {
			el.toggleClass('disabled');
			el.Alt();
			if(data.status) {
				$.growl('Bandwidth properly updated : '+data.message, {type: "success"});
				$(".bandwidth-warning").hide();
				$(".bandwidth-update").show();
			}
			else {
				$.growl('Error', {type: "danger"});
				$(".bandwidth-update").hide();
				$(".bandwidth-warning").show();
			}
		}, "json");
	});
}

function LatencyCheck(url) {
	start = new Date();
	$.post(url, {start: start.getTime()}, function(data) {
		end = new Date();
		var lag = end - start;
		$.post(url, {lag: lag}, function(data) {
			if(data.status) {
				$.growl(data.message, {type: "success"});
				$(".latency-warning").remove();
			}
			else
				$.growl(data.message, {type: "danger"});
		}, "json");
	}, "json");
}

var query = false;

$.fn.Alt = function() {
	var alt = $(this).data('alt');
	if(!alt)
		return false;
	$(this).data('alt', $(this).html());
	$(this).html(alt);
}
$.fn.GetPage = function() {
	return $(this).closest('ul').data('page');
}
$.fn.GetURL = function() {
	return $(this).closest('ul').data('url');
}

function LoadContent(data) {
	$(".body-content").html(data);
	$("#body").removeClass('loading');
	query = false;
}

function OnDownload(event) {
	//event.preventDefault();
	//if(query)
		//return false;
	
	//var	url = $(this).data('url'),
		//page = $(this).data('page'),
		//file = $(this).data('file');
	
	//query = true;
	//$.post(url, {page: page, file: file, json: true},
		//function(data) {
			//query = false;
		//}
	//).fail(function(jqxhr, status, message) {
		//$.growl(message, {type: "danger"});
		//query = false;
	//});
}

function OnFileInfo(event) {
	event.preventDefault();
	if(query)
		return false;
	
	query = true;
	$.post(
		$(this).GetURL(),
		{	directory: $(this).GetPage(),
			file: $(this).data('file')
		},
		function(data) {
			$(".files-display").html(data.result);
			Buttons();
			$(".files-download-error[data-toggle='tooltip']").tooltip();
			query = false;
		}, "json"
	).fail(function(jqxhr, status, message) {
		window.location.reload();
	});
}

function OnDirectoryChange(event) {
	event.preventDefault();
	
	if($(this).closest('li').hasClass('active'))
		return false;
	
	var	node = $(this).data('node'),
		level = $(this).data('level');
	$(".files-node").not("[data-parent='"+node+"']").hide();
	$(".files-node[data-parent='"+node+"']").show();
	
	if($(this).hasClass("files-directory-down")) {
		var rail = node.split('/');
		rail.pop();
		$(".breadcrumb").children("li").removeClass("active");
		$(".breadcrumb").append("<li class='active'><a href='#' class='files-directory files-directory-up' data-node='"+node+"' data-level="+level+">" + rail.pop() + "</a></li>");
		Buttons();
	}
	else if($(this).hasClass("files-directory-up")) {
		$.each($(".breadcrumb li a"), function(k, v) {
			if($(v).data('level') > level)
				$(v).closest('li').remove();
		});
		$(".breadcrumb li:last").addClass('active');
	}
}

function Buttons() {
	$(".files-info:not(.files-init)").addClass('files-init').click(OnFileInfo);
	$(".files-directory:not(.files-init)").addClass('files-init').click(OnDirectoryChange);
	$(".files-download:not(.files-init)").addClass('files-init').click(OnDownload);
	$(".files-bandwidth-check:not(.files-init)").addClass('files-init').click(BandwidthCheck);
}
	
$(document).ready(function() {
	
	$("[data-toggle='tooltip']").tooltip();
	
	$(".files-menu").click(function(event) {
		
		if(event.which == 2)
			return true;
		
		event.preventDefault();
		
		if(query || $(this).closest("li").hasClass('active'))
			return false;
		
		$(this).closest(".nav").children('li').removeClass('active');
		$(this).closest("li").addClass('active');
		
		$("#body").addClass('loading');
		query = true;
		$.post($(this).attr('href'), { }, LoadContent, "json")
			.fail(function(jqxhr, status, message) {
				window.location.reload();
			});
	});
	
	$(document).on('scroll', function(event) {
		
		var	elementStatic = $(".files-display:not('.files-display-fixed')"),
			elementFixed = $(".files-display.files-display-fixed"),
			fixed = $(window).scrollTop() > 125,
			isFixed = elementFixed.is(':visible');
			//changed = false;
		
		if(fixed && !isFixed) {
			var w = elementStatic.width();
			elementStatic.hide();
			elementFixed.show();
			elementFixed.css('width', w);
			//changed = true;
		}
		else if (!fixed && isFixed) {
			elementFixed.hide();
			elementStatic.show();
			//changed = true;
		}
		
		//if(changed) {
			//el.toggleClass('col-xs-offset-3');
			//el.toggleClass('col-xs-9');
			//el.toggleClass('col-xs-7');
			//el.toggleClass('files-relative-fixed');
		//}
	});
	
	$(window).resize(function(event) {
		var	elementStatic = $(".files-display:not('.files-display-fixed')"),
			elementFixed = $(".files-display.files-display-fixed");
		
		if(elementFixed.is(':visible')) {
			elementFixed.hide();
			elementStatic.show();
			var w = elementStatic.width();
			elementStatic.hide();
			elementFixed.show();
			elementFixed.css('width', w);
		}
	});
	
});
