<ol class="breadcrumb">
	<li class="active">
		<a href="#" class="files-directory files-directory-up" data-level=1 data-node="/">
			%{$directory["name"]}%
		</a>
	</li>
</ol>

<div class="col-xs-3">
	<ul class="list-group" data-page="%{$directory['id']}%" data-url="%{link '/json/owfFileBrowser_scanner/json_info'}%">
		%{$body}%
	</ul>
</div>
<div class="col-xs-9 text-center">
	<div class="files-display">
		%{@ "Select a file"}%
	</div>
	<div class="files-display files-display-fixed" style="position: fixed;width: 45%;margin-top: -125px;">
		%{@ "Select a file"}%
	</div>
</center>

<script type="text/javascript">
	$(".files-node").not("[data-level=1]").hide();
	$(".files-display-fixed").hide();
	$(document).ready(function() {
		Buttons();
	});
</script>
