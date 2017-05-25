
$(document).on( 'click, select, input' , 'select#site_type option' , function(){
    console.log($(this).data('url'));
    $.get( $(this).data('url') , {} , function(data){
        console.log(data);
    });
});

$(document).on( 'change' , 'select#site_type' , function(){
console.log($(this).find('option:selected').val());
    $('#design .section, #programming .section').remove();
    if ( $(this).find('option:selected').val() != 0 ) {
        $('#design, #programming, #final_calculations').show();
    } else {
        $('#design, #programming, #final_calculations').hide();
        return false;
    }

    $.get( $(this).find('option:selected').data('queryUrl') , {} , function(data){

        for( siteType in data.sitesTypes ) {

            var parameter = data.sitesTypes[siteType];
            var blockSelector = '';

            if ( parameter.workType.id == 13 ) {
                blockSelector = '#design';
            }

            if ( parameter.workType.id == 14 ) {
                blockSelector = '#programming';
            }

            if ( blockSelector ) {
                $(blockSelector).prepend('<p class="section"><input type="checkbox" id="checkbox-'+parameter.id+'" data-parameter-id="'+parameter.id+'" checked><label for="checkbox-'+parameter.id+'" checked> '+parameter.parameterName+' </label><input type="text" id="'+parameter.id+'-value" value="'+parameter.parameterValue+'" placeholder="Введите значение" required></p>');
            }
        }


    });
});

var i = 0;
$(document).on( 'click' , 'fieldset .add_templates' , function(e){

    e.preventDefault();

    i++;

    $(this).closest('fieldset').append('<p class="section"><input type="checkbox" id="checkbox-new-'+i+'" checked><label for="checkbox-new-'+i+'"></label><input type="text" id="checkbox-new-'+i+'-name" placeholder="Введите название" required><input type="text" id="checkbox-new-'+i+'-value" placeholder="Введите значение" required></p>');
});

