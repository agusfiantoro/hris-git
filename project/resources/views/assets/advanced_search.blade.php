extendDatatable();
$('#advanced').click(function(){
    console.log('a');
    $('.cf').select2({width:'100%'});
    if($("#cf").css('display') == 'none'){
        $("#cf").show("slow");
    }
    else {
        $("#cf").hide("slow");
    }		
});