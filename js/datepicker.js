/* * * * * * Date Picker * * * * * */

jQuery(document).ready(function ($) {
    $( "#eventsndocs_ends, #eventsndocs_starts" ).datepicker({
        prevText: objectL10n.prevText,
        nextText: objectL10n.nextText,
        monthNames: objectL10n.monthNames,
        monthNamesShort: objectL10n.monthNamesShort,
        dayNames: objectL10n.dayNames,
        dayNamesShort: objectL10n.dayNamesShort,
        dayNamesMin: objectL10n.dayNamesMin,
        dateFormat: objectL10n.dateFormat
    });

    // adjust minDate on the fly
    $("#eventsndocs_ends").change(function(){
        var end_date = $(this).datepicker('getDate');
        var start_date = $("#eventsndocs_starts").datepicker('getDate')
        if (end_date < start_date)
            $(this).datepicker( "option", "minDate", new Date(start_date) );
    });
    $("#eventsndocs_starts").change(function(){
        var start_date = $(this).datepicker('getDate');
        var end_date = $("#eventsndocs_ends").datepicker('getDate')
        $( "#eventsndocs_ends" ).datepicker( "option", "minDate", new Date(start_date) );
    });
});
