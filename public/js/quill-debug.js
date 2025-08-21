// Quill Debug Script - Remove this file after fixing the duplicate toolbar issue
console.log('=== QUILL DEBUG START ===');

// Check if Quill library is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for Quill instances...');
    
    // Check if Quill library is available
    if (typeof Quill === 'undefined') {
        console.error('Quill library is not loaded!');
        console.log('Available global objects:', Object.keys(window).filter(key => key.toLowerCase().includes('quill')));
        return;
    } else {
        console.log('Quill library is loaded successfully');
    }
    
    // Check for the editor container
    const editorContainer = document.getElementById('quill-editor');
    if (editorContainer) {
        console.log('Editor container found:', editorContainer);
        console.log('Editor container styles:', window.getComputedStyle(editorContainer));
        console.log('Editor container display:', window.getComputedStyle(editorContainer).display);
        console.log('Editor container visibility:', window.getComputedStyle(editorContainer).visibility);
    } else {
        console.error('Editor container not found!');
    }
    
    // Check for multiple toolbar elements
    const toolbars = document.querySelectorAll('.ql-toolbar');
    console.log('Found toolbars:', toolbars.length);
    
    if (toolbars.length > 1) {
        console.warn('DUPLICATE TOOLBARS DETECTED!');
        toolbars.forEach((toolbar, index) => {
            console.log(`Toolbar ${index + 1}:`, toolbar);
            console.log(`Toolbar ${index + 1} parent:`, toolbar.parentElement);
        });
        
        // Hide duplicate toolbars
        toolbars.forEach((toolbar, index) => {
            if (index > 0) {
                console.log(`Hiding duplicate toolbar ${index + 1}`);
                toolbar.style.display = 'none';
            }
        });
    }
    
    // Check for multiple editor containers
    const editors = document.querySelectorAll('[id*="quill-editor"]');
    console.log('Found editor containers:', editors.length);
    
    if (editors.length > 1) {
        console.warn('DUPLICATE EDITOR CONTAINERS DETECTED!');
        editors.forEach((editor, index) => {
            console.log(`Editor ${index + 1}:`, editor);
        });
    }
    
    // Check for multiple Quill instances
    if (typeof Quill !== 'undefined') {
        const quillInstances = Quill.instances;
        console.log('Quill instances:', quillInstances);
        
        if (quillInstances && quillInstances.length > 1) {
            console.warn('MULTIPLE QUILL INSTANCES DETECTED!');
            quillInstances.forEach((instance, index) => {
                console.log(`Instance ${index + 1}:`, instance);
            });
        }
    }
    
    // Check for multiple script tags
    const quillScripts = document.querySelectorAll('script[src*="quill"]');
    console.log('Quill script tags found:', quillScripts.length);
    
    // Check for multiple CSS links
    const quillCSS = document.querySelectorAll('link[href*="quill"]');
    console.log('Quill CSS links found:', quillCSS.length);
    
    // Check for hidden input
    const hiddenInput = document.getElementById('description');
    if (hiddenInput) {
        console.log('Hidden input found:', hiddenInput);
        console.log('Hidden input value:', hiddenInput.value);
    } else {
        console.error('Hidden input not found!');
    }
    
    // Check if there are any CSS rules hiding elements
    setTimeout(() => {
        const editorContainer = document.getElementById('quill-editor');
        if (editorContainer) {
            const computedStyle = window.getComputedStyle(editorContainer);
            console.log('Final editor container styles:');
            console.log('- display:', computedStyle.display);
            console.log('- visibility:', computedStyle.visibility);
            console.log('- height:', computedStyle.height);
            console.log('- width:', computedStyle.width);
            console.log('- opacity:', computedStyle.opacity);
        }
    }, 500);
});

console.log('=== QUILL DEBUG END ===');
