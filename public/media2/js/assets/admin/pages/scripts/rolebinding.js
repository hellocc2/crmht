$(function(){
		// handle group checkbox:

        console.log('role');
        $('.dataTables_wrapper').on('change', '.group-checkable', function () {
        	var checked = $(this).is(":checked");
            $(this).attr("checked", checked).parent("span").toggleClass('checked');
            var set = $(this).parents('table').find('.checkboxes');
            // var checked = $(this).is(":checked");
            $(set).each(function () {
                if($(this).parent("span").hasClass('checked')){
                    $(this).parent("span").removeClass('checked');
                    $(this).attr("checked",false);
                }else{
                    $(this).parent("span").addClass('checked');
                    $(this).attr("checked",true);
                }
            });
            jQuery.uniform.update(set);
            chooserole();
            $(this).parents('table').find('.checkboxes:checked').parents('tr').remove();
            checkpageInfo();
            pageContainer();
        });
        $('.dataTables_wrapper').on('change', '.checkboxes', function() {
            var checked = $(this).is(":checked");
            $(this).attr("checked", checked).parent("span").toggleClass('checked');
            chooserole();
   //          var choicesrowlen = $('this').parents('tr').children('td').length;
			// var choicestarget = $('#sample_2').find('tbody');
   //          var shtml = "";
			// var rowid = $(this).attr('rowid');
			// $(this).children('td').each(function(index){
			// 	if(index == 0){
			// 		shtml = '<td><div><span><input type="text" class="form-control input-inline input-mini" value="' +$('#sample_2').find('tr').length+ '"></span></div><input type="hidden" name="role_id" value='+rowid+' /></td>';
			// 	}else if(index ==  choicesrowlen-1){
			// 		shtml += '<td><a class="label label-sm label-danger delct" href="#">删除</a></td>';
			// 	}else{
			// 	shtml += '<td>' + $(this).html() + '</td>'
			// 	}
			// });
			// shtml = '<tr rowid='+ rowid +'>' + shtml + '</tr>';
			// if(choicestarget.find('[rowid="'+rowid+'"]').length==0){
			// 	choicestarget.append(shtml);
			// }
			$(this).parents('tr').remove();
            checkpageInfo();
            pageContainer();
        });
        $('#sample_2').on('click', '.delct', function(event) {
        	event.preventDefault();
        	/* Act on the event */
        	$(this).parents('tr').remove();
            checkpageInfo();
            pageContainer();
        });
        $('#rbconfirm').click(function(){
        	$('#rbform').submit();
        });
        $('#sample_2_paginate').on('click', 'li>a', function(event) {
        	event.preventDefault();
        	var clicknum = $(this).attr('title');
        	var currentpagedom = $('#sample_2_paginate').find('.active').children('a');
        	switch(clicknum){
        		case '首页':
        			pageContainer(1);
        			break;
        		case '上一页':
        			pageContainer(parseInt(currentpagedom.attr('title'))-1);
        			break;
        		case '下一页':
        			pageContainer(parseInt(currentpagedom.attr('title'))+1);
        			break;
        		case '尾页':
        			pageContainer($(this).parents('ul').children().length);
        			break;
        		default:
        		pageContainer($(this).text());
        	}
        });

        // 已选分页
	var checkpagenum = function(){
		return $('#sample_2').find('tbody').children('tr').length;
	}
	var checkpageInfo = function (){
		return $('#sample_2_info').text('共选定了'+checkpagenum()+'条记录');
	}
    var pageContainer = function(gotopage){
    	var maxpagelen = 5;
    	var pagehtml="";
    	var maxnum = Math.ceil(checkpagenum()/maxpagelen);
    	if(gotopage==undefined){
    		var currentpage = parseInt($('#sample_2_paginate').find('.active').text());
    	}else if(gotopage<1){
    		var currentpage = 1;
    	}else if(gotopage>maxnum){
			var currentpage = maxnum;
    	}else{
    		var currentpage = gotopage;
    	}
    		$('#sample_2').find('tbody').children('tr').hide();
    		$('#sample_2').find('tbody').children('tr:lt('+(currentpage*maxpagelen)+'):gt('+((currentpage-1)*maxpagelen)+')').show();
    		$('#sample_2').find('tbody').children('tr:eq('+(currentpage-1)*maxpagelen+')').show();
    		// if (checkpagenum()>maxpagelen) {$('#sample_2').find('tbody').children('tr:eq('+(currentpage*maxpagelen)+')').show();};
        if(currentpage == 1){var prevable = 'disabled';}
        if(currentpage == maxnum){var nextable = 'disabled';}
    	pagehtml += '<li class="prev '+prevable+'">'
        pagehtml += '<a href="#" title="首页"> <i class="fa fa-angle-double-left"></i>'
        pagehtml += '</a>'
        pagehtml += '</li>'
        pagehtml += '<li class="prev '+prevable+'">'
        pagehtml += '<a href="#" title="上一页">'
        pagehtml += '<i class="fa fa-angle-left"></i>'
        pagehtml += '</a>'
        pagehtml += '</li>'
        if (maxnum==0) {
        	pagehtml += '<li class="active"><a href="#" title="1">1</a></li>'
        }else{
        	for(var i=0;i<maxnum;i++){
		        pagehtml += '<li class="'
		        if(currentpage == i+1){pagehtml += 'active'}
		        pagehtml += '">'
		        pagehtml += '<a href="#" title="'+parseInt(i+1)+'">'+parseInt(i+1)+'</a>'
		        pagehtml += '</li>'
		    }
        }

        pagehtml += '<li class="next '+nextable+'">'
        pagehtml += '<a href="#" title="下一页"> <i class="fa fa-angle-right"></i>'
        pagehtml += '</a>'
        pagehtml += '</li>'
        pagehtml += '<li class="next '+nextable+'">'
        pagehtml += '<a href="#" title="尾页">'
        pagehtml += '<i class="fa fa-angle-double-right"></i>'
        pagehtml += '</a>'
        pagehtml += '</li>'
    	$('#sample_2_paginate').children().html(pagehtml);
    }



	var chooserole = function (){
		var choicesrow = $('#sample_1').find('tbody').find('.checked').parents('tr')
		var choicestarget = $('#sample_2').find('tbody');
		choicesrow.each(function() {
			var shtml = "";
			var rowid = $(this).attr('rowid');
			$(this).children('td').each(function(index){
				if(index == 0){
					shtml = '<td><div><span><input type="text" class="form-control input-inline input-mini" name="orderid[]" value="' +$('#sample_2').find('tr').length+ '"></span></div><input type="hidden" name="role_id[]" value='+rowid+' /></td>';
				}else if(index == choicesrow.eq(0).children().length-1){
					shtml += '<td><a class="label label-sm label-danger delct" href="#">删除</a></td>';
				}else{
				shtml += '<td>' + $(this).html() + '</td>'
				}
			});
			shtml = '<tr rowid='+ rowid +'>' + shtml + '</tr>';
			if(choicestarget.find('[rowid="'+rowid+'"]').length==0){
				choicestarget.append(shtml);
			}
		});
	}

    // 默认加载空搜索
    $('.search_role').trigger('click')

	})
