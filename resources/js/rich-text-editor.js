import tinymce from 'tinymce';
import 'tinymce/icons/default/icons.min.js';
import 'tinymce/themes/silver/theme.min.js';
import 'tinymce/models/dom/model.min.js';
import 'tinymce/skins/ui/oxide/skin.js';
import 'tinymce/skins/ui/oxide/content.js';
import 'tinymce/skins/content/default/content.js';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/code';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/wordcount';

export const initializeRichTextEditors = (editorElements) => {
    const editorForms = new Set(
        Array.from(editorElements)
            .map((editor) => editor.closest('form'))
            .filter(Boolean),
    );

    editorForms.forEach((form) => {
        form.addEventListener('submit', () => tinymce.triggerSave());
    });

    return tinymce.init({
        selector: '[data-rich-text-editor]',
        license_key: 'gpl',
        plugins: 'advlist autolink code link lists table wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link blockquote | table | removeformat code',
        toolbar_mode: 'sliding',
        menubar: false,
        branding: false,
        promotion: false,
        browser_spellcheck: true,
        contextmenu: false,
        height: 360,
        min_height: 300,
        resize: true,
        skin_url: 'default',
        content_css: 'default',
        content_style: `
            body {
                color: #111827;
                font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
                font-size: 14px;
                line-height: 1.7;
            }

            a { color: #6366f1; }
            blockquote { border-left-color: #6366f1 !important; color: #4b5563; }
        `,
        setup: (editor) => {
            editor.on('change input undo redo', () => editor.save());
        },
    });
};
