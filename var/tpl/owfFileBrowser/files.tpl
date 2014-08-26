
<li class="files-node" data-level="%{$tree['level']}%" data-parent="%{$tree['parent']}%">
	%{if $tree['type'] == 'f'}%
	<a href="#" class="files-info" data-file="%{$tree['parent']}%%{$tree['name']}%">
		<span class="glyphicon glyphicon-file"></span>
	%{elseif $tree['type'] == 'd'}%
	<a href="#" class="files-directory files-directory-down" data-node="%{$tree['parent']}%%{$tree['name']}%/" data-level=%{$tree['level']+1}%>
		<span class="glyphicon glyphicon-folder-close"></span>
	%{/if}%
		&nbsp;%{$tree['name']}%
	</a>
</li>

%{if $tree['type'] == 'd'}%
	<li class="files-node" data-level="%{$tree['level']+1}%" data-parent="%{$tree['parent']}%%{$tree['name']}%/">
		<a href="#"  class="files-directory files-directory-up" data-node="%{$tree['parent']}%" data-level=%{$tree['level']}%>
			<span class="glyphicon glyphicon-circle-arrow-up"></span>
			&nbsp;..
		</a>
	</li>
%{/if}%
