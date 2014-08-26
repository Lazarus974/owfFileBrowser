<p class="text-muted">
	%{$file}%
</p>
<div class="well" style="word-wrap: break-word;">
	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label for="size" class="col-sm-2 control-label">%{@ "Size"}%</label>
			<div class="col-sm-10">
				%{$size}%
			</div>
		</div>
		<div class="form-group">
			<label for="size" class="col-sm-2 control-label">%{@ "Download estimated time"}%</label>
			<div class="col-sm-10">
				%{$download_time}%
			</div>
		</div>
		<div class="form-group">
			<label for="size" class="col-sm-2 control-label">%{@ "Type"}%</label>
			<div class="col-sm-10">
				%{$mime}%
			</div>
		</div>
		%{if $content}%
			<hr style="border-color: lavender;" />
			%{foreach($content as $k => $c)}%
				<div class="form-group">
					<label for="%{$k}%" class="col-sm-2 control-label">%{$c['name']}%</label>
					<div class="col-sm-10 text-left">
						%{if isset($c['html']) && $c['html']}%
							%{$c['value']}%
						%{else}%
							%{$c['value']|entities}%
						%{/if}%
					</div>
				</div>
			%{/foreach}%
		%{/if}%
	</form>
</div>
<a href="%{link '/files/download'}%?directory=%{$directory['id']}%&file=%{$filename}%" class="btn btn-primary files-download%{if !$downloadable}% disabled%{/if}%"
	data-url="%{link '/files/download'}%" data-page="%{$directory['id']}%" data-file="%{$filename}%" target="_blank">
	<span class="glyphicon glyphicon-download-alt"></span>&nbsp;%{@ "Download"}%
</a>
%{if !$downloadable}%
	<span style="color: red;cursor: pointer;" class="glyphicon glyphicon-exclamation-sign files-download-error" data-toggle="tooltip" data-placement="top" title="%{@ 'This download would take more than 20 minutes, sorry'}%"></span>
%{/if}%
