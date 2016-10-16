(function($){

	$.fn.waapplugindisign = function(options){
		
		var settings = $.extend({
				plugin_id:'set',
				csrf:'',
				theme_block_name:'#select-frontend-disign-theme',
				dialog_block_name:'#frontend-disign-dialog',
				onSubmit : function(){}
			}, options),
			a = $('a',this),
			th = this;
		
		a.click(function(){
			var self = $(this),
				title = self.text(),
				name = self.data('name'),
				theme = $(settings.theme_block_name).val(),
				theme_input = $('<input />').attr('type','hidden').attr('name','theme').val(theme),
				div = $('<div />').hide();
			th.append( div.append($('<textarea />').attr('id','dialog-textarea').hide()).append(settings.csrf).append(theme_input) );
			div.waDialog({
				title: (theme == '_') ? title+' для всех тем' : title+' для темы '+theme,
				buttons: '<input type="submit" value="Сохранить" class="button green" /> или <a href="#" class="cancel">отмена</a>',
				onSubmit: function (d) {
					var f = $('form',d);
					$(':submit',d).after('<i class="icon16 loading"></i>');
					setTimeout(function(){
						$.post('?plugin='+settings.plugin_id+'&module=saveFile',f.serializeArray(),function(){
							d.find('.loading').remove();
							settings.onSubmit();
							$(':submit',d).removeAttr('disabled');
						});
					},1000);
					return false;
				},
				onLoad:function(){
					$.post('?plugin='+settings.plugin_id+'&module=getFileContent',{ name:name,theme:theme },function(response){
						$('textarea',div).attr('name',name).val(response.data).each(function () {
							CodeMirror.fromTextArea(this, {
								lineNumbers: true,
								mode: self.data('mode'),
								tabMode: "indent",
								height: "dynamic",
								lineWrapping: true
							});
						});
					},'json')
				},
				disableButtonsOnSubmit: true
			});
			return false;
		})
		
		return this;
	}
})(jQuery)