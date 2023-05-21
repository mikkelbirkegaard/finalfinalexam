(function($) {
    /*===============================================
    =          AJAX           =
    ===============================================*/
    function content_ajax() {
        var filter = $('.bbh-filter-down-form') // Get form
        var wrapper = $('#content-response') // Get markup grid container
        // console.log('ajax')
        $.ajax({
            url:filter.attr('action'), // Get form action
            data:filter.serialize(), // Get form data
            type:filter.attr('method'), // Get form method
            beforeSend:function(){
                //wrapper.animate({opacity:0},50)// Fade markup out
            },
            success:function(data){
                // console.log('jan ' + data.jan)
                // console.log('feb ' + data.feb)
                // console.log('mar ' + data.mar)
                wrapper.html( data.wine );
            },
            complete:function(){
               // wrapper.animate({opacity:1},50) // Fade markup in
            },
            error:function(error){
                // wrapper.css('opacity', 1) // Fade markup in
                console.log(error) // Console log error
            }
        })
    }

    // Search
    var typingTimer; //timer identifier

     var doneTypingInterval = 300;
     var $input = $('#search-input'); //on keyup, start the countdown

     $input.on('keyup', function () {
       clearTimeout(typingTimer);
       typingTimer = setTimeout(doneTyping, doneTypingInterval);
     }); //on keydown, clear the countdown

     $input.on('keydown', function () {
       clearTimeout(typingTimer);
     }); //user is "finished typing," do something

     function doneTyping() {
        content_ajax()
     }

    $('.bbh-filter-down-form').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        if ($('#search-input').val()) {
        return content_ajax()
        }
    }
    });

    })(jQuery)
