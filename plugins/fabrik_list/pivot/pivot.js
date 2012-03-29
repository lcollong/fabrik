var FbListPivot = new Class({
	
	Extends: FbListPlugin,
	
	initialize: function (options) {
		this.parent(options);
	},
	
	buttonAction: function () {
		var url = this.options.liveSite + "index.php?option=com_fabrik&controller=list.pivot&task=popupwin&tmpl=component&iframe=1&id=" + this.listid + "&renderOrder=" + this.options.renderOrder;
		this.listform.getElements('input[name^=ids]').each(function (id) {
			if (id.getValue() !== false && id.checked !== false) {
				url += "&ids[]=" + id.getValue();
			}
		});
		var id = 'pivot-list-plugin';
		this.windowopts = {
			'id': id,
			title: this.options.title,
			contentType: 'xhr',
			loadMethod: 'xhr',
			contentURL: url,
			width: 520,
			height: 420,
			evalScripts: true,
			y: 100,
			'minimizable': false,
			'collapsible': true,
			onContentLoaded: function () {
				var myfx = new Fx.Scroll(window).toElement(id);

			}.bind(this)
		};
		Fabrik.getWindow(this.windowopts);
	}
	
});