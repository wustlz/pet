$(document).ready(function(){
	// 获取当前页面索引
	var page_index = $('#page_index').val();
	// 获取标题的li
	var page_li = $('.top-nav>ul>li');
	// alert(page_ul);
	page_li.each(function(){
	    // alert($(this).index());
	    if(page_index==$(this).index()){
	    	$(this).addClass('active');
	    }
	});
});