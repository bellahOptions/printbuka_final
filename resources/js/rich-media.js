import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const MEDIA_URL = '/admin/media';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

/* ─── Rich text editor (Quill) — progressively enhances any
   `<textarea data-rich-editor>` in the admin portal into a WYSIWYG editor
   with image support, while keeping the original textarea (hidden, kept in
   sync on every change) as the real form field — no controller/validation
   changes needed beyond accepting HTML instead of plain text. ─── */
function openImagePicker(detail) {
    window.dispatchEvent(new CustomEvent('open-image-picker', { detail }));
}

function initRichEditors() {
    document.querySelectorAll('textarea[data-rich-editor]').forEach((textarea) => {
        if (textarea.dataset.richEditorInit) return;
        textarea.dataset.richEditorInit = 'true';

        const wasRequired = textarea.required;
        textarea.required = false;

        const wrapper = document.createElement('div');
        wrapper.className = 'pb-rich-editor';
        textarea.parentNode.insertBefore(wrapper, textarea);
        textarea.style.display = 'none';

        const quill = new Quill(wrapper, {
            theme: 'snow',
            placeholder: textarea.getAttribute('placeholder') || '',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [false, 2, 3] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link', 'image'],
                        ['clean'],
                    ],
                    handlers: {
                        image: () => {
                            openImagePicker({
                                onSelect: (url) => {
                                    const range = quill.getSelection(true) || { index: quill.getLength() };
                                    quill.insertEmbed(range.index, 'image', url, 'user');
                                    quill.setSelection(range.index + 1, 0);
                                },
                            });
                        },
                    },
                },
            },
        });

        if (textarea.value.trim()) {
            quill.clipboard.dangerouslyPasteHTML(textarea.value);
        }

        const syncToTextarea = () => {
            const html = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
            textarea.value = html;
        };

        quill.on('text-change', () => {
            syncToTextarea();
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        });

        const form = textarea.closest('form');
        if (form && wasRequired) {
            form.addEventListener('submit', (event) => {
                if (quill.getText().trim() !== '') return;
                event.preventDefault();
                event.stopImmediatePropagation();
                wrapper.classList.add('pb-rich-editor--invalid');
                wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, true);
        }
    });
}

/* ─── Shared Cloudinary image library / crop-on-upload picker.
   One modal per page (see admin._partials.image-picker-modal), driven
   entirely by the `open-image-picker` window event so any trigger — a plain
   "Choose Image" button, or the Quill image toolbar button above — can open
   it without knowing about each other. ─── */
function registerImageLibraryModal() {
    window.Alpine.data('imageLibraryModal', () => ({
        open: false,
        tab: 'library',
        images: [],
        nextCursor: null,
        loading: false,
        error: '',
        onSelect: null,
        targetSelector: null,
        previewSelector: null,
        cropper: null,
        uploading: false,
        hasFile: false,

        init() {
            window.addEventListener('open-image-picker', (event) => {
                this.launch(event.detail || {});
            });
        },

        launch(detail) {
            this.open = true;
            this.tab = 'library';
            this.error = '';
            this.onSelect = typeof detail.onSelect === 'function' ? detail.onSelect : null;
            this.targetSelector = detail.target || null;
            this.previewSelector = detail.preview || null;
            if (!this.images.length) this.loadImages();
        },

        close() {
            this.open = false;
            this.destroyCropper();
        },

        async loadImages(loadMore = false) {
            this.loading = true;
            this.error = '';
            try {
                const url = new URL(MEDIA_URL, window.location.origin);
                if (loadMore && this.nextCursor) url.searchParams.set('cursor', this.nextCursor);
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await response.json();
                if (!data.ok) { this.error = data.message || 'Could not load the image library.'; return; }
                this.images = loadMore ? [...this.images, ...data.images] : data.images;
                this.nextCursor = data.next_cursor;
            } catch (e) {
                this.error = 'Could not load the image library.';
            } finally {
                this.loading = false;
            }
        },

        selectImage(image) {
            this.applySelection(image.url, image.public_id);
            this.close();
        },

        applySelection(url, publicId) {
            if (this.onSelect) { this.onSelect(url, publicId); return; }
            if (this.targetSelector) {
                const input = document.querySelector(this.targetSelector);
                if (input) { input.value = url; input.dispatchEvent(new Event('change', { bubbles: true })); }
            }
            if (this.previewSelector) {
                const preview = document.querySelector(this.previewSelector);
                if (preview) { preview.src = url; preview.classList.remove('hidden'); }
            }
        },

        onFileChosen(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            this.hasFile = true;
            this.error = '';
            this.$nextTick(() => {
                const img = this.$refs.cropImage;
                img.src = URL.createObjectURL(file);
                img.onload = () => {
                    this.destroyCropper();
                    this.cropper = new Cropper(img, { viewMode: 1, autoCropArea: 1, background: false, responsive: true });
                };
            });
        },

        destroyCropper() {
            if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
            this.hasFile = false;
        },

        uploadCropped() {
            if (!this.cropper) return;
            this.uploading = true;
            this.error = '';
            this.cropper.getCroppedCanvas({ maxWidth: 2200, maxHeight: 2200 }).toBlob(async (blob) => {
                try {
                    const formData = new FormData();
                    formData.append('image', blob, 'upload.jpg');
                    const response = await fetch(MEDIA_URL, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-CSRF-TOKEN': csrfToken(), 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const data = await response.json();
                    if (!data.ok) { this.error = data.message || 'Upload failed.'; return; }
                    this.images = [{ public_id: data.public_id, url: data.url }, ...this.images];
                    this.applySelection(data.url, data.public_id);
                    this.close();
                } catch (e) {
                    this.error = 'Upload failed.';
                } finally {
                    this.uploading = false;
                }
            }, 'image/jpeg', 0.85);
        },
    }));
}

/* ─── Plain "Choose Image" trigger buttons (data-image-picker) ─── */
function wireImagePickerTriggers() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-image-picker]');
        if (!button) return;
        event.preventDefault();
        openImagePicker({
            target: button.dataset.imagePickerTarget || null,
            preview: button.dataset.imagePickerPreview || null,
        });
    });
}

document.addEventListener('alpine:init', registerImageLibraryModal);
document.addEventListener('DOMContentLoaded', () => {
    initRichEditors();
    wireImagePickerTriggers();
});
