$(function(){
//当键盘键被松开时发送Ajax获取数据
		$('#text').keyup(function(){
			var keywords = $(this).val();
			if (keywords=='') { $('#word').hide(); return };
			$.ajax({
				url: '/index/paper/get/?wd=' + keywords,
				dataType: 'json',
				// jsonpCallback: 'fun', //回调函数名(值) value
				beforeSend:function(){
					$('#word').append('<div>正在加载。。。</div>');
				},
				success:function(data){
					$('#word').empty().show();
					if (data.s=='')
					{
						$('#word').append('<div class="error">Not find  "' + keywords + '"</div>');
					}
					$.each(data.s, function(){
						$('#word').append('<div class="click_work">'+ this.data +'</div>');
					})
				},
				error:function(){
					$('#word').empty().show();
					$('#word').append('<div class="click_work">Fail "' + keywords + '"</div>');
				}
			})
		})
//点击搜索数据复制给搜索框
		$(document).on('click','.click_work',function(){
			var word = $(this).text();
			$('#text').val(word);
			$('#word').hide();
			// $('#texe').trigger('click');触发搜索事件
		})

	})