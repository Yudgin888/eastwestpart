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

    $('.btn-load').on('click', function(e){
        var id = $(e.target).closest('.btn-load').data('id');
        var url = "/viewpdf?id=" + id;
        $(location).attr('href', url);
    });

    $('.btn-user-remove').on('click', delUser);
});

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