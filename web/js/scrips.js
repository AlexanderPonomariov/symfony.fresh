
(function(){




$(document).on( 'click, select, input' , 'select#site_type option' , function(){
    console.log($(this).data('url'));
    $.get( $(this).data('url') , {} , function(data){
        console.log(data);
    });
});

$(document).on( 'change' , 'select#site_type' , function(){

    //console.log('hello');

    var selectedType = $(this);
    var adaptive = false;
    var for_complicate = false;

    //check_required_fields();

    $('#design .section, #programming .section').remove();

    $.get( $(this).find('option:selected').data('queryUrl') , {} , function(data){

        var finalCalculations = $('#final_calculations');

        if ( selectedType.find('option:selected').val() != 0 ) {
            $('#design, #programming, #final_calculations').show();
        } else {
            $('#design, #programming, #final_calculations').hide();
            return false;
        }

        //console.log(data.sitesTypes);
        for( siteType in data.sitesTypes ) {

            var parameter = data.sitesTypes[siteType];
            var blockSelector = '';

            if ( parameter.workType.id == 1 ) {
                blockSelector = 'design';
            }

            if ( parameter.workType.id == 3 ) {
                blockSelector = 'mark_up';
            }

            if ( parameter.workType.id == 2 ) {
                blockSelector = 'programming';
            }

            if ( blockSelector ) {
                $('#'+blockSelector).prepend('' +
                    '<div class="section">' +
                        '<input type="checkbox" id="'+parameter.id+'-'+blockSelector+'-checkbox" name="'+parameter.id+'-'+blockSelector+'-checkbox" data-parameter-id="'+parameter.id+'" checked><div class="field"><label for="checkbox-'+parameter.id+'"> '+parameter.parameterName+' </label>' +
                        '<input type="text" id="'+parameter.id+'-'+blockSelector+'-value" name="'+parameter.id+'-'+blockSelector+'-value" value="'+parameter.parameterValue+'" placeholder="Введите значение" required class="value input"><label for="checkbox-'+parameter.id+'" class="hours"> час.</label></div>' +
                    '</div>'
                );
            }

            // if ( parameter.workType.id == 3 ) {
            //     adaptive = true;
            //     finalCalculations.find('#adaptive').closest('p').remove();
            //     finalCalculations.find('div.adaptive').prepend('' +
            //         '<p>' +
            //             '<label for="adaptive">Адаптив</label>' +
            //             '<input type="text" class="input" name="adaptive" id="adaptive" value="'+parameter.parameterValue+'" class="value">' +
            //             '<label for="adaptive" class="field_label">%</label>' +
            //         '</p>'
            //     );
            // }
            //
            // if ( parameter.workType.id == 4 ) {
            //     for_complicate = true;
            //     finalCalculations.find('#for_complicate').closest('p').remove();
            //     finalCalculations.find('div.for_complicate').prepend(
            //         '<p>' +
            //             '<label for="for_complicate">За сложность</label>' +
            //             '<input type="text" class="input" name="for_complicate" id="for_complicate" value="'+parameter.parameterValue+'" class="value">' +
            //             '<label for="for_complicate" class="field_label">%</label>' +
            //         '</p>'
            //     );
            // }
        }
        //
        // if ( !for_complicate ) {
        //     finalCalculations.find('#for_complicate').closest('p').remove();
        //     finalCalculations.find('div.for_complicate').prepend(
        //         '<p>' +
        //             '<label for="for_complicate">За сложность</label>' +
        //             '<input type="text" class="input" name="for_complicate" id="for_complicate">' +
        //             '<label for="for_complicate" class="field_label">%</label>' +
        //         '</p>'
        //     );
        // }
        //
        // if ( !adaptive ) {
        //     finalCalculations.find('#adaptive').closest('p').remove();
        //     finalCalculations.find('div.adaptive').prepend(
        //         '<p>' +
        //             '<label for="adaptive">Адаптив</label>' +
        //             '<input type="text" class="input" name="adaptive" id="adaptive">' +
        //             '<label for="adaptive" class="field_label">%</label>' +
        //         '</p>'
        //     );
        // }

    });
});

var i = 0;
$(document).on( 'click' , 'fieldset .add_templates' , function(e){

    e.preventDefault();

    var workType = $(this).closest('fieldset').prop('id');
    // console.log(workType);

    i++;

    $(this).closest('fieldset').append(
        '<div class="section added">' +
            '<input type="checkbox" id="'+i+'-'+workType+'-checkbox-new" name="'+i+'-'+workType+'-checkbox-new" checked class="custom"><div class="field"><label for="checkbox-new-'+i+'"></label>' +
            '<input type="text" id="'+i+'-'+workType+'-new-name" name="'+i+'-'+workType+'-new-name" placeholder="Название" required class="value input">' +
            '<input type="text" id="'+i+'-'+workType+'-new-value" name="'+i+'-'+workType+'-new-value" placeholder="Значение" required class="value input"><label for="checkbox-new-'+i+'-value" class="hours"> час.</label> </div> ' +
            '<a href="#" class="del-custom-param"></a>' +
        '</div>'
    );
});

$(document).on( 'click' , '.del-custom-param' , function(e){

    e.preventDefault();

    $(this).closest('.section').remove();

});


    var design=0;
    var programming=0;
    var adaptiveDesign=0;
    var markUp=0;
    var markUpDole=0.3;
    var programmingFinal=0;
    var markUp=0;
    var totalTime=0;
    var markUpPrice = 0;
    var programmingPrice = 0;
    var designPrice = 0;
    var totalPrice = 0;
    var pricePerOnePayment = 0;



    $(document).on( 'click' , '.calculate-button' , function(e){

    design=0;
    programming=0;
    adaptiveDesign=0;
    markUp=0;
    markUpDole=0.3;
    programmingFinal=0;
    markUp=0;
    totalTime=0;
    markUpPrice = 0;
    programmingPrice = 0;
    designPrice = 0;
    totalPrice = 0;
    pricePerOnePayment = 0;

    e.preventDefault();

    //console.log(check_required_fields());

    //if ( check_required_fields() ) return false;


    $('#design').find('input:checked').each(function(){
        design += parseInt($(this).siblings('.field').find('input.value[id$="value"]').val())*1;
        // console.log($(this).siblings('.field').find('input.value[id$="value"]').val());
    });
    $('#programming').find('input:checked').each(function(){
        programming += parseInt($(this).siblings('.field').find('input.value[id$="value"]').val());
    });

    $('#mark_up').find('input:checked').each(function(){
        markUp += parseInt($(this).siblings('.field').find('input.value[id$="value"]').val());
    });

    // adaptiveDesign = Math.ceil( design * ( +( $('#adaptive').val() ? $('#adaptive').val() : 1 ) ) / 100 );

    adaptiveDesign = Math.round( (+design*$('#design .hour_price #design_hour_price').val() + +markUp*$('#mark_up .hour_price mark_up_hour_price').val() ) * ( $('#adaptive').val()/100 +1 ) );

    //programmingFinal = (+adaptiveDesign + +design) + programming - Math.ceil( ( +programming + +adaptiveDesign + +design )*markUpDole );

    //markUp = Math.ceil( ( +programming + +adaptiveDesign + +design ) * ( $("#mark_up").val()/100 ) );

    //totalTime = Math.ceil( (markUp + programmingFinal + adaptiveDesign + design)*( 1 + $('#for_complicate').val()/100 )*( 1 - $('#discount').val()/100 ) );

    totalTime = Math.round( (+adaptiveDesign + +programming) * ( 1 + $('#for_complicate').val()/100 ) );

    //totalPrice = (Math.ceil(+totalTime*$('#hour_price').val()/100))*100;

    totalPrice = totalTime * $('#hour_price').val();

    pricePerOnePayment = +totalPrice/$('#quantity_of_payments').val();

    $('#price_per_one_payment').val(pricePerOnePayment);
    $('#final_price').val(totalPrice);

    // markUpPrice = totalPrice*programming/( +programming + +adaptiveDesign + +design );
    // programmingPrice = totalPrice*adaptiveDesign/( +programming + +adaptiveDesign + +design );
    // designPrice = totalPrice*design/( +programming + +adaptiveDesign + +design );

    $(this).closest('#final_calculations').append(
        '<input type="hidden" id="calc_design" name="calc_design" value="'+design+'">'+
        '<input type="hidden" id="calc_adaptiveDesign" name="calc_adaptiveDesign" value="'+adaptiveDesign+'">'+
        '<input type="hidden" id="calc_programming" name="calc_programming" value="'+programming+'">'+
        '<input type="hidden" id="calc_markUp" name="calc_markUp" value="'+markUp+'">'+
    //     '<input type="hidden" id="calc_programmingFinal" name="calc_programmingFinal" value="'+programmingFinal+'">'+
        '<input type="hidden" id="calc_totalTime" name="calc_totalTime" value="'+totalTime+'">'+
        '<input type="hidden" id="calc_totalPrice" name="calc_totalPrice" value="'+totalPrice+'">'+
        '<input type="hidden" id="calc_pricePerOnePayment" name="calc_pricePerOnePayment" value="'+pricePerOnePayment+'">'
    //     '<input type="hidden" id="calc_markUpPrice" name="calc_markUpPrice" value="'+markUpPrice+'">'+
    //     '<input type="hidden" id="calc_programmingPrice" name="calc_" value="'+programmingPrice+'">'+
    //     '<input type="hidden" id="calc_designPrice" name="calc_designPrice" value="'+designPrice+'">'

    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    // '<input type="hidden" id="calc_" name="calc_" value="'++'">'+
    );

    console.log('design = ' + design);
    console.log('adaptiveDesign = ' + adaptiveDesign);
    // console.log('design + adaptiveDesign = '+ (+adaptiveDesign + +design) );
    console.log('programming = '+ programming);
    console.log('markUp = ' + markUp);
    // console.log('programmingFinal = '+ programmingFinal );
    console.log('totalTime = '+ totalTime );
    console.log('totalPrice = '+ totalPrice );
    console.log('pricePerOnePayment = '+ pricePerOnePayment );
    // console.log('markUpPrice = '+ markUpPrice );
    // console.log('programmingPrice = '+ programmingPrice );
    // console.log('designPrice = '+ designPrice );




});


function check_required_fields() {
    $('#calculator_form').find('input[required]').each(function(){
       // console.log($(this).val().length);
        if ( !$(this).val().length ) {
            $(this).addClass('error');
        }
    });

//console.log($('#calculator_form').find('.error').length);
    if ( $('#calculator_form').find('.error').length ) return true;

}

$(document).on( 'change, keydown' , '#calculator_form .error', function() {

    var currentInput = $(this);

    if ( currentInput.val().length >= 3 ) {
        currentInput.removeClass('error');
    } 
    
});

$(document).on( 'click' , '.get_pdf_path' , function(e){
    e.preventDefault();

    if ( $('.download_pdf') ) $('.download_pdf').remove();

    console.log('designPrice = '+ designPrice );

    var createPdfButton = $(this);

    console.log( createPdfButton.closest('form') );

    $.post( '/generate_pdf' , createPdfButton.closest('form').serialize() , function(data){
        console.log(data);
        createPdfButton.parent().append('<a href="/'+data+'" download class="download_pdf">Скачать PDF</a>');
    });
});





    $(document).on('click',".dropdown dt a", function(e) {
        e.preventDefault();
        $(this).closest('.dropdown').find("dd ul").slideToggle('fast');
    });

    $(document).on('click',".dropdown dd ul li a", function() {
        $(".dropdown dd ul").hide();
    });

    function getSelectedValue(id) {
        return $("#" + id).find("dt a span.value").html();
    }

    $(document).bind('click', function(e) {
        var $clicked = $(e.target);
        if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
    });

    $(document).ready(function() {

        $('.mutliSelect input[type="checkbox"]').each(function(){

            var title = $(this).val() + ",";
            var checkedRows = $(this).closest('.field').find('.multiSel input').val();

            if ($(this).is(':checked')) {
                console.log(checkedRows + title);
                $(this).closest('.field').find('.multiSel input').val( checkedRows + title );
            }
        });
    });


    $(document).on('click','.mutliSelect input[type="checkbox"]', function() {

        var title = $(this).val() + ",";

        var checkedRows = $(this).closest('.dropdown').find('.multiSel input').val();

        if ($(this).is(':checked')) {
            $(this).closest('.dropdown').find('.multiSel input').val( checkedRows + title );
        } else {
            $(this).closest('.dropdown').find('.multiSel input').val( checkedRows.replace( title, '' ) );

        }
    });







})()