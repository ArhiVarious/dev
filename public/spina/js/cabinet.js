$(function() {

    $.datepicker.regional['ru'] = {
            closeText: 'Закрыть',
            prevText: '&#x3c;Пред',
            nextText: 'След&#x3e;',
            currentText: 'Сегодня',
            monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
            monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
            'Июл','Авг','Сен','Окт','Ноя','Дек'],
            dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
            dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
            dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
            dateFormat: 'dd.mm.yy', firstDay: 1,
            isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    //$.datepicker.setDefaults({onSelect:function(){console.log(this)}});
    $(".datepicker").datepicker();
    $(".datepicker").datepicker('option','onSelect',function(){
        reload_data(1);
    });
    resize_scrolled = function(){
        $('.datatable.scrolled').parent('div').children().each(function(){
            $(this).css('width',($('.datatable.scrolled').parent('div').width()-($(this).outerWidth()-$(this).width())));
        })
        $('.datatable.scrolled').css('margin-top',($('.box-header.fixed').height()+$('.datatable.head').height()));
    };
    resize_scrolled();
    $(window).on('resize',resize_scrolled);
    
    reload_data = function(all){
        sort = '';
        arsort=[];
        i = 0;
        $('.datatable th.sorting').each(function(){
            i++;
            if($(this).find('i').text()>0)i=$(this).find('i').text();
            if($(this).data('sort').length)    arsort[i]={
                'key':$(this).data('key'), 
                'sort' : $(this).data('sort')
            };
        })
        $.each(arsort,function(i){
            if(this.sort)sort += '/key/'+this.key+'/sort/'+this.sort;
        })
        blocked = true;
        filter = $('.datatable th.filtered input, .datatable.head select').serializeArray();
        $('#load_data').show();
        $('#load_data').effect("pulsate", {
            times: 5
        }, 1000);
        $.ajax({
            type: "POST",
            url: $('.datatable.scrolled').data('url')+sort,
            data: {
                page: all?all:parseInt($('.datatable.scrolled').data('page'))+1,
                filter: filter
            }
        }).success(function( data,st,xhr ) {
            if(all)$('.datatable.scrolled tbody').empty();
            if(data.result)
                $( "#datatableRowTemplate" ).tmpl( data.result ).appendTo('.datatable.scrolled tbody');
            $.each(data.meta.cnt,function(i,v){
                $('#'+i+'_sum').text(v);
            });
            $('#str_cnt i').text(data.meta.cnt.cnt);
            $('.datatable.scrolled').data('page',all?all:parseInt($('.datatable.scrolled').data('page'))+1);
            blocked = false;
            $('#load_data').finish().hide();
        }).fail(function(jqXHR, textStatus) {
            if(jqXHR.status==401)window.location = location.orign + '/cabinet';
        });
    }
    blocked = false;
    $('#maincontainer').on('scroll',function(){
        if(($('#maincontainer #main').height())<($(document).height()+$('#maincontainer').scrollTop()+($(document).height()*2)) && !blocked)
        {
            if(!$('.datatable.scrolled.paged_off').length)reload_data();
        }
    });
    key_pressed = false;
    $('.datatable th.filtered').on('keyup',function(e){
        if(e.keyCode==13)reload_data(1);
    })
    $('.datatable th.filtered input').on('click',function(e){
        e.stopPropagation();
    })
    
    $('.datatable.head select').on('change',function(e){
        reload_data(1);
    })
    
    
    $('.datatable th.sorting').on('click', function(e){
        if(e.ctrlKey && !key_pressed)
        {
            if($(this).parent('tr').find('i').length==0)
                $('.datatable th.sorting').removeClass('sorting_asc').removeClass('sorting_desc').data('sort','');
            key_pressed = true;
            $(document).on('keyup',function(){
                reload_data(1);
                $(document).off('keyup');
                key_pressed = false;
            })
        }

        if(!$(this).data('int'))
        {
            switch($(this).data('sort'))
            {
                case 'desc':
                    sorting = '';
                    break;
                case 'asc':
                    sorting = 'desc'
                    break;
                default:
                    sorting = 'asc'
                    break
            }    
        }
        else
            switch($(this).data('sort'))
            {
                case 'asc':
                    sorting = '';
                    break;
                case 'desc':
                    sorting = 'asc'
                    break;
                default:
                    sorting = 'desc'
                    break
            }
        
        if(e.ctrlKey && !$(this).find('i').text()){
            last = 0;
            $(this).parent('tr').find('i').each(function(){
                if($(this).text()>last)
                    last=$(this).text()
            })
            $(this).append('<i>'+(parseInt(last)+1)+'</i>');
        }
        else if(!e.ctrlKey)
        {
            $(this).parent('tr').find('i').remove();
        }
        else if(!sorting)
        {
            s = $(this).find('i').text();
            $(this).find('i').remove();
            $(this).parent('tr').find('i').each(function(){
                if($(this).text()>s)
                    $(this).text(parseInt($(this).text())-1)
            })
        }
        
        if(!e.ctrlKey)
        {
            $('.datatable th.sorting').removeClass('sorting_asc').removeClass('sorting_desc').data('sort','');
        }
        else
        {
            $(this).removeClass('sorting_asc').removeClass('sorting_desc').data('sort','');
        }
        $(this).addClass('sorting_'+sorting);
        $(this).data('sort',sorting);
        if(!e.ctrlKey)reload_data(1);
    });
    
    $('div.quick-actions a').on('click',function(){
        var a = $(this);
        $.ajax({
            type: "POST",
            url: a.attr('href'),
            beforeSend: function()
            {
                $('#modalcontainer > div').remove();
                $('<div class="box"><div class="box-header"><h1>Пожалуйста подождите</h1></div><div class="box-content">Идёт формирование отчёта</div></div>').appendTo('#modalcontainer');
                $('#overlay').show();
            }
        }).success(function( data,st,xhr ) {
            $('#overlay').hide();
            window.location = a.attr('href');
        }).fail(function(jqXHR, textStatus) {
            if(jqXHR.status==401)window.location = location.orign + '/cabinet';
            $('#overlay').hide();
        });
        return false;
    })
    $('.datatable.scrolled .checker').on('click',function(){
        var a = $(this);
        $.ajax({
            type: "POST",
            url: a.parents('table').data('checker_url'),
            data: $(this).data()
        }).success(function( data,st,xhr ) {
            a.text(data.data)
        }).fail(function(jqXHR, textStatus) {
            if(jqXHR.status==401)window.location = location.orign + '/cabinet';
        });
        return false;
    })
});
