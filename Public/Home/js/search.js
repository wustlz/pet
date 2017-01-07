$(document).ready(function(){
	// 隐藏查询结果
	$('#searchResult').hide();

	$('#searchBtn').click(function(){
		$('#searchResult').show();
		search();
	});
});

function search(){
	// 获取breed
	var breed_ids =[]; 
	$('input[name="breed_id"]:checked').each(function(){ 
		breed_ids.push($(this).val()); 
	});
	// 获取pet_have
	var pet_haves =[]; 
	$('input[name="pet_have"]:checked').each(function(){ 
		pet_haves.push($(this).val()); 
	});
	// 获取pet_provide
	var pet_provides =[]; 
	$('input[name="pet_provide"]:checked').each(function(){ 
		pet_provides.push($(this).val()); 
	});
	// 获取pet_helptime
	var pet_helptimes =[]; 
	$('input[name="pet_helptime"]:checked').each(function(){ 
		pet_helptime.push($(this).val()); 
	});
	// 判断是否选择至少一个查询条件
	if(breed_ids.length==0 && pet_haves.length==0 && pet_provides.length==0 && pet_helptimes.length==0){
		alert('You must select one item at least!');
		return;
	}
	//ajax查询
	var pathName = window.document.location.pathname;
	var path = pathName.substring(0, pathName.substr(1).indexOf('/')+1);
	$.ajax({
		url : path + '/index.php/Home/Beg/complexSearch', //此处是提交的地址 
		data : {
			breed_id : breed_ids,
			pet_have : pet_haves,
			pet_provide : pet_provides,
			pet_helptime : pet_helptimes
		},  //此处是需要提交给后台的数据
		type : "post",
		cache : false,
		async: false,
		dataType : "json", // 返回json数据
		success : function(data) {
			if(data.status=='none'){
				document.getElementById('searchShow').innerHTML = '<span style="font-size:1.2em;color:#FF0000">So sorry, there is no begs for your expect!</span>';
			} else {
				// 分页显示结果
				var nums = 1; //每页出现的数量
				var pages = Math.ceil(data.length/nums); //得到总页数
				var thisDate = function(curr){
				    var str = '', last = curr*nums - 1;
				    last = last >= data.length ? (data.length-1) : last;
				    for(var i = (curr*nums - nums); i <= last; i++){
				        str += '<div class="blog-grid3"><div class="blog-grid1"><div class="text-blog">';
				        str += '<span>'+data[i].beg_ymd.day+'</span>';
				        str += '<small>'+data[i].beg_ymd.month;
				        str += '<br>'+data[i].beg_ymd.year+'</small>';
				        str += '<div class="clearfix"></div></div><ul><li>';
				        str += '<a class="heart" onclick="order('+data[i].beg.beg_id+')"><i class="glyphicon glyphicon-heart"></i></a>';
				        str += '</li></ul></div><div class="blog-grid2">';
				        str += '<a href="'+path+'/index.php/Home/Beg/detailBeg/id/'+data[i].beg.beg_id+'"><img style="width: 700px;" src="'+path+'/Public/Upload/'+data[i].beg.beg_photo+'" class="img-responsive" alt=""></a>';
				        str += '<div class="blog-text"><h5><a href="'+path+'/index.php/Home/Beg/detailBeg/id/'+data[i].beg.beg_id+'">'+data[i].beg.beg_title+'</a></h5><p>'+data[i].beg.beg_notes+'</p></div>';
				        str += '<a href="'+path+'/index.php/Home/Beg/detailBeg/id/'+data[i].beg.beg_id+'" class="more">Read More</a></div><div class="clearfix"></div></div>';
				    }
				    return str;
				};

				//调用分页
				laypage({
				    cont: 'searchPage',
				    pages: pages,
				    jump: function(obj){
				        document.getElementById('searchShow').innerHTML = thisDate(obj.curr);
				    }
				})
			}
		}, error : function() {  
           alert("Network error, please refresh the page and try again");
		}  
	});
}