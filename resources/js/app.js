const richTextEditors = document.querySelectorAll('[data-rich-text-editor]');

if (richTextEditors.length > 0) {
    import('./rich-text-editor.js').then(({ initializeRichTextEditors }) => {
        initializeRichTextEditors(richTextEditors);
    });
}

const adminSidebar = document.querySelector('[data-admin-sidebar]');

if (adminSidebar) {
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const openButton = document.querySelector('[data-sidebar-open]');
    const closeButton = document.querySelector('[data-sidebar-close]');

    const openSidebar = () => {
        adminSidebar.classList.remove('-translate-x-full');
        adminSidebar.classList.add('translate-x-0');
        overlay?.classList.remove('hidden');
        openButton?.setAttribute('aria-expanded', 'true');
    };

    const closeSidebar = () => {
        adminSidebar.classList.add('-translate-x-full');
        adminSidebar.classList.remove('translate-x-0');
        overlay?.classList.add('hidden');
        openButton?.setAttribute('aria-expanded', 'false');
    };

    openButton?.addEventListener('click', openSidebar);
    closeButton?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
}

const bannerCropper = document.querySelector('[data-banner-cropper]');

if (bannerCropper) {
    const aspectRatio = 16 / 9;
    const minimumSelectionWidth = 80;
    const input = bannerCropper.querySelector('[data-banner-input]');
    const dialog = bannerCropper.querySelector('[data-banner-dialog]');
    const stage = bannerCropper.querySelector('[data-banner-stage]');
    const image = bannerCropper.querySelector('[data-banner-image]');
    const selection = bannerCropper.querySelector('[data-banner-selection]');
    const applyButton = bannerCropper.querySelector('[data-banner-apply]');
    const cancelButtons = bannerCropper.querySelectorAll('[data-banner-cancel]');
    const preview = bannerCropper.querySelector('[data-banner-preview]');
    const previewImage = bannerCropper.querySelector('[data-banner-preview-image]');
    const cropAgainButton = bannerCropper.querySelector('[data-banner-crop-again]');
    const status = bannerCropper.querySelector('[data-banner-status]');
    const error = bannerCropper.querySelector('[data-banner-error]');
    const fileError = bannerCropper.querySelector('[data-banner-file-error]');
    const removeBanner = bannerCropper.querySelector('[data-remove-banner]');

    const state = {
        sourceFile: null,
        pendingFile: null,
        croppedFile: null,
        sourceUrl: null,
        previewUrl: null,
        naturalWidth: 0,
        naturalHeight: 0,
        crop: { x: 0, y: 0, width: 0, height: 0 },
        interaction: null,
        pointerId: null,
        startPoint: null,
        startCrop: null,
        resizeAnchor: null,
        resizeDirection: null,
    };

    const revokeUrl = (key) => {
        if (state[key]) {
            URL.revokeObjectURL(state[key]);
            state[key] = null;
        }
    };

    const assignInputFile = (file) => {
        if (!file) {
            input.value = '';

            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
    };

    const stageSize = () => {
        const bounds = stage.getBoundingClientRect();

        return {
            width: bounds.width,
            height: bounds.height,
        };
    };

    const stagePoint = (event) => {
        const bounds = stage.getBoundingClientRect();

        return {
            x: Math.min(bounds.width, Math.max(0, event.clientX - bounds.left)),
            y: Math.min(bounds.height, Math.max(0, event.clientY - bounds.top)),
        };
    };

    const renderSelection = () => {
        selection.style.left = `${state.crop.x}px`;
        selection.style.top = `${state.crop.y}px`;
        selection.style.width = `${state.crop.width}px`;
        selection.style.height = `${state.crop.height}px`;
    };

    const resetSelection = () => {
        const size = stageSize();
        const padding = Math.min(40, size.width * 0.08, size.height * 0.08);
        const availableWidth = Math.max(0, size.width - padding * 2);
        const availableHeight = Math.max(0, size.height - padding * 2);
        let width = availableWidth;
        let height = width / aspectRatio;

        if (height > availableHeight) {
            height = availableHeight;
            width = height * aspectRatio;
        }

        state.crop = {
            x: (size.width - width) / 2,
            y: (size.height - height) / 2,
            width,
            height,
        };

        renderSelection();
    };

    const selectionFromAnchor = (anchor, point, direction) => {
        const size = stageSize();
        const rawWidth = Math.abs(point.x - anchor.x);
        const rawHeight = Math.abs(point.y - anchor.y);
        const maximumWidth = direction.x > 0 ? size.width - anchor.x : anchor.x;
        const maximumHeight = direction.y > 0 ? size.height - anchor.y : anchor.y;
        let width = Math.max(rawWidth, rawHeight * aspectRatio);

        width = Math.min(width, maximumWidth, maximumHeight * aspectRatio);

        const height = width / aspectRatio;

        return {
            x: direction.x > 0 ? anchor.x : anchor.x - width,
            y: direction.y > 0 ? anchor.y : anchor.y - height,
            width,
            height,
        };
    };

    const resizeConfiguration = (handle) => {
        const crop = state.crop;

        return {
            nw: {
                anchor: { x: crop.x + crop.width, y: crop.y + crop.height },
                direction: { x: -1, y: -1 },
            },
            ne: {
                anchor: { x: crop.x, y: crop.y + crop.height },
                direction: { x: 1, y: -1 },
            },
            se: {
                anchor: { x: crop.x, y: crop.y },
                direction: { x: 1, y: 1 },
            },
            sw: {
                anchor: { x: crop.x + crop.width, y: crop.y },
                direction: { x: -1, y: 1 },
            },
        }[handle];
    };

    const restoreCommittedFile = () => {
        assignInputFile(state.croppedFile);
        state.pendingFile = null;
    };

    const openCropper = (file) => {
        if (!file || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            fileError.textContent = 'Choose a JPG, PNG, or WebP image.';
            fileError.classList.remove('hidden');
            restoreCommittedFile();

            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            fileError.textContent = 'The image must not be larger than 5 MB.';
            fileError.classList.remove('hidden');
            restoreCommittedFile();

            return;
        }

        fileError.classList.add('hidden');
        error.classList.add('hidden');
        state.pendingFile = file;
        revokeUrl('sourceUrl');
        state.sourceUrl = URL.createObjectURL(file);
        applyButton.disabled = true;

        image.onload = () => {
            state.naturalWidth = image.naturalWidth;
            state.naturalHeight = image.naturalHeight;
            applyButton.disabled = false;
            requestAnimationFrame(() => requestAnimationFrame(resetSelection));
        };
        image.onerror = () => {
            error.textContent = 'The selected image could not be opened.';
            error.classList.remove('hidden');
        };
        image.src = state.sourceUrl;

        if (!dialog.open) {
            dialog.showModal();
        }
    };

    const closeWithoutApplying = () => {
        restoreCommittedFile();

        if (dialog.open) {
            dialog.close('cancel');
        }
    };

    const makeCroppedFile = async () => {
        const size = stageSize();
        const scaleX = state.naturalWidth / size.width;
        const scaleY = state.naturalHeight / size.height;
        const canvas = document.createElement('canvas');

        canvas.width = 1600;
        canvas.height = 900;

        const context = canvas.getContext('2d');
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(
            image,
            state.crop.x * scaleX,
            state.crop.y * scaleY,
            state.crop.width * scaleX,
            state.crop.height * scaleY,
            0,
            0,
            canvas.width,
            canvas.height,
        );

        const blob = await new Promise((resolve) => {
            canvas.toBlob(resolve, 'image/jpeg', 0.9);
        });

        if (!blob) {
            throw new Error('The cropped image could not be created.');
        }

        const baseName = state.pendingFile.name.replace(/\.[^.]+$/, '') || 'category-banner';

        return new File([blob], `${baseName}-16x9.jpg`, {
            type: 'image/jpeg',
            lastModified: Date.now(),
        });
    };

    input.addEventListener('change', () => {
        const [file] = input.files;

        if (file) {
            openCropper(file);
        }
    });

    cropAgainButton?.addEventListener('click', () => {
        if (state.sourceFile) {
            openCropper(state.sourceFile);
        }
    });

    stage.addEventListener('pointerdown', (event) => {
        const point = stagePoint(event);
        const handle = event.target.closest('[data-banner-handle]')?.dataset.bannerHandle;

        state.pointerId = event.pointerId;
        state.startPoint = point;
        state.startCrop = { ...state.crop };

        if (handle) {
            const configuration = resizeConfiguration(handle);

            state.interaction = 'resize';
            state.resizeAnchor = configuration.anchor;
            state.resizeDirection = configuration.direction;
        } else if (event.target.closest('[data-banner-selection]')) {
            state.interaction = 'move';
        } else {
            state.interaction = 'draw';
            state.resizeAnchor = point;
            state.resizeDirection = { x: 1, y: 1 };
            state.crop = { x: point.x, y: point.y, width: 0, height: 0 };
            renderSelection();
        }

        stage.setPointerCapture(event.pointerId);
        event.preventDefault();
    });

    stage.addEventListener('pointermove', (event) => {
        if (!state.interaction || event.pointerId !== state.pointerId) {
            return;
        }

        const point = stagePoint(event);

        if (state.interaction === 'move') {
            const size = stageSize();
            const deltaX = point.x - state.startPoint.x;
            const deltaY = point.y - state.startPoint.y;

            state.crop.x = Math.min(
                size.width - state.crop.width,
                Math.max(0, state.startCrop.x + deltaX),
            );
            state.crop.y = Math.min(
                size.height - state.crop.height,
                Math.max(0, state.startCrop.y + deltaY),
            );
        } else if (state.interaction === 'draw') {
            const direction = {
                x: point.x < state.resizeAnchor.x ? -1 : 1,
                y: point.y < state.resizeAnchor.y ? -1 : 1,
            };

            state.crop = selectionFromAnchor(state.resizeAnchor, point, direction);
        } else {
            state.crop = selectionFromAnchor(
                state.resizeAnchor,
                point,
                state.resizeDirection,
            );
        }

        renderSelection();
    });

    const stopInteraction = (event) => {
        if (event.pointerId !== state.pointerId) {
            return;
        }

        if (state.crop.width < minimumSelectionWidth) {
            resetSelection();
        }

        state.interaction = null;
        state.pointerId = null;
    };

    stage.addEventListener('pointerup', stopInteraction);
    stage.addEventListener('pointercancel', stopInteraction);

    applyButton.addEventListener('click', async () => {
        applyButton.disabled = true;
        applyButton.textContent = 'Applying…';
        error.classList.add('hidden');

        try {
            const croppedFile = await makeCroppedFile();

            state.sourceFile = state.pendingFile;
            state.croppedFile = croppedFile;
            assignInputFile(croppedFile);
            revokeUrl('previewUrl');
            state.previewUrl = URL.createObjectURL(croppedFile);
            previewImage.src = state.previewUrl;
            preview.classList.remove('hidden');
            cropAgainButton?.classList.remove('hidden');
            status.textContent = 'Cropped to 16:9 · 1600 × 900 px';

            if (removeBanner) {
                removeBanner.checked = false;
            }

            state.pendingFile = null;
            dialog.close('apply');
        } catch (cropError) {
            error.textContent = cropError.message;
            error.classList.remove('hidden');
        } finally {
            applyButton.disabled = false;
            applyButton.textContent = 'Apply crop';
        }
    });

    cancelButtons.forEach((button) => {
        button.addEventListener('click', closeWithoutApplying);
    });

    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeWithoutApplying();
    });

    window.addEventListener('beforeunload', () => {
        revokeUrl('sourceUrl');
        revokeUrl('previewUrl');
    });
}
