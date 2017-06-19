(function(){





//=====================================================================================================
//  Action when type of legal entity is being changed
//=====================================================================================================

    $(document).on( 'change' , '#legal_entity_type input[name="legal_entity_type"]' , function(){

        getOrganizationClearData()

    });
//=====================================================================================================
//  END Action when type of legal entity is being changed
//=====================================================================================================


//=====================================================================================================
// This function returns clear organization data
//=====================================================================================================

    function getOrganizationClearData() {

        $.post( 'get-organizations' , { orgType : $('#legal_entity_type input:checked').val() } , function(data){

            var organizations ='<option value="0">Новый заказчик</option>';

            for ( var i in data.organizations ) {
                if ( data.organizations[i].organizationType == false ) {
                    organizations += '<option value="' + data.organizations[i].id + '">' + data.organizations[i].surname + ' ' + data.organizations[i].name + ' ' +data.organizations[i].secondName + '</option>';

                } else {
                    organizations += '<option value="' + data.organizations[i].id + '">' + data.organizations[i].organizationName + '</option>';

                }
            }

            $('#organizations').html(organizations);

            $('#organization_data').html(data.organizationTypeHtml);

        });
    }
//=====================================================================================================
//  END This function returns clear organization data
//=====================================================================================================


//=====================================================================================================
// Fill organization data is saved customer was selected
//=====================================================================================================

    $(document).on( 'change' , 'select#organizations' , function(){

        var selectedType = $(this);

        if ( selectedType.val() == 0 ) {
            getOrganizationClearData();
            return;
        }

        $.post( 'get-organization-data' , { orgId : selectedType.val() } , function(data){

            $('#organization_data').html(data.organizationTypeHtml);

        });
    });
//=====================================================================================================
//  END Fill organization data is saved customer was selected
//=====================================================================================================


//=====================================================================================================
//
//=====================================================================================================

    $(document).on( 'click' , '#organization_data #add_or_update input[type="checkbox"]' , function(){

        var container = $(this).closest('#add_or_update');
        var selectedCheckbox = $(this)

        if ( !selectedCheckbox.prop('checked') ) {
            selectedCheckbox.prop('checked', false);
        } else {
            container.find('input[type="checkbox"]').each(function(){
                $(this).prop('checked', false);
            });

            selectedCheckbox.prop('checked', true);
        }

    });
//=====================================================================================================
//  END
//=====================================================================================================


//=====================================================================================================
//
//=====================================================================================================

    $(document).on( 'click' , '#patenting_form .create_сontract' , function(e){

        e.preventDefault();

        var createContract = $(this);

        createContract.closest('.buttons-block').find('.download_sample').remove();

        $.post( 'get-contract-doc' , createContract.closest('form').serialize() , function(data){

            console.log(data);
            createContract.closest('.buttons-block').append('<a href="/'+data.archive +'" download class="download_sample button">Скачать образец договора</a>');
            createContract.closest('.buttons-block').append('<a class="download_sample button save_client_and_contract">Сохранить/Обновить</a>');

        });

    });
//=====================================================================================================
//  END
//=====================================================================================================


//=====================================================================================================
//
//=====================================================================================================

    $(document).on( 'click' , '#patenting_form .save_client_and_contract' , function(e){

        e.preventDefault();

        var saveContract = $(this);

        saveContract.closest('.buttons-block').find('.download_sample:not(.save_client_and_contract)').remove();

        //createContract.closest('.buttons-block').find('.download_sample').remove();

        $.post( 'save' , saveContract.closest('form').serialize() , function(data){

            console.log(data);

            saveContract.closest('.buttons-block').append('<a href="/'+data.archive +'" download class="download_sample button">Скачать договор</a>');
            // createContract.closest('.buttons-block').append('<a class="download_sample button save_client_and_contract">Сохранить/Обновить</a>');

        });

    });
//=====================================================================================================
//  END
//=====================================================================================================



    $(document).on( 'click' , '#patenting_form #add_customer:checked' , function(e){


    });






})()
