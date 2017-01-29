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
		$('#'+formId+' #alerts').html(alerts.substr(2)+Langs.not_entered);
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