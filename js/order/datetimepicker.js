$.order_datetimepicker = {
	
	dtp : {},
	dtps : {},
	dtpe : {},
	dtpli : {},
	max_date : '',
	min_date : '',
	year_start : '',
	year_end : '',
	work_min_time : '',
	work_max_time : '',
	weekend : 0,
	errors : ['В выходные нет доставки','Укажите корректную дату','Ошибка сохранения'],
	form : $('#sod-backend-order-form'),
	
	init : function(options){
		
		this.dtp = options.dtp || {};
		this.dtps = options.dtps || {};
		this.dtpe = options.dtpe || {};
		this.min_date = options.min_date || '';
		this.max_date = options.max_date || '';
		this.year_start = options.year_start || '';
		this.year_end = options.year_end || '';
		this.work_min_time = options.work_min_time || '';
		this.work_max_time = options.work_max_time || '';
		this.weekend_min_time = options.weekend_min_time || '';
		this.weekend_max_time = options.weekend_max_time || '';
		this.weekend = options.weekend || 0;
		
		this.dtp.closest('li').addClass('datetimepicker-li');
		this.dtpli = $('.datetimepicker-li');
		
		this.initDtp();
		this.initDtps();
		this.initDtpe();
		
		var self = this;
		$('input[type="checkbox"]',this.form).change(function(){
			self.saveDate();
		});
	},
	
	initDtp : function(){
		var self = this,
			options = {
				lang : 'ru',
				mask : '39.19.2999',
				format : 'd.m.Y',
				timepicker : false,
				minDate : this.min_date,
				maxDate : this.max_date,
				yearStart : this.year_start,
				yearEnd : this.year_end,
				dayOfWeekStart:1,
				onSelectDate : function(){
					self.saveDate();
				},
				onChangeDateTime : function( ct ){
					self.initDtps();
					self.initDtpe();
				}
			};
		if ( !this.weekend )
			options.onGenerate = function( ct ){
				$(this).find('.xdsoft_date.xdsoft_weekend')
				.addClass('xdsoft_disabled');
			}
		this.dtp.datetimepicker(options);
	},
	
	initDtps:function(){
		var self = this,
			options = this.getDtpOptions();
		options.onShow = function( ct ){
			var max = parseInt(self.dtpe.val()),
				t = ( self.isWeekend() ) ? parseInt(self.weekend_max_time) : parseInt(self.work_max_time);
			max = ( max > t ) ? t : max;
			this.setOptions({
				maxTime : max+'',
				formatTime : 'H'
			});
		};
		this.dtps.datetimepicker(options);
	},
	
	initDtpe : function(options){
		var self = this,
			options = this.getDtpOptions();
		options.onShow = function( ct ){
			var min = parseInt(self.dtps.val())+1,
				t = ( self.isWeekend() ) ? parseInt(self.weekend_min_time) : parseInt(self.work_min_time);
			min = ( min < t ) ? t : min;
			this.setOptions({
				minTime : min+'',
				formatTime : 'H'
			});
		};
		this.dtpe.datetimepicker(options)
	},
	
	getDtpOptions : function(){
		var self = this,
			options = {
				datepicker : false,
				format : 'H',
				formatTime : 'H',
				onSelectTime : function(){
					self.saveDate();
				}
			};
		if ( !this.isWeekend() ){
			options.minTime = this.work_min_time;
			options.maxTime = this.work_max_time;
		}else{
			options.minTime = this.weekend_min_time;
			options.maxTime = this.weekend_max_time;
		}
		return options;
	},
	
	saveDate : function(){
		var self = this,
			f = this.form,
			b = $('b',f);
		if ( $('.loading',b).size() == 0 )
			b.append('<i class="icon16 loading"></i>');
		$.post(f.attr('action'),f.serializeArray(),function(response){
			$('.loading',b).remove();
			if ( response.data.error )
				self.setError(response.data.error);
			else {
				self.dtpe.val(response.data.end_hour);
				self.dtps.val(response.data.start_hour);
			}
		},'json')
	},
	
	setError : function(i){
		var e = $('<span />');
		e.addClass('datetimepicker-error').text(this.errors[i-1]);
		this.dtpe.after(e);
		setTimeout(this.removeError,3000);
	},
	
	removeError : function(){
		$('.datetimepicker-error',this.form).remove();
	},
	
	isWeekend : function(){
		var ds = this.dtp.val().split('.'),
			d = new Date(ds[2],ds[1]-1,ds[0]);
		return ( d.getDay()==6 || d.getDay()==0 );
	}
};