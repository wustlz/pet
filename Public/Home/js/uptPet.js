$(function(){
	// 控制breed选择
	var breed_id = $('#before_breed_id').val();
	$('select[name="breed_id"]').val(breed_id);
	// 控制have provide helpTime checkbox选中
	ownerHave();
	ownerProvide();
	ownerHelpTime();
});

function ownerHave(){
	var pet_haves = $('#before_pet_have').val();
	var pet_have = pet_haves.split(',');
	$.each(pet_have,function(index,value){
		$("input[name='pet_have[]']").each(function(){
			if($(this).val()==value)
				$(this).attr("checked",true);
		});
	});
}
function ownerProvide(){
	var pet_provides = $('#before_pet_provide').val();
	var pet_provide = pet_provides.split(',');
	$.each(pet_provide,function(index,value){
		$("input[name='pet_provide[]']").each(function(){
			if($(this).val()==value)
				$(this).attr("checked",true);
		});
	});
}
function ownerHelpTime(){
	var pet_helpTimes = $('#before_pet_helpTime').val();
	var pet_helpTime = pet_helpTimes.split(',');
	$.each(pet_helpTime,function(index,value){
		$("input[name='pet_helpTime[]']").each(function(){
			if($(this).val()==value)
				$(this).attr("checked",true);
		});
	});
}