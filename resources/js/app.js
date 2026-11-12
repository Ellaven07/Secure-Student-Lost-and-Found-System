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

const toastRoot = document.getElementById('uiToastRoot');
const modalRoot = document.getElementById('uiModalRoot');

function showUiToast(message, kind = 'success') {
    if (!toastRoot || !message) {
        return;
    }

    const toast = document.createElement('div');
    const icon = document.createElement('span');
    const text = document.createElement('p');
    const closeButton = document.createElement('button');

    toast.className = `ui-toast ui-toast--${kind}`;
    toast.setAttribute('role', kind === 'error' ? 'alert' : 'status');
    icon.className = 'ui-toast__icon';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = kind === 'error' ? '!' : '✓';
    text.className = 'ui-toast__message';
    text.textContent = message;
    closeButton.className = 'ui-toast__close';
    closeButton.type = 'button';
    closeButton.setAttribute('aria-label', 'Dismiss notification');
    closeButton.textContent = '×';
    closeButton.addEventListener('click', () => toast.remove());

    toast.append(icon, text, closeButton);
    toastRoot.append(toast);
    requestAnimationFrame(() => toast.classList.add('is-visible'));

    window.setTimeout(() => {
        toast.classList.remove('is-visible');
        window.setTimeout(() => toast.remove(), 250);
    }, 6000);
}

document.querySelectorAll('[data-ui-toast]').forEach((notice) => {
    showUiToast(notice.textContent.trim(), notice.dataset.kind);
});

function closeUiConfirm(overlay, previousFocus) {
    overlay.remove();
    modalRoot?.replaceChildren();
    previousFocus?.focus();
}

function showUiConfirm(form, submitter) {
    if (!modalRoot) {
        if (window.confirm(form.dataset.confirm || 'Continue with this action?')) {
            form.dataset.uiConfirmed = 'true';
            submitter ? form.requestSubmit(submitter) : form.requestSubmit();
        }

        return;
    }

    const previousFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;
    const overlay = document.createElement('div');
    const dialog = document.createElement('section');
    const heading = document.createElement('h2');
    const message = document.createElement('p');
    const actions = document.createElement('div');
    const cancelButton = document.createElement('button');
    const confirmButton = document.createElement('button');

    overlay.className = 'ui-confirm-overlay';
    dialog.className = 'ui-confirm-dialog';
    dialog.setAttribute('role', 'alertdialog');
    dialog.setAttribute('aria-modal', 'true');
    heading.className = 'ui-confirm-dialog__title';
    heading.id = 'uiConfirmTitle';
    heading.textContent = form.dataset.confirmTitle || 'Please confirm';
    dialog.setAttribute('aria-labelledby', heading.id);
    message.className = 'ui-confirm-dialog__message';
    message.textContent = form.dataset.confirm || 'Continue with this action?';
    actions.className = 'ui-confirm-dialog__actions';
    cancelButton.type = 'button';
    cancelButton.className = 'ui-confirm-dialog__cancel';
    cancelButton.textContent = 'Cancel';
    confirmButton.type = 'button';
    confirmButton.className = form.dataset.confirmTone === 'danger'
        ? 'ui-confirm-dialog__accept ui-confirm-dialog__accept--danger'
        : 'ui-confirm-dialog__accept';
    confirmButton.textContent = form.dataset.confirmLabel || 'Continue';

    cancelButton.addEventListener('click', () => closeUiConfirm(overlay, previousFocus));
    confirmButton.addEventListener('click', () => {
        form.dataset.uiConfirmed = 'true';
        closeUiConfirm(overlay, previousFocus);
        submitter ? form.requestSubmit(submitter) : form.requestSubmit();
    });
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) {
            closeUiConfirm(overlay, previousFocus);
        }
    });

    actions.append(cancelButton, confirmButton);
    dialog.append(heading, message, actions);
    overlay.append(dialog);
    modalRoot.replaceChildren(overlay);
    cancelButton.focus();
}

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) {
        return;
    }

    if (form.dataset.uiConfirmed === 'true') {
        delete form.dataset.uiConfirmed;

        return;
    }

    event.preventDefault();
    event.stopImmediatePropagation();
    showUiConfirm(form, event.submitter);
}, true);

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || !modalRoot?.firstElementChild) {
        return;
    }

    const cancelButton = modalRoot.querySelector('.ui-confirm-dialog__cancel');
    cancelButton?.click();
});
