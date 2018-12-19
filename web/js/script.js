$(document).ready(function() {
    setSelectHandlers();
});

function setSelectHandlers(){
    $('.select-item-cat').on('change', function(e){
        var option = $(e.target).find('option:selected');
        var id = $(option).data('id');
        if(id == 0){

        } else {
             $.ajax({
                url: '/site/index',
                type: 'POST',
                data: {
                    id: id,
                    name: 'change-cat'
                },
                success: function(res){
                    debugger;
                    console.log(res);
                },
                error: function(res){
                    console.log(res);
                }
            });
        }
    });
}