<!DOCTYPE html>

<html lang="%{$lang}%">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="description" content="" />
		<meta name="author" content="Olivier Leclercq" />
		
		<title>%{$title}%</title>
		
		<link href="%{link '/data/bootstrap/css/bootstrap.min.css'}%" rel="stylesheet" />
		<link href="%{link '/data/bootstrap/css/bootstrap-theme.min.css'}%" rel="stylesheet" />
		<link href="%{link '/data/owfFileBrowser/css/owfFileBrowser.css'}%" rel="stylesheet" />
		
		<script type="text/javascript" src="%{link '/data/js/jquery-1.11.0.min.js'}%"></script>
		<script type="text/javascript" src="%{link '/data/bootstrap/js/bootstrap.min.js'}%"></script>
		<script type="text/javascript" src="%{link '/data/bootstrap/js/bootstrap-growl.min.js'}%"></script>
		<script type="text/javascript" src="%{link '/data/owfFileBrowser/js/owfFileBrowser.js'}%"></script>
		<script type="text/javascript">
			root = "%{link '/'}%";
			//dateFormat = "%{*$dateFormat}%";
			lang = "%{$lang}%";
		</script>
	</head>
	
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="%{link '/'}%">
						<span class="glyphicon glyphicon-home"></span>
						%{@ "Home"}%
					</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						%{foreach($directories as $directory)}%
							<li class="%{if $directory['active']}% active%{/if}%">
								<a href="%{link '/files/'}%%{$directory['id']}%" class="files-menu">
									%{$directory['name']}%
								</a>
							</li>
						%{/foreach}%
					</ul>
					
					<ul class="nav navbar-nav navbar-right">
						<li class="minitopnavbar dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<span class="glyphicon glyphicon-user"></span>&nbsp;%{$me['firstname']}% %{$me['name']}%&nbsp;<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="%{link '/admin/options'}%?back=%{$here}%"><span class="glyphicon glyphicon-user"></span>&nbsp;%{@ "Profil"}%</a></li>
								<li class="divider"></li>
								%{if (isset($perms['session:god']) && is_array($perms['session:god'])) || (isset($perms['session:admin']) && is_array($perms['session:admin']))}%
									<li><a href="%{link '/admin'}%"><span class="glyphicon glyphicon-cog"></span>&nbsp;%{@ "Administration"}%</a></li>
								%{/if}%
								<li><a href="%{link '/session/logout'}%"><span class="glyphicon glyphicon-off"></span>&nbsp;%{@ "Logout"}%</a></li>
							</ul>
						</li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
		
		<div id="body" class="container">
			<center class="files-loading">
				<p class="text-muted">
					%{@ "Scanning directory, please wait."}%
				</p>
				<img src="%{link '/data/owfFileBrowser/images/loader.gif'}%" alt="%{@ 'Loading ...'}%" />
			</center>
			
			<div class="panel panel-info">
				<div class="panel-heading">
					%{@ "File browser"}%
				</div>
				<div class="panel-body body-content">
				</div>
			</div>
		</div>
		<script>$(document).ready(function() {LoadContent(%{$body}%);});</script>
		
<!--
		<p class="bandwidth-warning"%{*if $bandwidth}% style="display: none;"%{*/if}%>
			%{@ "Your bandwidth has not been checked yet"}%
			⋅
			<span style="color: yellow;cursor: pointer;" class="glyphicon glyphicon-question-sign"
				data-toggle="tooltip" data-placement="top" title="%{@ 'You cannot download anything without a bandwidth check'}%">
				</span>
			⋅
			<button class="btn btn-xs btn-default files-bandwidth-check" data-url="%{link '/bandwidth/checker'}%" data-alt="<span class='glyphicon glyphicon-sort-by-attributes'></span>&nbsp;%{@ 'Updating ...'}%"
				data-toggle="tooltip" data-placement="top" title="%{@ 'This lasts ~10s and entirelly freeze your connection'}%">
				<span class="glyphicon glyphicon-play"></span>&nbsp;%{@ "Check it now"}%
			</button>
		</p>
		
		<p class="bandwidth-update"%{*if !$bandwidth}% style="display: none;"%{*/if}%>
			<button class="btn btn-xs btn-default files-bandwidth-check" data-url="%{link '/bandwidth/checker'}%" data-alt="<span class='glyphicon glyphicon-sort-by-attributes'></span>&nbsp;%{@ 'Updating ...'}%"
				data-toggle="tooltip" data-placement="top" title="%{@ 'This lasts ~10s and entirelly freeze your connection'}%">
				<span class="glyphicon glyphicon-play"></span>&nbsp;%{@ "Update bandwidth"}%
			</button>
		</p>
-->
	</body>
</html>
