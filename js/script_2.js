$(function() {
        function saveDate(d,b,e){
            $.post('/shop-sod-plugin-set-date/',{
                'date' : d,
                'start_hour' : b,
                'end_hour' : e
            });
        };
        function ch(e){
            $('#sod-form > .interval').hide();
			$('#sod-form .selected').removeClass('selected');
            if ( e ){
                $('#sod-form > .interval.weekend').show();
                if ( $('#sod-form > .interval.weekend.selected').size() == 0 )
                    $('#sod-form > .interval.weekend:first').addClass('selected');
            } else {
                $('#sod-form > .interval.workday').show();
                if ( $('#sod-form > .interval.workday.selected').size() == 0 )
                    $('#sod-form > .interval.workday:first').addClass('selected');
            }
            switchShipping();
        }
        function format(date,type){
            var d = date.getDate(),
                m = date.getMonth()+1,
                y = date.getFullYear(),
                s = '';
            d = d < 10 ? '0'+d : d;
            m = m < 10 ? '0'+m : m;
            //return d+'.'+m+'.'+y;
			return type ? (d+'.'+m+'.'+y) : (y+'/'+m+'/'+d);
        }
        function switchShipping(){
            var s = $('#sod-form .interval.selected').data('shipping-id');
            $('#sod-form .interval').each(function(){
                var c = $(this).data('shipping-id');
                ( c == s ) ? $('.shipping-'+c+', .shipping-'+c+' .wa-address').show() : $('.shipping-'+c).hide();
            })
            $('.shipping-'+s).find('[name="shipping_id"]').attr('checked',true).prop('checked',true);
			saveDate($('#datetimepicker').val(),$('#sod-form .interval.selected').data('start'),$('#sod-form .interval.selected').data('end'));
        }
		function hide(){
			t = true;
			$('#sod-form .interval').each(function(){
                var c = $(this).data('shipping-id');
				$('.shipping-'+c).size() && (t = false);
            });
			t && $('#sod-form').hide();
		}
		hide();
		
		$('[name="shipping_id"]').click(function(){
			var id = $(this).val();
			if ( $('#sod-form [data-shipping-id="'+id+'"]').size() == 0 )
				$('#sod-form').hide();
			else
				$('#sod-form').show();
		})
		
        $('#sod-form').on('click','.interval',function(){
            $(this).parent().find('.selected').removeClass('selected');
            $(this).addClass('selected');
            switchShipping();
        })
        switchShipping();
        var today = new Date(),
			min_date = new Date(),
			max_date = new Date();
		
		if ( today.getDay() == 5 ){
			min_date.setDate(today.getDate()+4);
		} else {
			if ( today.getDay() == 6 )
				min_date.setDate(today.getDate()+3);
			else
				min_date.setDate(today.getDate()+2);
		}

		max_date.setDate(min_date.getDate()+3);
		
        //today.setDate(today.getDate());
        today.getDay() == 0 && today.setDate(today.getDate()+1);
        ch( today.getDay() == 6 )
        $('#datetimepicker').val(format(min_date,1));
        var options = {
            lang : 'ru',
            mask : '39.19.2999',
            format : 'd.m.Y',
            timepicker : false,
            minDate : format(min_date,0),
            maxDate : format(max_date,0),
            //yearStart : this.year_start,
            //yearEnd : this.year_end,
            dayOfWeekStart:1,
            onSelectDate : function(){
                setTimeout(function(){
                    ch($('.xdsoft_datepicker.active .xdsoft_current').is('.xdsoft_day_of_week6'));
                },100)
            },
            onChangeDateTime : function( ct ){
				setTimeout(function(){
                    ch($('.xdsoft_datepicker.active .xdsoft_current').is('.xdsoft_day_of_week6'));
                },100)
            },
            onGenerate : function( ct ){
                $('.xdsoft_day_of_week0',this).addClass('xdsoft_disabled');
            }
        };
        $('#datetimepicker').datetimepicker(options);
    });
