<% if $EmbeddedItem %>
	$EmbeddedItem.EmbedHTML.RAW
<% else_if $Widget.EmbeddedItem %>
	$Widget.EmbeddedItem.EmbedHTML.RAW
<% end_if %>