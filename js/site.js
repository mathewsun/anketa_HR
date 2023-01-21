dhtmlx.message.defPosition="top";

function alert_error(message_text){
	dhtmlx.message({ type:"error", text:message_text });
}

function alert_info(message_text){
	dhtmlx.message({ type:"alert", text:message_text });
}
