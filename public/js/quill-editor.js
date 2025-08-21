// Quill Editor Configuration and Functions
class QuillEditorManager {
    constructor(editorId, hiddenInputId, initialContent = '') {
        this.editorId = editorId;
        this.hiddenInputId = hiddenInputId;
        this.initialContent = initialContent;
        this.quill = null;
        this.init();
    }

    init() {
        try {
            // Check if Quill is already initialized on this element
            const existingQuill = Quill.find(document.querySelector(this.editorId));
            if (existingQuill) {
                console.warn('Quill editor already exists on this element, removing duplicate');
                existingQuill.destroy();
            }

            // Initialize Quill editor
            this.quill = new Quill(this.editorId, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                placeholder: 'Enter event description...'
            });

            // Set initial content if provided
            if (this.initialContent) {
                this.quill.root.innerHTML = this.initialContent;
            }

            // Set up form submission handler
            this.setupFormHandler();
            
            console.log('Quill editor initialized successfully');
        } catch (error) {
            console.error('Failed to initialize Quill editor:', error);
            this.fallbackToTextarea();
        }
    }

    fallbackToTextarea() {
        console.log('Falling back to textarea');
        const editorContainer = document.querySelector(this.editorId);
        if (editorContainer) {
            // Create a textarea as fallback
            const textarea = document.createElement('textarea');
            textarea.id = 'quill-fallback-textarea';
            textarea.name = 'description';
            textarea.placeholder = 'Enter event description...';
            textarea.style.width = '100%';
            textarea.style.minHeight = '200px';
            textarea.style.padding = '15px';
            textarea.style.border = '1px solid #dcdcdc';
            textarea.style.borderRadius = '6px';
            textarea.style.fontFamily = 'inherit';
            textarea.style.fontSize = '16px';
            textarea.style.resize = 'vertical';
            
            // Set initial content
            if (this.initialContent) {
                textarea.value = this.initialContent;
            }
            
            // Replace the editor container with textarea
            editorContainer.parentNode.replaceChild(textarea, editorContainer);
            
            // Update hidden input
            const hiddenInput = document.getElementById(this.hiddenInputId);
            if (hiddenInput) {
                hiddenInput.value = this.initialContent || '';
            }
        }
    }

    setupFormHandler() {
        const form = document.querySelector('form');
        if (form) {
            // Remove any existing listeners to prevent duplicates
            form.removeEventListener('submit', this.handleSubmit);
            form.addEventListener('submit', this.handleSubmit.bind(this));
            
            // Also update on input change for real-time updates
            if (this.quill) {
                this.quill.on('text-change', () => {
                    this.updateHiddenInput();
                });
            }
        }
    }

    handleSubmit(event) {
        console.log('Form submission detected, updating hidden input...');
        this.updateHiddenInput();
        
        // Log the content being submitted
        const hiddenInput = document.getElementById(this.hiddenInputId);
        if (hiddenInput) {
            console.log('Content being submitted:', hiddenInput.value);
        }
    }

    updateHiddenInput() {
        const hiddenInput = document.getElementById(this.hiddenInputId);
        if (hiddenInput && this.quill) {
            const content = this.quill.root.innerHTML;
            hiddenInput.value = content;
            console.log('Updated hidden input with content:', content);
        }
    }

    getContent() {
        return this.quill ? this.quill.root.innerHTML : '';
    }

    setContent(content) {
        if (this.quill) {
            this.quill.root.innerHTML = content;
        }
    }

    destroy() {
        if (this.quill) {
            this.quill.destroy();
            this.quill = null;
        }
    }
}

// Global variable to track if Quill is already initialized
let quillInitialized = false;

// Initialize Quill editors when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Quill editor...');
    
    // Wait a bit to ensure all elements are ready
    setTimeout(() => {
        // Check if we're on the add event form
        const addFormEditor = document.getElementById('quill-editor');
        if (addFormEditor) {
            const hiddenInput = document.getElementById('description');
            let initialContent = '';
            
            // Check if there's old input (validation errors)
            if (hiddenInput && hiddenInput.value) {
                initialContent = hiddenInput.value;
            }
            
            new QuillEditorManager('#quill-editor', 'description', initialContent);
            quillInitialized = true;
            console.log('Quill editor initialized on add form');
        }

        // Check if we're on the edit event form
        const editFormEditor = document.getElementById('quill-editor');
        if (editFormEditor && !quillInitialized) {
            const hiddenInput = document.getElementById('description');
            let initialContent = '';
            
            // Check if there's old input (validation errors) or existing content
            if (hiddenInput && hiddenInput.value) {
                initialContent = hiddenInput.value;
            }
            
            new QuillEditorManager('#quill-editor', 'description', initialContent);
            quillInitialized = true;
            console.log('Quill editor initialized on edit form');
        }
    }, 100);
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    quillInitialized = false;
});
