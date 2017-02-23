(function($){

	$.shop.sodPlugin = {
		options: [],
		plugin_id: 'sod',
		design_data: '',
		progress_count: 0,
		init: function(options){
			var self = this,
				form = $('#plugins-settings-form');
			
			/* custom init */
			function checkHours(min,max){
				min = parseInt(min);
				max = parseInt(max);
				console.log(min+'-'+max);
				min = (isNaN(min))?9:min;
				max = (isNaN(max))?18:max;
				
				max = (max<1)?1:max;
				max = (max>24)?24:max;
				
				min = (min<0)?0:min;
				min = (min>23)?23:min;
				min = (min>=max)?max-1:min;
				
				return [min,max];
			}
			
			
			function initChange(a,b){
				$(a+', '+b).change(function(){
					var min = $(a).val(),
						max = $(b).val();
						m = checkHours(min,max);
					console.log(m);
					$(a).val(m[0]);
					$(b).val(m[1]);
				})
			}
			
			initChange('#datetimepicker_work_min_time','#datetimepicker_work_max_time');
			initChange('#datetimepicker_weekend_min_time','#datetimepicker_weekend_max_time');
			
			
			$('span.state').click(function(){
				var s = $(this),
					id = s.data('id');
				if ( s.is('.checked-state') ){
					$('input',s).remove();
					s.removeClass('checked-state');
				}else{
					s.prepend('<input type="hidden" name="shop_sod[disable_states][]" value="'+id+'">').addClass('checked-state');
				}
			})
			
			/* general init */
			$('[type="checkbox"]').iButton({
				labelOn: 'вкл',
				labelOff: 'выкл',
				className: 'mini',
				change: function(){
					self.editSubmitBtn();
				}
			});
			form.on('change','input[type="text"], select',function(){
				self.editSubmitBtn();
			})
			
			this.initDescription();
			this.initDesign();

			$($.plugins).on('success',function(e,r){
				if ( typeof r.data.errors != 'undefined' ){
					$('#plugins-settings-form-status').hide();
					setTimeout(function(){
						$('#plugin-submit-btn').removeClass('green').addClass('yellow');
					},100);
					$('#pp-errors').text(r.data.errors.join(' '));
				}else
					$('#pp-errors').text('');
				self.successSubmitBtn();
			});
		},
		
		/* custom methods */
		
		/* general methods */
		initDescription: function(){
			var b = $('#desc-block'),
				show = '<i class="icon10 darr"></i>Показать описание плагина',
				hide = '<i class="icon10 darr"></i>Скрыть описание плагина',
				desc = $('<div />').addClass('desc').html(b.html()).hide(),
				btn = $('<a />').addClass('inline-link desc-btn').html(show);
			
			btn.click(function(){
				var self = $(this);
				desc.toggle();
				if ( desc.is(':visible') ){
					self.html(hide);
					$('i',self).removeClass('darr').addClass('uarr');
				}else{
					self.html(show);
					$('i',self).removeClass('uarr').addClass('darr');
				}
				return false;
			})
			b.html('').append(desc).append(btn);
		},
		initDesign: function(){
			var self = this,
				b = $('.block-frontend-design');
			$('a',b).click(function(){
				var title = $(this).text(),
					name = $(this).data('name'),
					mode = $(this).data('mode'),
					theme = $('#select-frontend-design-theme').val(),
					div = $('<div />').hide();
				self.design_data = {name:name,theme:theme};
				b.append( div.attr('id','dialog-plugin-design') );
				title = (theme == '_') ? 'Редактирование "'+title+'" для всех тем' : 'Редактирование "'+title+'" для темы "'+theme+'"';
				title = (theme == '_default_') ? 'Просмотр содержимого исходного файла' : title;
				div.waDialog({
					title: title,
					buttons: ((theme == '_default_') ? '' : '<input type="submit" value="Сохранить" class="button green" /> <em>Ctrl+S</em> или ')+'<a href="#" class="cancel">закрыть без сохранения</a>',
					onSubmit: function (d) {
						self.saveChanges();
						return false;
					},
					onLoad:function(){
						$.post('?plugin='+self.plugin_id+'&module=getFileContent',{ name:name,theme:theme },function(response){
							$('#dialog-plugin-design .dialog-content-indent').append('<div id="plugin-block-editor"></div>');
							
							var editor = ace.edit('plugin-block-editor');
							ace.config.set("basePath", wa_url + 'wa-content/js/ace/');
							editor.setTheme("ace/theme/eclipse");
							var session = editor.getSession();
							session.setMode("ace/mode/"+mode);
							session.setUseWrapMode(true);
							editor.setOption("maxLines", 10000);
							editor.setAutoScrollEditorIntoView(true);
							editor.renderer.setShowGutter(false);
							editor.setShowPrintMargin(false);
							
							if (navigator.appVersion.indexOf('Mac') != -1)
								editor.setFontSize(13);
							else if (navigator.appVersion.indexOf('Linux') != -1)
								editor.setFontSize(16);
							else
								editor.setFontSize(14);
							
							$('.ace_editor').css('fontFamily', '');
							
							editor.insert(response.data);
							self.design_data[name] = response.data;
							//$('#plugin-design-textarea').html(response.data);
							
							editor.focus();
							editor.navigateTo(0, 0);
							
							editor.commands.addCommands([{
								name: 'plugindesignSave',
								bindKey: {win: 'Ctrl-S',  mac: 'Ctrl-S'},
								exec: function(){self.saveChanges()}
							}]);
							
							session.on('change', function() {
								self.design_data[name] = editor.getValue();
								var btn = $('#dialog-plugin-design :submit');
								
								if ( btn.hasClass('green') )
									btn.removeClass('green').addClass('yellow');
							});
						},'json')
					},
					onClose: function(){
						$('div.dialog').remove();
					}
				});
				return false;
			})
		},
		saveChanges: function(){
			var wr = $('#dialog-plugin-design'),
				btn = $(':submit',wr);
				
			btn.after('<i class="icon16 loading"></i>');
			$.post('?plugin='+$('#plugin-submit-btn').data('plugin-id')+'&module=saveFile',this.design_data,function(){
				$('.loading',wr).remove();
				if ( btn.hasClass('yellow') )
					btn.removeClass('yellow').addClass('green');
			});
		},
		successSubmitBtn: function(){
			var btn = $('#plugin-submit-btn');
			if ( btn.hasClass('yellow') )
				btn.removeClass('yellow').addClass('green');
			//$.plugins.dispatch(window.location.hash,true);
		},
		editSubmitBtn: function(){
			var btn = $('#plugin-submit-btn');
			if ( btn.hasClass('green') )
				btn.removeClass('green').addClass('yellow');
		},
	}

})(jQuery)