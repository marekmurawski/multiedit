
<link href="<?php echo PLUGINS_URI; ?>multiedit/multiedit.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>multiedit/js/helpers.js"></script>

<?php if (Plugin::isEnabled('tags_input')): ?>
<script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>tags_input/assets/jquery.autocomplete.pack.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo PLUGINS_URI; ?>tags_input/assets/jquery.tagsinput.min.js"></script>
<link href="<?php echo PLUGINS_URI; ?>multiedit/css/tags_input.css" media="screen" rel="stylesheet" type="text/css" />
<?php endif; ?>

<script>

$(".multiedit-field").live('change',function() {
    field = $(this);
    progressIndicator = $('#'+field.attr('id')+'-loader');
    progressIndicator.addClass('visible');
    var request = $.ajax({
			url:	"<?php echo URL_PUBLIC.ADMIN_DIR; ?>/plugin/multiedit/setvalue",
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


$("#multiedit-fe-show").live('click',function() {
$(this).hide();
document.cookie = 'mtedfe=1; path=/';
   target=$('#multipage_item-'+<?php echo $page_id; ?>);
    target.fadeOut('fast', function(){
	var request = $.ajax({
			url:	"<?php echo URL_PUBLIC.ADMIN_DIR; ?>/plugin/multiedit/getonepage/"
				+ <?php echo $page_id; ?> + '/0/0/1',
			type:   'get',
			success: function(data){
				target.html(data);
				$(".multiedit-countchars").trigger('keyup');
				$(".multiedit-counttags").trigger('keyup');
				target.fadeIn('fast');
				$('#multiedit-list').show();
                  <?php if (Plugin::isEnabled('tags_input')): ?>
                    $('.multiedit-field-tags').tagsInput({
                        interactive:true,
                        defaultText:'add a tag',
                        minChars:1,
                        width:'auto',
                        minInputWidth: '100px',
                        height:'64px',
                        'hide':true,
                        'delimiter':',',
                        'unique':true,
                        removeWithBackspace:true,
                        placeholderColor:'#666666',
                        autosize: false,
                        comfortZone: 20,
                        inputPadding: 6*2,
                        onChange : function() {
                          $(this).trigger('change');
                          $(".multiedit-counttags").trigger('keyup');
                        },
                        autocomplete_url: "<?php echo URL_PUBLIC.ADMIN_DIR ?>/plugin/tags_input/autocomplete/index?v=0.1",
                        autocomplete: {
                            selectFirst: false,
                            autoFill:false,
                            matchContains: true,
                            minChars: 0,
                            scroll: true,
                            scrollHeight: 100
                        }
                    });
                  <?php endif; ?>
                $("#multiedit-fe-hide").fadeIn('slow');
				},
			error: function( data ) {
					alert (dump(data));
				}
			})
		})
})



$("#multiedit-fe-hide").live('click',function() {
    $(this).hide();
    me_eraseCookie('mtedfe');
    target=$('#multipage_item-'+<?php echo $page_id; ?>);
    target.hide();
    $("#multiedit-fe-show").fadeIn('slow');
});

$(document).ready( function() {
  if (me_readCookie("mtedfe") === '1') {
    //alert("hello again");
    $("#multiedit-fe-show").trigger('click');
  }
  else {
//    document.cookie = "mtedfe=1";
//    $("#multiedit-frontend-trigger").trigger('click');
  }
});

</script>

<div id="multiedit-wrapper" class="frontend">
  <div id="multiedit-fe-hide"></div>
	<div id="multiedit-list" style="display: none;">
		<div class="multiedit-item-root multiedit-item" id="multipage_item-<?php echo $page_id; ?>" style="box-shadow: 0px 0px 16px 4px rgba(0,0,0,0.3);">
		</div>
	</div>
	<div id="multiedit-fe-show"></div>
</div>