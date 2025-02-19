jQuery(document).ready(function ($) {
    var frame;

    $('#add-property-gallery').click(function (e) {
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Images',
            button: { text: 'Use Images' },
            multiple: true
        });

        frame.on('select', function () {
            var images = frame.state().get('selection').map(function (attachment) {
                attachment = attachment.toJSON();
                return attachment.id;
            });

            var existing = $('#property_gallery').val().split(',').filter(Boolean);
            existing = existing.concat(images);
            $('#property_gallery').val(existing.join(','));

            updateGalleryPreview(existing);
        });

        frame.open();
    });

    function updateGalleryPreview(images) {
        $('#property-gallery-container').empty();
        images.forEach(function (id) {
            var imageUrl = wp.media.attachment(id).get('url');
            $('#property-gallery-container').append(
                `<div class="gallery-image" data-id="${id}">
                    <img src="${imageUrl}" />
                    <button type="button" class="remove-image">×</button>
                </div>`
            );
        });
    }

    $(document).on('click', '.remove-image', function () {
        var id = $(this).parent().data('id');
        var images = $('#property_gallery').val().split(',').filter(val => val !== id);
        $('#property_gallery').val(images.join(','));
        $(this).parent().remove();
    });
});
