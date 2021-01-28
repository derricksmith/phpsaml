
$(function() { 
	$('#keep-order').multiSelect({
		  keepOrder: true,
          afterSelect: function(value, text){
            var get_val = $("#multiple_value").val();
            var hidden_val = (get_val != "") ? get_val+"," : get_val;
            $("#multiple_value").val(hidden_val+""+value);
			$('#requested-authn-context').val(JSON.stringify(new_val));
			alert(JSON.stringify(new_val));
          },
          afterDeselect: function(value, text){
            var get_val = $("#multiple_value").val();
            var new_val = get_val.replace(value, "");
            $("#multiple_value").val(new_val);
			$('#requested-authn-context').val(JSON.stringify(new_val));
			alert(JSON.stringify(new_val));
          }
    });
});