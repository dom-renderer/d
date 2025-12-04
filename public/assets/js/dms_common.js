function handleBarbadosUI(countryId) {
    const BARBADOS_ID = "20";
        
    if (countryId == BARBADOS_ID) {
        $('#city').closest('div').hide();
        $('#pincode').closest('div').hide();
        $('#pincode').prop('required', false);
        
        if ($('#engineerForm').length && $('#engineerForm').data('validator')) {
            $('#pincode').rules('remove', 'required');
        }

        const statePlaceholder = $('#state').data('select2');
        if (statePlaceholder) {
            statePlaceholder.$container.find('.select2-selection__placeholder').text('Select Parish');
        }

    } else {
        $('#city').closest('div').show();
        $('#pincode').closest('div').show();
        $('#pincode').prop('required', true);

        if ($('#engineerForm').length && $('#engineerForm').data('validator')) {
            $('#pincode').rules('add', { required: true });
        }

        const statePlaceholder = $('#state').data('select2');
        if (statePlaceholder) {
            statePlaceholder.$container.find('.select2-selection__placeholder').text('Select state');
        }
    }
}