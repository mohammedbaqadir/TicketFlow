import Editor from "@toast-ui/editor";
import colorSyntaxPlugin from '@toast-ui/editor-plugin-color-syntax';
import codeSyntaxHighlightPlugin from '@toast-ui/editor-plugin-code-syntax-highlight';
import Prism from 'prismjs';
import '@toast-ui/editor/dist/i18n/ar';
import DOMPurify from 'dompurify';

// Define a global variable to store editor instances by ID
window.toastUIEditors = {};

// A helper function to log errors in a standardized way
const logError = (error, contextMessage) => {
  console.error(`[Toast UI Editor Error] ${contextMessage}`, error);
};

window.initializeToastUIEditor = (mode, id, content, lang) => {
  try {
    const element = document.querySelector(`#${id}`);
    if (!element) {
      throw new Error(`Element with ID '${id}' not found.`);
    }

    const commonOptions = {
      el: element,
      height: '500px',
      initialValue: content,
      language: lang === 'ar' ? 'ar' : 'en', // Set language dynamically
      plugins: [
        [codeSyntaxHighlightPlugin, {highlighter: Prism}],
        colorSyntaxPlugin
      ],
    };

    if (mode === 'editor') {
      // Editor-specific configuration
      commonOptions.previewStyle = 'tab';
      commonOptions.placeholder = 'Please enter text.';
      commonOptions.useCommandShortcut = false;
      commonOptions.customHTMLRenderer = {
        // Disable HTML rendering in the editor
        html: () => ''
      };
      window.toastUIEditors[id] = new Editor(commonOptions);

    } else if (mode === 'viewer') {
      // Viewer-specific configuration
      commonOptions.viewer = true;
      commonOptions.customHTMLRenderer = {
        html (node) {
          const sanitizedHTML = DOMPurify.sanitize(node.literal);
          return {type: 'html', content: sanitizedHTML};
        }
      };
      window.toastUIEditors[id] = Editor.factory(commonOptions);

    } else {
      throw new Error(`Invalid mode specified: '${mode}'. Use 'editor' or 'viewer'.`);
    }

  } catch (error) {
    logError(error, `Error during the initialization process for element ID: ${id}`);
  }
};