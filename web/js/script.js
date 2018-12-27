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

    $('.btn-load-price').on('click', function(e){
        var model = $(e.target).closest('.model-item');
        var model_id = $(model).data('id');
        var options = $(model).find('input:checkbox:checked');
        var options_id = [];
        for(var i = 0; i < options.length; i++){
            options_id.push($(options[i]).data('id'));
        }
        var city = $(model).find('.city-input').val();
        var cost = $(model).find('.cost-delivery input').val();
        var data = {
            'model_id': model_id,
            'options_id': options_id,
            'city': city,
            'cost': cost
        };
        $.post({
            type: 'POST',
            url: "/viewpdf?id=" + model_id,
            data: data,
            success: function(e){
            }
        });
    });

    $('.btn-open-offer').on('click', function(e){
        var model = $(e.target).closest('.model-item');
        var model_id = $(model).data('id');
        var data = {
            'model_id': model_id
        };
        $.post({
            type: 'POST',
            url: "/viewpdf?id=" + model_id,
            data: data,
            success: function(e){
                var WinId = window.open('', 'newwin', 'width=600,height=800');
                window.open(e, 'window name', 'window settings');
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

    //checkAvailableBtnLoadPrice();


});

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if ((charCode >= 48 && charCode <= 57) || (charCode === 46 && $(evt.target).val().indexOf('.') === -1 && $(evt.target).val().length > 0)) {
        return true;
    }
    return false;
};

function checkAvailableBtnLoadPrice(){
    var models = $('.model-item');

    for(var i = 0; i < models.length; i++){
        var check = $(models[i]).find('.model-options input:checkbox:checked').length;

        if(check > 0){
            $(models[i]).find('.btn-load-price').css('disabled', 'enabled');
        } else {
            $(models[i]).find('.btn-load-price').css('disabled', 'disabled');
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