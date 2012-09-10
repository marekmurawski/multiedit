    <link href="/wolf/plugins/multiedit/multiedit.css" media="screen" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="/wolf/plugins/multiedit/js/helpers.js"></script> 
<script>
$(".multiedit-field").live('change',function() {
    field = $(this);
    progressIndicator = $('#'+field.attr('id')+'-loader');
    progressIndicator.addClass('visible');
    var request = $.ajax({
			url:	"/<?php echo ADMIN_DIR; ?>/plugin/multiedit/setvalue/'); ?>", 
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
							$(".multiedit-slugfield").trigger('keyup');
							
					}
				},
			error: function( data ) {
					showMessageBox (dump(data));
				},
			dataType: 'json'				
			})
});


$("#multiedit-frontend-trigger").live('click',function() {
   target=$('#multipage_item-'+<?php echo $page_id; ?>);
    target.fadeOut('fast', function(){
	var request = $.ajax({
			url:	"/<?php echo ADMIN_DIR; ?>/plugin/multiedit/getonepage/"
				+ <?php echo $page_id; ?> + '/0/0', 
			type:   'get',
			success: function(data){
				target.html(data);
				$(".multiedit-countchars").trigger('keyup');
				$(".multiedit-counttags").trigger('keyup');
				target.fadeIn('fast');
				$('#multiedit-list').show();
				},
			error: function( data ) {
					alert (dump(data));
				}				
			})
		})
})

$(document).ready( function() {
$("#multiedit-frontend-trigger").trigger('click');
});

</script>
<div id="multiedit-wrapper" style="margin: 0px 1%; position: fixed; bottom: 0px; width: 98%;">
	<div id="multiedit-list" style="display: none;">
		<div class="multiedit-item-root multiedit-item" id="multipage_item-<?php echo $page_id; ?>">
		</div>	
	</div>
	<div id="multiedit-frontend-trigger">
		[edit meta]
	</div>
</div>