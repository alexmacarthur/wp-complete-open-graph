(function($) {
  var $imageHolder = $('#cogImageHolder');
  var $imageInput = $('#cogImage');
  var $COGMetaBox = $('#cogMetaBox');
  var $COGSettingsBox = $('#cogSettingsBox');
  var $uploadedFileName = $('#cogUploadedFileName');
  var customUploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose Open Graph Image',
      button: {
          text: 'Set As Open Graph Image'
      },
      multiple: false
    });
  var thumbURL;
  var ogURL;
  var attachment;

  customUploader.on('select', function(e) {
    attachment = customUploader.state().get('selection').first().toJSON();
    thumbURL = attachment.sizes['medium'] == undefined ? attachment.url : attachment.sizes['medium'].url;
    if($COGMetaBox) {
      $COGMetaBox.removeClass('has-no-image');
    }
    if($COGSettingsBox) {
      $COGSettingsBox.removeClass('has-no-image');
    }
    $imageHolder.css('background-image', 'url(' + thumbURL + ')');
    $uploadedFileName.html(attachment.url.split('/').reverse()[0]);
    ogURL = attachment.sizes['open-graph'] == undefined ? attachment.url : attachment.sizes['open-graph'].url;
    $imageInput.val(ogURL);
  });

  $('#cogImageSelectButton').on('click', function(e){
    customUploader.open();
    return true;
  });

  $('#ogRemoveImage').on('click', function() {
    if($COGMetaBox) {
      $COGMetaBox.addClass('has-no-image');
    }
    if($COGSettingsBox) {
      $COGSettingsBox.addClass('has-no-image');
    }
    $uploadedFileName.html('');
    $imageHolder.css('background-image', 'url("")');
    $imageInput.val('');
  });
})(jQuery);
