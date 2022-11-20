/**
 * 简介：自定义动态条件下拉框
 * 作者：刘蛟龙
 */
layui.define(['jquery', 'laydate','form'],function(exports){
	var $ = layui.jquery,
	laydate = layui.laydate
	form = layui.form,
	c = layui.hint();
	
	var obj = function(config){
		
		//最后的结果
		this.data={};
		
		//配置
		this.config = {
				reset:false,//重置按钮默认不显示
				field: {nameField:'name',valueField:'value'}//候选项数据的键名
		};
		
		//融合参数
		this.config = $.extend(this.config,config);
		
	}
	
	//初始化
	obj.prototype.init = function(){
		//生成初始化下拉框html，添加到页面中
		var b = this,
		config = b.config,
		html='',       //最后拼接成的html
		n = config.number, //数量
		rn = config.rowNum,//每行几个
		o = config.options,//下拉选项
		elem = $(config.elem);
		
		html += '<form id="selectC-form" class="layui-form"><div class="layui-form-item">';
		for (var i = 0; i < n; i++) {
			html +=	'<div class="layui-input-inline" style="width:100px;">'+
			      	'<div id="lay-unselect'+i+'" lay-index="'+ i +'" class="layui-unselect layui-form-select">'+
						'<div class="layui-select-title">'+
							'<input id="layui-select-title" type="text" placeholder="请选择" value="" readonly="" class="layui-input layui-unselect">'+
							'<i class="layui-edge"/>'+
						'</div>'+
						'<dl class="layui-anim layui-anim-upbit selectC-dd">'+
							'<dd lay-value="" class="layui-select-tips">请选择</dd>';
			//拼接下拉选项
			for (var j = 0; j < o.length; j++) {
				html +=	'<dd lay-value="'+o[j].name+'" class="">'+o[j].elemName+'</dd>';
				//select类型，判断需不需要请求接口获取数据
				if(o[j].type == "select" && o[j].url != null && o[j].url != '' && i==0){
					this.getSelectData(o[j].url, j);
				}	
			}
			html += '</dl></div></div>';
			html +=	'<div class="layui-input-inline" id="inputBox'+i+'">'+
						'<input type="text" id="disabled" autocomplete="off" class="layui-input" disabled>'+
					'</div>';
		}
		html += '<div class="layui-inline">'+
					'<button type="button" class="layui-btn" id="selectC-search-btn">查询</button>';
		if(config.reset){
			html += '<button type="button" class="layui-btn layui-btn-primary" id="selectC-reset">清空</button>';
		}
		html += '</div></div></form>';
		elem.append(html);
		/**搜索按钮点击事件**/
		$('#selectC-search-btn').click(function(){
			//清空之前的条件
			b.data = {};
			var a = elem.find('dd.layui-this');
			elem.find('.selectC-dd dd.layui-this').each(function(){
				var name = $(this).attr("lay-value");
				var value = $("[name='"+name+"']").val();
				if(name!=null && name!="" && value!=null && value!=""){
					b.data[name]=value;
				}
			})
			b.config.search(b.data);
		})
		/**清空按钮点击事件**/
		if(config.reset){
			$('#selectC-reset').click(function(){
				$('#selectC-form')[0].reset();
			})
		}
	}
	
	//生成输入框，根据类型不同，生成的输入框类型也不同
	obj.prototype.createInput = function(n,id){
		//生成初始化下拉框html，添加到页面中
		var config = this.config,
		html='',       //最后拼接成的html
		o = config.options;//下拉选项
		
		//拼接输入框
		for (var l = 0; l < o.length; l++) {
			if(n == o[l].name){
				if(o[l].type == 'input'){
					//输入框
					html += '<input type="text" name="'+ o[l].name +'" class="layui-input">';
				}else if(o[l].type == 'select'){
					//下拉框
					var data = o[l].data;
					var field = o[l].field == null ? config.field : o[l].field;
					html += '<select name="'+ o[l].name +'">'+
							'<option value="">全部</option>';
					for (var k = 0; k < data.length; k++) {
						html += '<option value="'+ data[k][field['valueField']] +'">'+ data[k][field['nameField']] +'</option>';
					}
					html += '</select>';
				}else if(o[l].type == 'date'){
					//日期框
					html += '<input type="text" name="'+ o[l].name +'" class="layui-input" id="dateBox'+ l +'">';
				}
				//先清除之前的输入框
				$('#inputBox' + id).empty();
				//添加到页面中
				$('#inputBox' + id).append(html);
				//日期框需要初始化
				if(o[l].type == 'date'){
					laydate.render({
						elem : '#dateBox'+l,
						range : true
					});
				}
				//重新渲染，为了渲染出下拉框
				form.render();
			}
		}
		
	}
	
	//渲染一个实例
	obj.prototype.render = function(){
		var o = this,
		s = o.config.options,
		c = $(o.config.elem);
		//初始化
		o.init();
		//展开/收起选项
		$('div.layui-unselect').off('click').on('click',function(e){
			//隐藏其他实例显示的弹层
			c.find("div [id*='lay-unselect']").not("#"+$(this).attr("id")).removeClass('layui-form-selected');
			var l = $(this);
			if(l.is('.layui-form-selected')){
				//隐藏下拉框
				l.removeClass('layui-form-selected');
				$(document).off('click');
			}else{
				//显示下拉框
				l.addClass('layui-form-selected');
				//点击页面其他区域，隐藏下拉框
				$(document).on('click',function(e){
					if(e.target.id!==l.attr("id") && e.target.className!=='layui-input layui-unselect'){
						l.removeClass('layui-form-selected');			
						$(document).off('click');
					}
				});
			}
		});
		
		//点击选项
		c.off('click','dd').on('click','dd',function(e){
			//显示之前隐藏的选项
			var lt = $(this).parent().find('.layui-this:first').attr("lay-value");
			if(lt!=null){
				c.find("dd[lay-value='"+ lt +"']").show();
			}
			//移除其他选项的选中样式
			$(this).siblings().removeClass('layui-this');
			//设置选中样式
			$(this).addClass('layui-this');
			//显示选中值
			$(this).parent().parent().find("#layui-select-title:first").val($(this).text());
			//点击选项时的一些操作
			var id = $(this).parent().parent().attr("lay-index");
			var name = $(this).attr("lay-value");
			if(!($(this).is('.layui-select-tips'))){
				//隐藏其他下拉框中这个选项
				c.find("dd[lay-value='"+ name +"']").not($(this)).hide();
				//初始化输入框
				o.createInput(name,id);
			}else{
				//先清除之前的输入框
				$('#inputBox' + id).empty();
				//添加到页面中
				$('#inputBox' + id).append('<input type="text" id="disabled" autocomplete="off" class="layui-input" disabled>');
			}
		});
	}
	
	//获取填写的条件
	obj.prototype.getCondition = function(){
		var b = this,
		config = b.config,
		elem = $(config.elem);
		
		var a = elem.find('dd.layui-this');
		elem.find('.selectC-dd dd.layui-this').each(function(){
			var name = $(this).attr("lay-value");
			var value = $("[name='"+name+"']").val();
			b.data[name]=value;
		})
		return b.data;
	}
	
	//请求接口，获取下拉框数据
	obj.prototype.getSelectData = function(url,optionNum){
		//ajax请求
		$.ajax({ 
			type : "post",
			url : url,
			dataType : "json",
			success : (result)=>{
				if(result.code == 0){
					this.config.options[optionNum].data = typeof result.data == 'string' ? JSON.parse(result.data) : result.data;
				}else{
					throw new Error('selectC：下拉框获取接口异常，' + result.msg);
				}
			},
			error:function(){
				throw new Error('selectC：下拉框获取接口异常');
			}
		})
	}
	
	//输出模块接口
	exports('selectC', function(config){
		var _this = new obj(config);
		_this.render();
		return _this;
	});
});