jQuery(document).ready(function ($) {
    const PropertyGallery = {
        frame: null,

        init: function () {
            this.bindEvents();
        },

        bindEvents: function () {
            $('#add-property-gallery').on('click', this.openMediaLibrary.bind(this));
            $(document).on('click', '.remove-image', this.removeImage);
        },

        openMediaLibrary: function (e) {
            e.preventDefault();

            if (this.frame) {
                this.frame.open();
                return;
            }

            this.frame = wp.media({
                title: 'Select Images',
                button: { text: 'Use Images' },
                multiple: true
            });

            this.frame.on('select', this.handleMediaSelect.bind(this));
            this.frame.open();
        },

        handleMediaSelect: function () {
            const images = this.frame.state().get('selection').map(attachment => attachment.toJSON().id);
            let existing = $('#property_gallery').val().split(',').filter(Boolean);
            existing = [...new Set(existing.concat(images))]; // Ensure unique values

            $('#property_gallery').val(existing.join(','));
            this.updateGalleryPreview(existing);
        },

        updateGalleryPreview: function (images) {
            const $galleryContainer = $('#property-gallery-container').empty();
            images.forEach(id => {
                const imageUrl = wp.media.attachment(id).get('url');
                $galleryContainer.append(`
                    <div class="gallery-image" data-id="${id}">
                        <img src="${imageUrl}" />
                        <button type="button" class="remove-image">×</button>
                    </div>
                `);
            });
        },

        removeImage: function () {
            const id = $(this).parent().data('id').toString();
            let images = $('#property_gallery').val().split(',').filter(Boolean);
            images = images.filter(val => val !== id);

            $('#property_gallery').val(images.join(','));
            $(this).parent().remove();
        }
    };

    // Initialize the module.
    PropertyGallery.init();
});
