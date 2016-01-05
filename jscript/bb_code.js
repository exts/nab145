/**************************************
*
*    Copyrighted Nevux Ability Boards
*    2007 All Rights Reserved
*
***************************************/

function bb_code(type)
{
	if(type.value.match(/IMG/i))
	{
		var cnfirm = prompt("Insert a image url.","http://");
		if(cnfirm.match(/([^?](?:[^\[]+|\[(?!url))*?)/i))
		{
			document.getElementsByTagName("textarea")[0].value += "[img]" + cnfirm + "[/img]";
			document.getElementsByTagName("textarea")[0].focus
			document.getElementsByTagName("textarea")[0].focus();
		}
	}
	else
	{
		if(type.value == type.value.split("*")[0] + "*")
		{
			type.value = type.value.split("*")[0];
			document.getElementsByTagName("textarea")[0].value += "[/" + type.value + "]";
			document.getElementsByTagName("textarea")[0].focus
			document.getElementsByTagName("textarea")[0].focus();
		}
		else
		{
			type.value = type.value + "*";
			document.getElementsByTagName("textarea")[0].value += "[" + type.value.split("*")[0] + "]";
			document.getElementsByTagName("textarea")[0].focus
			document.getElementsByTagName("textarea")[0].focus();
		}
	}
}