$(document).ready(function() {
    var opt = $('.selects-block option:selected');
    if($(opt).length && $(opt).data('id') !== 0){
        getSubCat($(opt).data('id'), $(opt).data('ism'));
    }

    $('.selects-block').delegate('.select-item-cat', 'change', function(e){
        var option = $(e.target).find('option:selected');
        var id = $(option).data('id');
        $(e.target).nextAll().remove();
        $('.btn-block').css('display', 'none');
        if(id !== 0){
            getSubCat(id, $(option).data('ism'));
        }
    });

    $('#btn-view-mdl').on('click', function(e){
        var id = $('.selects-block option:selected').last().data('id');
        var url = "/model?id=" + id;
        $(location).attr('href', url);
    });

    $('.btn-no-basic-pdf').on('click', function(e){
        var url = '/settings?tab=upload-offers';
        window.open(url, '_target');
    });

    $('.model-item-main input:checkbox').on('change', checkAvailableBtnLoadPrice);
    $('.model-item-main .city-input').on('keyup', checkAvailableBtnLoadPrice);
    $('.model-item-main .cost-delivery input').on('keyup', checkAvailableBtnLoadPrice);

    $('.btn-load-price').on('click', openPdfWithOptions);

    $('.btn-open-offer').on('click', function(e){
        var model = $(e.target).closest('.model-item');
        var model_id = $(model).data('id');
        window.open('/viewpdf?id=' + model_id, '_blank');
    });

    $('.btn-save-delvr').on('click', function(e){
        var model = $(e.target).closest('.model-item');
        var model_id = $(model).data('id');
        var txt = $('#txt-area-delvr' + model_id).val();
        $.ajax({
            url: '/site/settings',
            data: {
                name: 'edit-model',
                id: model_id,
                txt: txt
            },
            type: 'POST',
            success: function(){
            },
            error: function(){
            }
        });
    });

    $('.btn-user-remove').on('click', delUser);

    $('.nav-link-sort').on('click', function(e){
        $('.nav-link-sort').removeClass('active');
        $(e.target).addClass('active');
        var iso = $(e.target).data('iso');
        if(iso === 2){
            $('.model-item').css('display', 'block');
        } else {
            $('.model-item').css('display', 'none');
            $('.model-item[data-iso=' + iso + ']').css('display', 'block');
        }
    });

    checkAvailableBtnLoadPrice();
});

function openPdfWithOptions(e) {
    var model = $(e.target).closest('.model-item');
    var model_id = $(model).data('id');
    var options = $(model).find('input:checkbox:checked');
    var options_id = [];
    for(var i = 0; i < options.length; i++){
        options_id.push($(options[i]).data('id'));
    }
    var city = $(model).find('.city-input').val();
    var cost = $(model).find('.cost-delivery input').val();
    var url = '/viewpdf?id=' + model_id + '&opts=' + options_id.join('+') + '&city=' + city + '&cost=' + cost;
    window.open(url, '_blank');
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if ((charCode >= 48 && charCode <= 57) || (charCode === 46 && $(evt.target).val().indexOf('.') === -1 && $(evt.target).val().length > 0)) {
        return true;
    }
    return false;
};

function checkAvailableBtnLoadPrice(){
    var models = $('.model-item-main');
    if(models) {
        for (var i = 0; i < models.length; i++) {
            var check = $(models[i]).find('.model-options input:checkbox:checked').length;
            var elem = $(models[i]).find('.btn-load-price');
            var city = $(models[i]).find('.city-input').val();
            var cost = $(models[i]).find('.cost-delivery input').val();
            $(elem).unbind('click');
            if (check > 0 || (city.length > 0 && cost.length > 0)) {
                $(elem).removeClass('disabled-btn');
                $(elem).bind('click', openPdfWithOptions);
            } else {
                $(elem).addClass('disabled-btn');
            }
        }
    }
}

function delUser(e){
    var tr = $(e.target).closest('tr')[0];
    var id = $(tr).data('id');
    $.ajax({
        url: '/site/settings',
        data: {
            name: 'delete-user',
            id: id
        },
        type: 'POST',
        success: function(){
        },
        error: function(){
        }
    });
}

function getSubCat(id, ism){
    if(ism === 1){
        $('.btn-block').css('display', 'block');
    }
    $.ajax({
        url: '/site/index',
        type: 'POST',
        data: {
            id: id,
            name: 'change-cat'
        },
        success: function(res){
            if($('.select-item-cat').length === 1 && res.length > 0){
                $('.selects-block').append('<label class="lab-sub-cat">Выберите подкатегорию</label>');
            }
            if(res.length > 0) {
                $('.selects-block').append(res);
            }
        },
        error: function(res){
            console.log(res);
        }
    });
}