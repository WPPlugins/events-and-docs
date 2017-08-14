/* * * * * * Options page * * * * * */

jQuery(document).ready(function ($) {

    //carrousel
    $("#optionspage form h2").click(function(){
        $(".form-table").hide();
        if ($(this).hasClass( "checked" )){
            $(this).removeClass( "checked" );
        }else{
            $("form h2.checked").removeClass( "checked" );
            $(this).addClass( "checked" );
            $("form h2.checked + .form-table").show();
        }
    })

});
