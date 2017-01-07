$(function(){
	// 判断Owner中的have、provide、helpTime中的select选择
	var petSize = $('#petSize').val();
	if(petSize>0){
		// 控制have provide helpTime checkbox选中
		ownerHave(petSize);
		ownerProvide(petSize);
		ownerHelpTime(petSize);
	}
});

function ownerHave(petSize){
	for(var i=0; i<petSize; i++){
		var pet_haves = $('.pet_have_'+i).val();
		var pet_have = pet_haves.split(',');
		$.each(pet_have,function(index,value){
			$("input[name='pet_have_"+i+"']").each(function(){
				if($(this).val()==value)
					$(this).attr("checked",true);
			});
		});
	}
}
function ownerProvide(petSize){
	for(var i=0; i<petSize; i++){
		var pet_provides = $('.pet_provide_'+i).val();
		var pet_provide = pet_provides.split(',');
		$.each(pet_provide,function(index,value){
			$("input[name='pet_provide_"+i+"']").each(function(){
				if($(this).val()==value)
					$(this).attr("checked",true);
			});
		});
	}
}
function ownerHelpTime(petSize){
	for(var i=0; i<petSize; i++){
		var pet_helpTimes = $('.pet_helpTime_'+i).val();
		var pet_helpTime = pet_helpTimes.split(',');
		$.each(pet_helpTime,function(index,value){
			$("input[name='pet_helpTime_"+i+"']").each(function(){
				if($(this).val()==value)
					$(this).attr("checked",true);
			});
		});
	}
}

// 删除宠物信息
function delPet(pet_id){
	$.confirm({
		'title'	: 'Delete Confirmation',
		'message': 'You are about to delete this pet. <br />It cannot be restored at a later time! Continue?',
		'buttons': {
			'Yes': {
				'class'	: 'blue',
				'action': function(){
					var pathName = window.document.location.pathname;
					//获取带"/"的项目名
					var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
					$.ajax({
						url : path + "/index.php/Home/Pet/delete", //此处是提交的地址 
						data : {
							pet_id : pet_id
						},  //此处是需要提交给后台的数据
						type : "post",
						cache : false,
						async: false,
						dataType : "json", // 返回json数据
						success : function(data) {
							if(data=="success"){
								alert("Delete Successfully");
							} else {
								alert("Delete Failed, you can try it again!");
							}
							location.href = path + "/index.php/Home/User/userCenter";
						},
						error : function() {  
				           alert("Network error, Please refresh and try it again!");
				           return false;  
						}  
					});
				}
			},
			'No': {
				'class'	: 'gray',
				'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
			}
		}
	});
}

// 删除发布宠物求助信息
function delBeg(beg_id){
	$.confirm({
		'title'	: 'Delete Confirmation',
		'message': 'You are about to delete this beg. <br />It cannot be restored at a later time! Continue?',
		'buttons': {
			'Yes': {
				'class'	: 'blue',
				'action': function(){
					var pathName = window.document.location.pathname;
					//获取带"/"的项目名
					var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
					$.ajax({
						url : path + "/index.php/Home/Beg/delete", //此处是提交的地址 
						data : {
							beg_id : beg_id
						},  //此处是需要提交给后台的数据
						type : "post",
						cache : false,
						async: false,
						dataType : "json", // 返回json数据
						success : function(data) {
							if(data=="success"){
								alert("Delete Successfully");
							} else {
								alert("Delete Failed, you can try it again!");
							}
							location.href = path + "/index.php/Home/User/userCenter";
						},
						error : function() {  
				           alert("Network error, Please refresh and try it again!");
				           return false;  
						}  
					});
				}
			},
			'No': {
				'class'	: 'gray',
				'action': function(){}	// Nothing to do in this case. You can as well omit the action property.
			}
		}
	});
}