(function($){
    function formatSelected (state) {
        if (!state.id) {
            return state.text
        }
        let bbhClass = state.element.value

        var $state = '<span id="bbh-custom-icon" class="'+ state.element.value +'" style="margin-right: 6px;"></span><span class="text">' + state.text + '</span>'

        return $state
    }
    function formatList (state) {
        if (!state.id) {
           return state.text
        }
        var $state = $(
            '<span class="'+ state.element.value +'" style="margin-right: 10px; font-size: 2em;"></span><span class="text">' + state.text + '</span>'
        )
        return $state

    }

    acf.add_filter('select2_args', function( args, $select, settings, $field ){
        if($field.hasClass('icomoon-select-element')){
            args['width'] = '200px'
            args['templateSelection'] = formatSelected
            args['templateResult'] = formatList

        }
        // return
        return args
    })


    /*LOTTIE - APPEND - Backend*/
    $(document).ready(function () {
        var templateUrl = object_name.templateUrl;
        $(".icomoon-select-element").each(function() {
            let selected = $(this).find('select [selected="selected"]').val();
            if (selected) {
            if (selected.includes("lottie")) {
                let lottieName = selected;
                let fullPath = templateUrl + "/assets/json/" + selected + ".json"
                $(this).append('<lottie-player class="bbh-lottie-player" src="' + fullPath + '"  background="transparent"  speed="1"  style="width: 150px; height: 150px;"  loop controls autoplay></lottie-player>')
            }
            }

        });
        $(".icomoon-select-element").change(function(){
            let iconClass = $(this).find('#bbh-custom-icon').attr('class');
            // console.log(iconClass);
            $(this).closest('.icomoon-select-element').attr('id',iconClass);
            $(this).find('.bbh-lottie-player').remove()
            if (iconClass.includes("lottie")) {
                let lottieName = iconClass;
                let fullPath = templateUrl + "/assets/json/" + lottieName + ".json"
                // console.log(lottieName)
                $(this).append('<lottie-player class="bbh-lottie-player" src="' + fullPath + '"  background="transparent"  speed="1"  style="width: 150px; height: 150px;"  loop controls autoplay></lottie-player>')
            }

        });
    });

})(jQuery)
