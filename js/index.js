const { __ } = wp.i18n;

const GallerySelector = {
	windowProperties: {
		title: __( 'Select a thumbnail', 'wpmpw' ),
		multiple: false,
		library: {
			type: 'image',
		}
	},

	initControls(trigger, inputField) {
		// Create an array of values in the input.
		let selectedIDs = inputField.value.split(',').filter(Number).map(Number);
		// Create media selection window.
		const galleryFrame = wp.media(this.windowProperties);

		// Set up triggering window on clicking the link.
		trigger.addEventListener('click', (e) => {
			// Prevent link navigation just in case.
			e.preventDefault();
			// Trigger window opening.
			galleryFrame.open();
		});

		galleryFrame.on('select', (e) => {
			// Get attachment ids from the selected items.
			galleryFrame.state().get('selection').each((attachment) => {
				// Remove existing values if only single image is allowed.
				if (this.windowProperties.multiple === false) {
					selectedIDs = [];
				}
				// Check if the item is already selected.
				if (selectedIDs.includes(attachment.attributes.id)) {
					return;
				}
				// Push id into an array.
				selectedIDs.push(attachment.attributes.id);
			});
			// Assign new value to the input field.
			inputField.value = selectedIDs.join(',');
		});
	},
}

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.wp-media-select-image').forEach((trigger) => {
		GallerySelector.initControls(trigger, document.querySelector('#' + trigger.getAttribute('rel')));
	});
});
