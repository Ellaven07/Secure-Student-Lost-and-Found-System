//

const profilePhotoLimit = 2048 * 1024;
const compressedPhotoTarget = 1900 * 1024;

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const photoInput = form.querySelector('[data-compress-profile-photo]');

    if (!photoInput?.files?.length) {
        return;
    }

    if (form.dataset.photoPrepared === 'true') {
        delete form.dataset.photoPrepared;

        return;
    }

    const selectedPhoto = photoInput.files[0];

    if (selectedPhoto.size <= profilePhotoLimit) {
        return;
    }

    event.preventDefault();

    const submitButton = event.submitter;
    const message = form.querySelector('[data-photo-compression-message]');
    const originalButtonText = submitButton?.textContent;

    if (submitButton instanceof HTMLButtonElement) {
        submitButton.disabled = true;
        submitButton.textContent = 'Preparing photo...';
    }

    try {
        const compressedPhoto = await compressProfilePhoto(selectedPhoto);
        const transfer = new DataTransfer();

        transfer.items.add(compressedPhoto);
        photoInput.files = transfer.files;

        if (message) {
            message.textContent = `Photo optimized to ${(compressedPhoto.size / 1024).toFixed(0)} KB.`;
            message.classList.remove('text-red-600');
        }

        form.dataset.photoPrepared = 'true';

        if (submitButton instanceof HTMLButtonElement) {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }

        if (submitButton instanceof HTMLElement) {
            form.requestSubmit(submitButton);
        } else {
            form.requestSubmit();
        }
    } catch {
        if (message) {
            message.textContent = 'This photo could not be optimized. Please choose a JPG, PNG, or WEBP photo.';
            message.classList.add('text-red-600');
        }

        if (submitButton instanceof HTMLButtonElement) {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    }
});

async function compressProfilePhoto(photo) {
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(photo.type)) {
        throw new Error('Unsupported image format.');
    }

    const image = await createImageBitmap(photo);
    const canvas = document.createElement('canvas');
    const scale = Math.min(1, 1920 / Math.max(image.width, image.height));

    canvas.width = Math.round(image.width * scale);
    canvas.height = Math.round(image.height * scale);

    const context = canvas.getContext('2d');

    if (!context) {
        image.close();
        throw new Error('Unable to process this photo.');
    }

    context.drawImage(image, 0, 0, canvas.width, canvas.height);
    image.close();

    for (const quality of [0.86, 0.76, 0.66, 0.56, 0.46]) {
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));

        if (blob && blob.size <= compressedPhotoTarget) {
            const filename = photo.name.replace(/\.[^.]+$/, '') || 'profile-photo';

            return new File([blob], `${filename}.jpg`, {
                type: 'image/jpeg',
                lastModified: Date.now(),
            });
        }
    }

    throw new Error('Photo could not be reduced below the upload limit.');
}
