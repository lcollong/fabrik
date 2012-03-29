/**
 * @author Robert
 */
head.ready(function() {
	
	var sceneShuffle = new FbAutocomplete('vfx_filter',
	 {
		'url': 'index.php?option=com_fabrik&task=plugin.userAjax&format=raw&method=getVfxIds',
		autoLoadSingleResult: false
	 }
	);
	sceneShuffle.addEvent('selection', function(autoCompleter, v) {
		Fabrik.loader.start('form_94');
		document.location = document.location + '?rowid=' + v;
	});
})
