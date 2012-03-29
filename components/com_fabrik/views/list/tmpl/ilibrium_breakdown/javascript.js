/**
 * @author Robert
 */
head.ready(function() {
	
	var sceneShuffle = new FbAutocomplete('scene_number_limitstart',
	 {
		'url': 'index.php?option=com_fabrik&task=plugin.userAjax&format=raw&method=getSceneNumbers'
	 }
	);
	sceneShuffle.addEvent('selection', function(autoCompleter, v) {
		document.getElement('input[name=limitstart22]').value = v;
		document.getElement('form[name=fabrikList]').submit();
	});
})
