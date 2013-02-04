	var showpageparts;
	var showcollapsed;
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];

			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

    function me_createCookie( name,value,days)
      {
          if ( days)
          {
                  var date = new Date( );
                  date.setTime( date.getTime( )+( days*24*60*60*1000));
                  var expires = "; expires="+date.toGMTString( );
          }
          else var expires = "";
          document.cookie = name+"="+value+expires+"; path=/";
      }
    function me_readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split( ';');
        for( var i=0;i < ca.length;i++)
        {
                var c = ca[i];
                while ( c.charAt( 0)==' ') c = c.substring( 1,c.length);
                if ( c.indexOf( nameEQ) == 0) return c.substring( nameEQ.length,c.length);
        }
        return null;
    }
    function me_eraseCookie(name) { me_createCookie( name,"",-1); }

function showMessageBox (message,status) {
messageBox = $('#multiedit-messagebox');
	messageBox.fadeOut('fast', function(){
	messageBox.html(message);
		if (status=='OK') {messageBox.removeClass('error'); messageBox.addClass('success');}
		else {messageBox.removeClass('success'); messageBox.addClass('error');}
	messageBox.fadeIn('fast');
	});
}

$(".multiedit-slugifier").live('click',function(){
	id = $(this).attr('rel').split('-',2)[1];
	source = $('#title-' + id )
	target = $('#slug-' + id );

	oldval = target.val();
	if (oldval != toSlug(source.val())) {
		target.val(toSlug(source.val()));
		target.trigger("change");
		target.trigger("keyup");
	}
})

$(".multiedit-breadcrumber").live('click',function(){
	id = $(this).attr('rel').split('-',2)[1];
	source = $('#title-' + id )
	target = $('#breadcrumb-' + id );
	oldval = target.val();
	if (oldval != source.val()) {
		target.val(source.val());
		target.trigger("change");
	}
})

$(".multiedit-countchars").live('keyup',function() {
	field = $(this);
	len = field.val().length;
        counterObject = $('#' + field.attr('id')+'-cnt');
	counterObject.html(len);
        if (field.attr('id').substr(0,4)=='desc') {
            if ((100 > len) || (len > 180)) {
                    counterObject.removeClass('green');
                    counterObject.addClass('red');
            }
            else if ((129 < len) && (len < 161)) {
                counterObject.removeClass('red');
                counterObject.addClass('green');
            } else {
                counterObject.removeClass('red');
                counterObject.removeClass('green');
            }
        };
});

$(".multiedit-counttags").live('keyup',function() {
	field = $(this); // this needs impovement for abc,,,,,sda,,,dsa case
	tmp = field.val();
	tmp = tmp.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	tmp = tmp.replace(/^,*/, '').replace(/,*$/, '');
	if (tmp.length > 0) {
	len = tmp.split(',').length;
	$('#' + field.attr('id')+'-cnt').html(len);
	}
	else {
	$('#' + field.attr('id')+'-cnt').html('0');
	}
});

$(".multiedit-slugfield").live('keyup',function() {
	field = $(this);
	$('#' + field.attr('id')+'-title').html(field.val());
});