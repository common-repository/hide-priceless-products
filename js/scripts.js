jQuery(document).ready(function( $ ) {
    if( $('input[name=hidepricelessproductscheckbox]').is(':checked') )
        $('#pricelesscategoriesmultiselect').parents('tr').addClass('pricelesscategoriesvisible');
    else
        $('#pricelesscategoriesmultiselect').parents('tr').addClass('pricelesscategoriesnotvisible');

    if( $('input[name=hidezeropriceproductscheckbox]').is(':checked') )
        $('#zerocategoriesmultiselect').parents('tr').addClass('zerocategoriesvisible');
    else
        $('#zerocategoriesmultiselect').parents('tr').addClass('zerocategoriesnotvisible');
});

jQuery(document).ready(function( $ ) {

    $('#hidepricelessproductscheckbox').change(function(){
        if(this.checked)
            $('#pricelesscategoriesmultiselect').parents('tr').fadeIn('fast');
        else
            $('#pricelesscategoriesmultiselect').parents('tr').fadeOut('fast');

    });

    $('#hidezeropriceproductscheckbox').change(function(){
        if(this.checked)
            $('#zerocategoriesmultiselect').parents('tr').fadeIn('fast');
        else
            $('#zerocategoriesmultiselect').parents('tr').fadeOut('fast');

    });
});