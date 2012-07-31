<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }
?>

<div id="multiedit-wrapper">
	<div id="multiedit-header">
			<?php 
			echo $pagesList; 
			?>
	</div>
	<img id="multiedit-list-preloader" src="<?php echo PLUGINS_URI.'multiedit/icons/progress-big.gif'; ?>"/>
	<div id="multiedit-list">
		<?php 
		echo $rootItem; 
		?>
		<?php 
		echo $itemsList; 
		?>
	</div>
</div>	
<script>
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
function showMessageBox (message,status) {
messageBox = $('#multiedit-messagebox');
	messageBox.fadeOut('fast', function(){
	messageBox.html(message);
		if (status=='OK') {messageBox.removeClass('error'); messageBox.addClass('success');}
		else {messageBox.removeClass('success'); messageBox.addClass('error');}
	messageBox.fadeIn('fast');
	});
}	

$(document).ready( function() { // @todo: change counters to be initially PHP processed - faster
	$(".countchars").trigger('keyup');
	$(".counttags").trigger('keyup');

	// FLOATING messages
	var top = $('#multiedit-messages').offset().top - parseFloat($('#multiedit-messages').css('marginTop').replace(/auto/, 0));
	$(window).scroll(function (event) {
	// what the y position of the scroll is
	var y = $(this).scrollTop();

	// whether that's below the form
	if (y >= top) {
	// if so, ad the fixed class
	$('#multiedit-messages').addClass('fixed');
	} else {
	// otherwise remove it
	$('#multiedit-messages').removeClass('fixed');
	}
  });
	
})

$(".multiedit-items-select").live('change',function() {
   if ((document.getElementById("showpageparts").checked)) {showpageparts='1'} else {showpageparts='0'}
   if ((document.getElementById("showcollapsed").checked)) {showcollapsed='1'} else {showcollapsed='0'}
    $('#multiedit-list').fadeOut('fast', function(){
	    $('#multiedit-list-preloader').addClass('preloading');
	var request = $.ajax({
			url:	"<?php echo get_url('plugin/multiedit/getsubpages/'); ?>"
				+ $('#multiedit-pageslist').val() + '/' +
				$("#multiedit-pageslist-sorting").val() + '/' +
				$("#multiedit-pageslist-order").val()+ '/' +
				showpageparts + '/' +
				showcollapsed + '/'
				, 
			type:   'get',
			success: function(data){
				$('#multiedit-list').html(data);
				$(".multiedit-countchars").trigger('keyup');
				$(".multiedit-counttags").trigger('keyup');
				$('#multiedit-list-preloader').removeClass('preloading');
				$('#multiedit-list').fadeIn('fast');
				},
			error: function( data ) {
					alert (dump(data));
				}				
			})
})

});

$(".slugifier").live('click',function(){
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

$(".breadcrumber").live('click',function(){
	id = $(this).attr('rel').split('-',2)[1];
	source = $('#title-' + id )
	target = $('#breadcrumb-' + id );
	oldval = target.val();
	if (oldval != source.val()) {
		target.val(source.val());
		target.trigger("change");
	}
})

$("#reload-list").live('click',function(){
	$('#multiedit-pageslist').trigger('change');
})

$(".reload-item").live('click',function(){
   if ((document.getElementById("showpageparts").checked)) {showpageparts='1'} else {showpageparts='0'}
   if ((document.getElementById("showcollapsed").checked)) {showcollapsed='1'} else {showcollapsed='0'}
   id = $(this).attr('rel').split('-',2)[1];
	target = $('#' + $(this).attr('rel'));
		$.get("<?php echo get_url('plugin/multiedit/getonepage/'); ?>" + id + '/' + showpageparts+ '/' + showcollapsed,
		function(data){
			target.fadeOut('fast', function(){
			target.html(data);
			$(".countchars").trigger('keyup');
			$(".counttags").trigger('keyup');
			target.fadeIn('fast');
			showMessageBox ('Reloaded item ' + id,'OK');
			});	
		});
})
$(".hide-item").live('click',function(){
	id = $(this).attr('rel').split('-',2)[1];
	target = $('#' + $(this).attr('rel'));
			target.fadeOut('fast', function(){
				target.remove();
			});

})

$(".multiedit-item .header").live('click',function(){
$(this).parent().find('table').toggle();
})

$(".collapse-item").live('click',function(){
	id = $(this).attr('rel').split('-',2)[1];
	target = $('#' + $(this).attr('rel'));
			target.find('table').toggle();
})

$(".multiedit-field").live('change',function() {
    field = $(this);
    //alert(field.attr('name'));
    progressIndicator = $('#'+field.attr('id')+'-loader');
    progressIndicator.addClass('visible');
    var request = $.ajax({
			url:	"<?php echo get_url('plugin/multiedit/setvalue/'); ?>", 
			type:   'post',
			data:	{ 
					item: field.attr('name'),
					value: field.val()
				},
			success: function( data ) {
					if (data.status == 'OK') {
							field.removeClass('error');  field.addClass('success');

							showMessageBox (data.message,data.status);
							setTimeout(function(){progressIndicator.removeClass('visible');},300)
							if (data.hasOwnProperty('datetime') && data.hasOwnProperty('identifier')) {
								$('#updated_on-'+data.identifier).html(data.datetime).addClass('wasmodified');
							}
							// change status if page has expired
							if (data.hasOwnProperty('setstatus') && data.hasOwnProperty('identifier')) {
								$('#status_id-'+data.identifier).val(data.setstatus);
								indicator = $('#status-indicator-'+data.identifier);
								indicator.removeClass('status-1 status-10 status-100 status-101 status-200');
								indicator.addClass('status-' + data.setstatus);
							}								
								// status change management @todo DRY status change
								if (field.hasClass('status-select')) {
								indicator = $('#'+field.attr('rel'));	
								indicator.removeClass('status-1 status-10 status-100 status-101 status-200');
								indicator.addClass('status-' + field.val());
								}
					} else {
							field.removeClass('success'); field.addClass('error');

							field.val(data.oldvalue);
							showMessageBox (data.message,data.status);
							setTimeout(function(){progressIndicator.removeClass('visible');},300)
							$(".slugfield").trigger('keyup');
							
					}
				},
			error: function( data ) {
					showMessageBox (dump(data));
				},
			dataType: 'json'				
			})
});

$(".countchars").live('keyup',function() {
	field = $(this);
	len = field.val().length
	// var wlength = $(this).val().split(' ').length;
	$('#' + field.attr('id')+'-cnt').html(len);
});

$(".counttags").live('keyup',function() {
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

$(".slugfield").live('keyup',function() {
	field = $(this);
	//alert(field.val());
	// var wlength = $(this).val().split(' ').length;
	$('#' + field.attr('id')+'-title').html(field.val());	
});
</script>

