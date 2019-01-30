(function ($) {

    if (typeof wp === 'undefined' || !wp.hasOwnProperty('media')) {
        return;
    }

    var $imageHolder = $('#cogImageHolder');
    var $imageInput = $('#cogImage');
    var $COGMetaBox = $('#cogMetaBox');
    var $cogSettingsPage = $('#cogSettingsPage');
    var $uploadedFileName = $('#cogUploadedFileName');

    var customUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Open Graph Image',
        button: {
            text: 'Set As Open Graph Image'
        },
        multiple: false
    });
    var thumbURL;
    var attachment;

    customUploader.on('select', function (e) {
		attachment = customUploader.state().get('selection').first().toJSON();
		thumbURL = attachment.sizes['medium'] == undefined ? attachment.url : attachment.sizes['medium'].url;

		//-- Require that images be minimum dimensions.
		if(attachment.width < 200 || attachment.height < 200) {
			alert("Sorry! Your Open Graph image must be at least 200px wide and 200px high.")
			return;
		}

        if ($COGMetaBox) {
            $COGMetaBox.removeClass('has-no-image');
        }

        if ($cogSettingsPage) {
            $cogSettingsPage.removeClass('has-no-image');
        }

        $imageHolder.css('background-image', 'url(' + thumbURL + ')');
        $uploadedFileName.html(attachment.url.split('/').reverse()[0]);

      //-- Set input values.
        $imageInput.val(attachment.id);
    });

    $('#cogImageSelectButton').on('click', function (e) {
        customUploader.open();
        return true;
    });

    $('#ogRemoveImage').on('click', function () {
        if ($COGMetaBox) {
            $COGMetaBox.addClass('has-no-image');
        }
        if ($cogSettingsPage) {
            $cogSettingsPage.addClass('has-no-image');
        }
        $uploadedFileName.html('');
        $imageHolder.css('background-image', 'url("")');
        $imageInput.val('');
    });

    $('#ogForceAll').on('change', function () {
        if ($(this).is(':checked')) {
            $cogSettingsPage.addClass('is-forcing-all');
        } else {
            $cogSettingsPage.removeClass('is-forcing-all');
        }
    });
})(jQuery);
