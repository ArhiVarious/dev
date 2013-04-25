$(function() {
    if(window.getComputedStyle(document.body,':after').getPropertyValue('content')!='media_query_on')
    {
        $(window).on('resize',function(){
            var i = null
            if($(this).width()<1280)
            {
                i = 1280
            }
            else if($(this).width()<1380)
            {
                i = 1380
            }
            else if ($(this).width()<1500)
            {
                i = 1500
            }
            else if ($(this).width()>1500)
                i = 0
        
            $('body').removeClass()
            if(i)$('body').addClass('r_'+i)
  
        })
        $(window).resize();
    }
    /******************
    Tablet rotation
  ******************/
  
    var isiPad = navigator.userAgent.match(/iPad/i) !== null;
  
    if(isiPad) {
        $('body').prepend('<div id="rotatedevice"><h1>Пожалуйста разверните ваше устройство на 90 градусов</div>');
    }
  
    /********
    Login
  ********/
  
    $('#login_entry > a').click(function() {
        $(this).fadeOut(200, function() {
            $('#login_form').fadeIn();
        });

        return false;
    });
  
    /********************
    Modal preparation
  ********************/
  
    $('body').prepend('<div id="overlay"><div id="modalcontainer"></div></div>');
  
    /*******
    PJAX
  *******/
  
    $('nav#primary a').click(function() {
        window.location = $(this).attr("href");
        return false;
    });
  

  
    /****************
    Notifications
  ****************/

    var maxHeight = $(window).height() - $('#secondary ul').height() - 50;
    $('#notifications').css({
        'max-height': maxHeight
    });
  
  
    $('#notifications').prepend('<a href="#">Show all notifications</a>');
  
    $('#notifications > a').click(function() {
        var container = $('#notifications');
        var height = $('#notifications ul').height() + 24;
    
        if(container.hasClass('expanded')) {
            container.animate({
                'height': 42
            }, 200);
            container.removeClass('expanded');
            $(this).html('show all notifications');
        } else {
            container.animate({
                'height': height
            }, 200);
            container.addClass('expanded');
            $(this).html('hide notifications');
        }
    
        return false;
    });
  
    function init() {
    
        /**************************
      Obtrusive notifications
    **************************/
    
        $('.notification .close').click(function() {
            $(this).closest('.notification').animate({
                'opacity': 0.01
            }, 200, function() {
                $(this).slideUp(200);
            });
        });
    
        /*************
      Datepicker
    *************/
    
        $('.datepicker').datepicker();


        /************
      Code view
    ************/
  
        $('code').each(function() {
            var elem = $(this);
            var lang = elem.attr("class");
    
            elem.sourcerer(lang);
        });
  
        /*************
      Datatables
    *************/
  

  
        $('.dataTables_wrapper').each(function() {
            var table = $(this);
            var info = table.find('.dataTables_info');
            var paginate = table.find('.dataTables_paginate');
    
            table.find('.datatable').after('<div class="action_bar nomargin"></div>');
            table.find('.action_bar').prepend(info).append(paginate);
        });
    
        /************************
      Combined input fields
    ************************/
    
        $('div.combined p:last-child').addClass('last-child');
  
        /**********
      Sliders
    **********/
        $(".slider, .slider-vertical").each(function() {
            var options = $(this).metadata();
            $(this).slider(options, {
                animate: true
            });
        });
  
    
        /*******
      Tags
    *******/
    
        $('.taginput').tagsInput({
            'width':'auto'
        });
  
        /****************
      Progress bars
    ****************/
  
        $(".progressbar").each(function() {
            var options = $(this).metadata();
            $(this).progressbar(options);
        });
  
        /**********************
      Modal functionality
    **********************/
  
        $('a.modal').each(function() {
            var link = $(this);
            var id = link.attr('href');
            var target = $(id);
      
            if($("#modalcontainer " + id).length === 0) {
                $("#modalcontainer").append(target);
            }
      
            $("#main " + id).remove();
    
            link.click(function() {
                $('#modalcontainer > div').hide();
                target.show();
                $('#overlay').show();
                return false;
            });
        });
  
        $('.close').click(function() {
            $('#modalcontainer > div').hide();
            $('#overlay').hide();
    
            return false;
        });
    
        /***********************
      Secondary navigation
    ***********************/
    
        $('nav#secondary > ul > li > a').click(function() {
            $('nav#secondary li').removeClass('active');
            $(this).parent().addClass('active');
        });
  
        /********************
      Pretty checkboxes
    ********************/
  
        $('input[type=checkbox], input[type=radio]').each(function() {
            if($(this).siblings('label').length > 0) {
                $(this).prettyCheckboxes();
            }
        });
  
        /**********************
      Pretty select boxes
    **********************/
  
        $('select').parent().each(function(index) {
            $(this).css('position', 'relative');
            $(this).css('z-index', 99-index);
        });
    

  
        /******************
      Window resizing
    ******************/

        $(window).resize(function() {
            $('.chzn-container').each(function(){
                $(".chzn-container").css({
                    'width': '100%'
                });
                var res_wid_drop = ($(".chzn-container").width() - 2);
                $(".chzn-drop").css({
                    'width': res_wid_drop
                });
            });
        });

  
  
        /******************
      Form Validation
    ******************/
  
        $("form").each(function() {
            $(this).validate({
                wrapper: 'span class="error"',
                meta: 'validate',
                highlight: function(element, errorClass, validClass) {
                    if (element.type === 'radio') {
                        this.findByName(element.name).addClass(errorClass).removeClass(validClass);
                    } else {
                        $(element).addClass(errorClass).removeClass(validClass);
                    }
        
                    // Show icon in parent element
                    var error = $(element).parent().find('span.error');
        
                    error.siblings('.icon').hide(0, function() {
                        error.show();
                    });
                },
                unhighlight: function(element, errorClass, validClass) {
                    if (element.type === 'radio') {
                        this.findByName(element.name).removeClass(errorClass).addClass(validClass);
                    } else {
                        $(element).removeClass(errorClass).addClass(validClass);
                    }
        
                    // Hide icon in parent element
                    $(element).parent().find('span.error').hide(0, function() {
                        $(element).parent().find('span.valid').fadeIn(200);
                    });
                }
            });
        });
    
  
        // Add valid icons to validatable fields
        $('form p > *').each(function() {
            var element = $(this);
      
            if(element.metadata().validate) {
                element.parent().append('<span class="icon tick valid"></span>');
            }
        });
    }
  
    init();

});

/*************************
  Notification function!
*************************/

function notification(message, error, icon, image) {
    if(icon === null) {
        icon = 'tick2';
    }
  
    if(image) {
        image = 'icon16';
    } else {
        image = 'glyph';
    }
  
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    
    if (hours < 10) {
        hours = "0" + hours;
    }
  
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
  
    var time = hours + ':' + minutes;
  
    if(error) {
        $('#notifications ul').append('<li class="error"><span class="' + image + ' cross"></span> ' + message + ' <span class="time">' + time + '</span></li>');
    } else {
        $('#notifications ul').append('<li><span class="' + image + ' ' + icon + '"></span> ' + message + ' <span class="time">' + time + '</span></li>');
    }
  
    $('#notifications ul li:last-child').hide();
    $('#notifications ul li:last-child').slideDown(200);
}