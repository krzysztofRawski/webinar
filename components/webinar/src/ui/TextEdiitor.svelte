<script>
	// Import libraries
    import appData from '../stores/appData.js';

    // Props
    export let disableEditor; 
 
    // Variables   
    let editorLoaded = false;

    // Logic
    function loadEditor() {
        ClassicEditor
        .create( document.querySelector( '#editor' ), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList'],
            heading: {
                options: [
                    { model: 'paragraph', title: $appData.translations.paragraph, class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: $appData.translations.header + ' 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: $appData.translations.header + ' 2', class: 'ck-heading_heading2' }
                ]
            }
        } )
        .then( newEditor => {
            window.editor = newEditor;
            editorLoaded = true;
        } )
        .catch( error => {
            console.error( error );
        } );
    }

    $: if(editorLoaded) {
        editor.isReadOnly = disableEditor;
        clearEditorData();
    }

    function clearEditorData() {
        editor.setData('');
    }

</script>

<div class="text-editor" use:loadEditor>
    <textarea id="editor" ></textarea>
    <div class="buttons">
        <slot></slot>
    <button class='bcim-button bcim-button-alt' on:click={clearEditorData} disabled={disableEditor}>{$appData.translations.clear}</button> 
    </div>
</div>

<style>
    .buttons{
        display: flex;
        justify-content: flex-end;
        margin-top: 0.5rem;
    }
    button {
        margin-left: 0.5rem;
    }
</style>