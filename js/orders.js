$.sod_order_list = {
    link : $('#sort_order_date'),
    form : $('#sort_order_date_form'),
    calendar : $('#calendar'),
    calendar_input : $('#calendar-input'),
    dates_form : $('#s-sod-date-list-form'),

    init : function(){
        var self = this;
        this.link.click(function(){
            //$('input',self.form).val('');
            //self.load();
            self.loadDateList();
        });
        this.calendar_input.datetimepicker({
            lang : 'ru',
            mask : '39.19.2999',
            format : 'Y-m-d',
            timepicker : false,
            //minDate : '0',
            //maxDate : this.max_date,
            //yearStart : this.year_start,
            //yearEnd : this.year_end,
            onSelectDate : function(){
                self.load();
            }
        });
        this.calendar.click(function(){
            self.calendar_input.datetimepicker('show');
        });

        $('#s-content').off('click','#date-list a').on('click','#date-list a',function(){
            self.calendar_input.val($(this).data('date'));
            self.load();
            return false;
        });

    },

    load : function(){
        var self = this,
            li = self.link.closest('li');
        $('i',self.link).removeClass('clock').addClass('loading');
        $.post('?plugin=sod&module=orders',this.form.serializeArray(),function(response){
            $('#s-content').html('').append(response);
            li.closest('ul').find('.selected').removeClass('selected');
            li.addClass('selected');
            $('i',self.link).removeClass('loading').addClass('clock');
        });
        return false;
    },

    loadDateList : function(){
        var self = this,
            li = this.link.closest('li'),
            f = this.dates_form;
        this.loadingOn();
        $.post(f.attr('action'),f.serializeArray(),function(response){
            $('#s-content').html('').append(response);
            li.closest('ul').find('.selected').removeClass('selected');
            li.addClass('selected');
            self.loadingOff();
        });
        return false;
    },

    loadingOn : function(){
        $('i',this.link).removeClass('clock').addClass('loading');
    },

    loadingOff : function(){
        $('i',this.link).removeClass('loading').addClass('clock');
    }
};
