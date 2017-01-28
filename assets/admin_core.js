function Send(el) {
	var valuesArray = {};
	var alerts = "";
	var formId = $(el).attr('id');
	$(el).find('input, select, textarea').each(function(e,v)
	{

		if(v.placeholder==undefined) v.placeholder = $(v).attr('data-placeholder');
		valuesArray[e] = {'value':v.value,'name':v.name,'must':parseInt($(v).attr('must')),'title':v.placeholder};
		if (parseInt($(v).attr('must'))==1&&v.value.length==0) alerts+=", "+v.placeholder;

	});
	if (alerts.length>0)
	{
		$('#'+formId+' #alerts').removeClass().addClass('bg-danger');
		$('#'+formId+' #alerts').html(alerts.substr(2)+" not entered");
		return false;
	}
	var url = $(el).attr('action');
    $.ajax({
		dataType: "json",
		url: url,
		data: {values:valuesArray},
		method: 'POST',
		success: function (data) {
			console.log(data);
			console.log(formId);
			if (data.error.length>0){
				$('#'+formId+' #alerts').removeClass().addClass('bg-danger');
				$('#'+formId+' #alerts').html(data.error);
				return false;
			}
			else {
				$('#'+formId+' #alerts').removeClass().addClass('bg-success');
				$('#'+formId+' #alerts').html(data.html);
				$('#'+formId)[0].reset();
				return false;
			}
			return false;
		}
	});
	return false;

}
$( document ).ready(function() {
    $('.dell').on('click',function (e){
        var title = $(this).attr('title');
        var url = $(this).attr('data-link');
        if (confirm("Do you wanna delete "+title+"?")) {
            $.ajax({
                url: url,
                data: {post:'1'},
                method: 'POST',
                success: function (data) {
                    alert(data);
                    location.reload(true);
                }
            });
        }
    });

});

function AddStep(id) {
	count = parseInt($('#step'+id+'').html().split('</select>').length);
	$('#step'+id+' .additional').append('<label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Question '+count+':</label><div class="col-xs-12 col-sm-12 col-md-8 col-lg-10"><select name="step_'+id+'_'+(count-1)+'" class="form-control" data-placeholder="Question '+count+' step '+(id+1)+'" must="1">'+$('#select_questions').html()+'</select></div>');
	$('html, body').animate({
		scrollTop: $("#step"+id+" .additional div:last-child").offset().top-30
	}, 300);
}

function changeSteps(){
	var count = parseInt($('body').html().split('<legend>').length)-1;
	var steps = parseInt($('#number_steps').val());

	if (steps<count){
		for(var i=steps;i<count;i++){
			$('#step'+i).remove();
		}
	}
	if (count<steps){
		for(var i=count;i<steps;i++){
			var HTML = '<div id="step'+i+'" style="padding: 15px;margin-bottom: 25px"><legend>Step '+(i+1)+'</legend>';
			HTML+='<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Hint for step '+(i+1)+' EN:</label>';
			HTML+='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea class="form-control" name="hint'+i+'_en" placeholder="Hint for step '+(i+1)+' EN" must="1"></textarea></div>';

			HTML+='<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Hint for step '+(i+1)+' RO:</label>';
			HTML+='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea class="form-control" name="hint'+i+'_ro" placeholder="Hint for step '+(i+1)+' RO" must="1"></textarea></div>';

			HTML+='<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Hint for step '+(i+1)+' RU:</label>';
			HTML+='<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea class="form-control" name="hint'+i+'_ru" placeholder="Hint for step '+(i+1)+' RU" must="1"></textarea></div>';

			HTML+='<label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Question 1:</label><div class="col-xs-12 col-sm-12 col-md-8 col-lg-10"><select name="step_'+i+'_0" class="form-control" data-placeholder="Question 1 step '+(i+1)+'" must="1">'+$('#select_questions').html()+'</select></div>';
			HTML+='<div class="additional"></div><div style="text-align:center;"><a href="#" onclick="AddStep('+i+')">Add another question for '+(i+1)+' step</a> </div> </div>';
			$('#steps').append(HTML);
		}
	}
}

function  AddCampaign() {
	var count = parseInt($('body').html().split('name="campaign').length)-1;
	var HTML = '<label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Campaign:</label> <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">';
	HTML+='<select name="campaign'+count+'" class="form-control" data-placeholder="Campaign" must="0">'+$('#campaign').html()+'</select>';
	HTML+='</div>';
	$('#ac').append(HTML);
}

function questionType() {
    val = $('#type').val();
    if (val.length==0) return alert('You must choose a single option');

	if (val=='selectText'){
		for(var i=0;i<3;i++)
		{
			var lang = $($('.additional')[i].parentElement).attr('id');
			var HTML = '';
			for(var k=0;k<3;k++){
				    HTML+= '<label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Response #'+(k+1)+'</label>';
					HTML+='<div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">';
					HTML+='<input type="text" class="form-control" name="res_'+lang+'_'+k+'" placeholder="Response #'+(k+1)+'" must="1" value=""/>';
					HTML+='</div>';
			}
			HTML2= '<div id="c"><label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Correct answer</label>';
			HTML2+='<div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">';
			HTML2+='<input type="number" class="form-control" name="correct" placeholder="Correct" must="1" value=""/>';
			HTML2+='</div></div>';
			$('.additional:eq('+i+')').html(HTML);
			$('#c').remove();
			$('#bottom').html(HTML2);
		}
	}
	if(val=='selectImage'){
		var url = document.URL.split('/');
		var link = '';
		for(var k=0;k<url.length-2;k++)
		link+=url[k]+'/';
		link+='uploadphoto/';
		for(var i=0;i<3;i++) {
			var lang = $($('.additional')[i].parentElement).attr('id');
			var HTML = '<iframe src="'+link+lang+'/" style="border: none;width: 100%;height: auto"></iframe>';
			HTML2= '<div id="c"><label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Correct answer</label>';
			HTML2+='<div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">';
			HTML2+='<input type="number" class="form-control" name="correct" placeholder="Correct" must="1" value=""/>';
			HTML2+='</div></div>';
			$('.additional:eq('+i+')').html(HTML);
			$('#c').remove();
			$('#bottom').html(HTML2);
		}
	}
	if (val=='enterAnswer'){
		for(var i=0;i<3;i++) {
			var lang = $($('.additional')[i].parentElement).attr('id');
			var HTML = '';
			HTML+= '<label class="col-xs-12 col-sm-12 col-md-4 col-lg-2">Correct answer</label>';
			HTML+='<div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">';
			HTML+='<input type="text" class="form-control" name="correct_'+lang+'" placeholder="Correct '+lang+'" must="1" value=""/>';
			HTML+='</div>';

			$('.additional:eq(' + i + ')').html(HTML);
		}
	}
}