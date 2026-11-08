document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.getElementById('galleryLightbox');
    const lightboxImage = document.getElementById('galleryLightboxImage');
    const lightboxCaption = document.getElementById('galleryLightboxCaption');
    const closeButton = document.getElementById('galleryLightboxClose');

    if (!lightbox || !lightboxImage || !lightboxCaption || !closeButton) {
        return;
    }

    document.querySelectorAll('.gallery-photo-card').forEach((photoButton) => {
        photoButton.addEventListener('click', () => {
            lightboxImage.src = photoButton.dataset.fullSrc;
            lightboxImage.alt = photoButton.dataset.photoAlt;
            lightboxCaption.textContent = photoButton.dataset.photoCaption;
            lightbox.showModal();
        });
    });

    closeButton.addEventListener('click', () => lightbox.close());

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            lightbox.close();
        }
    });
});