var input_initial_value = '';
var suggest_count = 0;

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
        var url = '/settings?tab=upload';
        window.open(url, '_target');
    });

    $('.model-item-main input:checkbox').on('change', checkAvailableBtnLoadPrice);
    $('.model-item-main .city-input').on('keyup', checkAvailableBtnLoadPrice);
    $('.model-item-main .cost-delivery input').on('keyup', checkAvailableBtnLoadPrice);

    $('.btn-load-price').on('click', openPdfWithOptions);

    $('.btn-open-offer').on('click', function(e){
        var model = $(e.target).closest('.model-item');
        var model_id = $(model).data('id');
        var id_agency = $(model).find('.select-agency option:selected').val();
        var agency_str = '';
        if(id_agency){
            agency_str = '&id_agency=' + id_agency;
        }
        window.open('/viewpdf?id=' + model_id + agency_str, '_blank');
    });

    $('.btn-save-delvr').on('click', function(e){
        var model = $(e.target).closest('.model-item');
        var id = $(model).data('id');
        var delivery = $('#txt-area-delvr' + id).val();
        var name = $(model).find('.input-edit-name').val();
        var id_category = $(model).find('option:selected').val();
        $.ajax({
            url: '/ajax/editmodel',
            data: {
                id: id,
                delivery: delivery,
                name: name,
                id_category: id_category
            },
            type: 'POST',
            success: function(){
                location.reload();
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


    //autoautocomplete
    // читаем ввод с клавиатуры
    $('.city-input').on('keyup', function(e){
        switch(e.keyCode) {
            case 13:  // enter
            case 27:  // escape
            case 38:  // стрелка вверх
            case 40:  // стрелка вниз
                break;
            default:
                var parent = $(e.target).closest('.input-group');
                if($(this).val().length > 2){
                    input_initial_value = $(this).val();
                    $.ajax({
                        url: '/ajax/getcities',
                        data: {
                            query: $(this).val()
                        },
                        type: 'POST',
                        success: function(data){
                            var list = JSON.parse(data);
                            suggest_count = list.length;
                            if(list.length > 0){
                                var wrapper = $(parent).find('.search_advice_wrapper');
                                $(wrapper).html("").show();
                                for(var i in list){
                                    if(list[i] != ''){
                                        $(wrapper).append('<div class="advice_variant">' + list[i] + '</div>');
                                    }
                                }
                            }
                        },
                        error: function(){
                        }
                    });
                } else {
                    var wrapper = $(parent).find('.search_advice_wrapper');
                    $(wrapper).html("").hide();
                    suggest_count = 0;
                }
                break;
        }
    });

    //считываем нажатие клавишь, уже после вывода подсказки
    $(".city-input").keydown(function(e){
        var parent = $(e.target).closest('.input-group');
        var wrapper = $(parent).find('.search_advice_wrapper');
        switch(e.keyCode) {
            // по нажатию клавишь прячем подсказку
            case 13: // enter
            case 27: // escape
                $(wrapper).hide();
                return false;
                break;
            // делаем переход по подсказке стрелочками клавиатуры
            case 38: // стрелка вверх
            case 40: // стрелка вниз
                e.preventDefault();
                var suggest_count = $(wrapper).data('count');
                if(suggest_count){
                    //делаем выделение пунктов в слое, переход по стрелочкам
                    key_activate(e.keyCode - 39, suggest_count, parent);
                }
                break;
        }
    });

    // делаем обработку клика по подсказке
    $(document).on('click', '.advice_variant', function(e){
        var parent = $(e.target).closest('.input-group');
        var search_box = $(parent).find('.city-input');
        $(search_box).val($(this).text());
        var wrapper = $(parent).find('.search_advice_wrapper');
        $(wrapper).fadeOut(350).html('');
    });

    // если кликаем в любом месте сайта, нужно спрятать подсказку
    $(document).on('click', function(){
        $('.search_advice_wrapper').hide();
    });

    // если кликаем на поле input и есть пункты подсказки, то показываем скрытый слой
    $('.city-input').on('click', function(e){
        var parent = $(e.target).closest('.input-group');
        var wrapper = $(parent).find('.search_advice_wrapper');
        if(suggest_count) {
            $(wrapper).show();
        }
        e.stopPropagation();
    });


    $('.model-item .btn-edit-open').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        $(parent).find('.tab1').css('display', 'none');
        $(parent).find('.tab2').css('display', 'block');
    });

    $('.model-item .btn-edit-open-opt').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        var id = $(parent).data('id');
        $.ajax({
            url: '/ajax/geteditoption',
            data: {
                id: id,
            },
            type: 'POST',
            success: function(data){
                if(data != false) {
                    $(parent).find('.tab2')[0].innerHTML = data;
                    $(parent).find('.tab1').css('display', 'none');
                    $(parent).find('.tab2').css('display', 'block');
                }
            },
            error: function(){
            }
        });
    });

    $(document).on('click', '.model-item .btn-edit-close-option', function(e){
        var parent = $(e.target).closest('.model-item');
        $(parent).find('.tab2')[0].innerHTML = '';
        $(parent).find('.tab2').css('display', 'none');
        $(parent).find('.tab1').css('display', 'block');
    });

    $('.model-item .btn-edit-close').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        $(parent).find('.tab2').css('display', 'none');
        $(parent).find('.tab1').css('display', 'block');
    });


    $(document).on('click', '.category-block .btn-edit-close', function(e){
        var parent = $(e.target).closest('.category-block');
        $(parent).find('.tab2').css('display', 'none');
        $(parent).find('.tab1').css('display', 'block');
    });

    $('.category-block .btn-save-category').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        var id = $(parent).data('id');
        var num = $(parent).find('.input-edit-num').val();
        var name = $(parent).find('.input-edit-name').val();
        var id_par = $(parent).find('option:selected').val();
        $.ajax({
            url: '/ajax/editcategory',
            data: {
                id: id,
                num: num,
                name: name,
                id_par: id_par
            },
            type: 'POST',
            success: function(){
                location.reload();
            },
            error: function(){
            }
        });
    });

    $(document).on('click', '.model-item .btn-save-option', function(e){
        var parent = $(e.target).closest('.model-item');
        var id = $(parent).data('id');
        var name = $(parent).find('.input-edit-name').val();
        var model = $(parent).find('.select-model option:selected').val();
        var cost = $(parent).find('.input-edit-cost').val();
        var type = $(parent).find('.select-type option:selected').val();
        $.ajax({
            url: '/ajax/editoption',
            data: {
                id: id,
                name: name,
                model: model,
                cost: cost,
                type: type
            },
            type: 'POST',
            success: function(){
                location.reload();
            },
            error: function(){
            }
        });
    });

    $('.agency-name .btn-save-agency').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        var id = $(parent).data('id');
        var name = $(parent).find('.input-edit-name').val();
        $.ajax({
            url: '/ajax/editagency',
            data: {
                id: id,
                name: name
            },
            type: 'POST',
            success: function(){
                location.reload();
            },
            error: function(){
            }
        });
    });

    $('.agency-block .btn-del-agency').on('click', function(e){
        var parent = $(e.target).closest('.agency-block');
        var elem = $(parent).find('.tab1 p')[0];
        $('#dialog').text("Удалить представительство " + elem.innerText + "?");
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    var id = $(parent).data('id');
                    $.ajax({
                        url: '/ajax/deleteagency',
                        data: {
                            id: id
                        },
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    $('.btn-del-option').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        var elem = $(parent).find('.tab1 .opt-name')[0];
        $('#dialog').text("Удалить опцию " + elem.innerText + "?");
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    var id = $(parent).data('id');
                    $.ajax({
                        url: '/ajax/deleteoption',
                        data: {
                            id: id
                        },
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    $('.agency-block .btn-del-category').on('click', function(e){
        var parent = $(e.target).closest('.agency-block');
        var id = $(parent).data('id');
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    $.ajax({
                        url: '/ajax/deletecategory',
                        data: {
                            id: id,
                            mode: 'save'
                        },
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                     $(this).dialog("close");
                }
            }
        });
    });

    $('.agency-block .btn-del-footer').on('click', function(e){
        var parent = $(e.target).closest('.agency-block');
        var elem = $(parent).find('.tab1 p')[0];
        $('#dialog').text('Удалить футер у ' + elem.innerText + '?');
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    var id = $(parent).data('id');
                    $.ajax({
                        url: '/ajax/deletefooter',
                        data: {
                            id: id
                        },
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    $('.btn-model-cat-remove').on('click', function(e){
        $('#dialog').text('Вы уверены?');
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    $.ajax({
                        url: '/ajax/modelcatremove',
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    $('.btn-option-remove').on('click', function(e){
        $('#dialog').text('Вы уверены?');
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    $.ajax({
                        url: '/ajax/optionremove',
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    $('.btn-del-model').on('click', function(e){
        var parent = $(e.target).closest('.model-item');
        var elem = $(parent).find('h2')[0];
        $('#dialog').text('Удалить: ' + elem.innerText + '?');
        $("#dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                'Да': function() {
                    var id = $(parent).data('id');
                    $.ajax({
                        url: '/ajax/deletemodel',
                        data: {
                            id: id,
                        },
                        type: 'POST',
                        success: function(){
                            location.reload();
                        },
                        error: function(){
                        }
                    });
                    $(this).dialog("close");
                },
                'Нет': function() {
                    $(this).dialog("close");
                }
            }
        });
    });
});

function key_activate(keyCode, suggest_count, parent){
    var suggest_selected = 0;
    var divs = $(parent).find('.search_advice_wrapper div');
    $(divs).eq(suggest_selected - 1).removeClass('active');
    if(keyCode === 1 && suggest_selected < suggest_count){
        suggest_selected++;
    }else if(keyCode === -1 && suggest_selected > 0){
        suggest_selected--;
    }
    var search_box = $(parent).find('.city-input');
    if(suggest_selected > 0){
        $(divs).eq(suggest_selected-1).addClass('active');
        $(search_box).val($(divs).eq(suggest_selected - 1).text());
    } else {
        $(search_box).val(input_initial_value);
    }
}

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
    var id_agency = $(model).find('.select-agency option:selected').val();
    var agency_str = '';
    if(id_agency){
        agency_str = '&id_agency=' + id_agency;
    }
    var url = '/viewpdf?id=' + model_id + '&opts=' + options_id.join('+') + '&city=' + city + '&cost=' + cost + agency_str;
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
    var elem = $(tr).find('td:nth-child(2)')[0];
    $('#dialog').text('Удалить пользователя: ' + elem.innerText + '?');
    $("#dialog").dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            'Да': function() {
                $.ajax({
                    url: '/ajax/deleteuser',
                    data: {
                        id: id
                    },
                    type: 'POST',
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                    }
                });
                $(this).dialog("close");
            },
            'Нет': function() {
                $(this).dialog("close");
            }
        }
    });
}

function getSubCat(id, ism){
    if(ism === 1){
        $('.btn-block').css('display', 'block');
    }
    $.ajax({
        url: '/ajax/changecategory',
        type: 'POST',
        data: {
            id: id,
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