document.addEventListener('DOMContentLoaded', function() {
    // Lightbox functionality
    const galleryThumbnails = document.querySelectorAll('.gallery-thumbnail');

    // Create lightbox elements if they don't exist
    // It's generally better to have these in your HTML from the start for better SEO and initial render.
    // However, if you insist on dynamic creation, this approach ensures they are available.
    let lightbox = document.getElementById('lightbox');
    let lightboxContent = document.getElementById('lightbox-content');
    let lightboxCaption = document.getElementById('lightbox-caption');
    let closeButton = document.querySelector('.lightbox-close');

    // If lightbox elements don't exist in the HTML, create them dynamically
    if (!lightbox) {
        lightbox = document.createElement('div');
        lightbox.id = 'lightbox';
        lightbox.classList.add('lightbox'); // Add the CSS class
        document.body.appendChild(lightbox); // Append to body

        closeButton = document.createElement('span');
        closeButton.classList.add('lightbox-close');
        closeButton.innerHTML = '&times;'; // 'x' character
        lightbox.appendChild(closeButton);

        lightboxContent = document.createElement('img');
        lightboxContent.id = 'lightbox-content';
        lightboxContent.classList.add('lightbox-content'); // Add the CSS class
        lightbox.appendChild(lightboxContent);

        lightboxCaption = document.createElement('div');
        lightboxCaption.id = 'lightbox-caption';
        lightboxCaption.classList.add('lightbox-caption'); // Add the CSS class
        lightbox.appendChild(lightboxCaption);
    }

    // Attach event listeners ONLY if the lightbox elements are available (either pre-existing or just created)
    if (lightbox && lightboxContent && lightboxCaption && closeButton) {
        // Close lightbox when clicking the close button
        closeButton.addEventListener('click', function() {
            lightbox.style.display = 'none';
        });

        // Close lightbox when clicking outside the image
        lightbox.addEventListener('click', function(e) {
            // Check if the click occurred directly on the lightbox background, not its children
            if (e.target === lightbox) {
                lightbox.style.display = 'none';
            }
        });

        // Close lightbox with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && lightbox.style.display === 'flex') {
                lightbox.style.display = 'none';
            }
        });
    } else {
        console.error("Lightbox elements not found or failed to create. Lightbox functionality may not work.");
    }


    // Event listeners for gallery thumbnails
    galleryThumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const fullImageSrc = this.getAttribute('data-full-image');
            const altText = this.getAttribute('alt');

            if (lightboxContent && lightboxCaption && lightbox) { // Ensure elements exist before trying to use them
                lightboxContent.src = fullImageSrc;
                lightboxCaption.textContent = altText;
                lightbox.style.display = 'flex'; // Use flex to center the content
            } else {
                console.error("Attempted to open lightbox but lightbox elements are missing.");
            }
        });
    });
});