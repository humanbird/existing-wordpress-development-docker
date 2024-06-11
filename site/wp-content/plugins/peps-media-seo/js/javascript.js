jQuery(document).ready(function($) {

    // Character count for title
    $('#pm_seo_title').on('input', function() {
        $("#titleLength").text(80 - $(this).val().length + ' characters left');
    });

    // Character count for description
    $('#pm_seo_description').on('input', function() {
        $("#descLength").text(320 - $(this).val().length + ' characters left');
    });

    // Media manager logic
    $('input#pm-seo-media-manager').click(function(e) {
        e.preventDefault();

        var image_frame;
        if (image_frame) {
            image_frame.open();
            return;
        }

        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Media',
            multiple: false,
            library: {
                type: 'image'
            }
        });

        image_frame.on('close', function() {
            var ids = image_frame.state().get('selection').map(function(attachment) {
                return attachment.id;
            }).join(",");
            $('input#pm-seo-image-id').val(ids);
            refreshImage(ids);
        });

        image_frame.on('open', function() {
            var selection = image_frame.state().get('selection');
            var ids = $('input#pm-seo-image-id').val().split(',');
            ids.forEach(function(id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });
        });

        image_frame.open();
    });

    // Ajax request to refresh the image preview
    function refreshImage(the_id) {
        $.get(ajaxurl, {
            action: 'pm_seo_get_image',
            id: the_id
        }, function(response) {
            if (response.success) {
                $('#pm-seo-social-image').replaceWith(response.data.image);
            }
        });
    }

});
