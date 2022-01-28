<script>
	// Import libraries
    import {fade} from 'svelte/transition';

    // Props
    export let success = false;
    export let message = ''
    
    // Variables
    let icon;

    // Logic
    if(success) {
        icon = 'thumb_up'
    } else {
        icon = 'warning'
    }

</script>

<div class="message" transition:fade class:success={success}>
    <span class="material-icons">{icon}</span><span>{message}</span>
</div>

<style>
    .message {
        background-color: rgb(255, 245, 160);
        border: 1px solid coral;
        color: orangered;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
        display: flex;
        align-items: center;
    }
    .message span {
        display: inline-block;
    }
    .message .material-icons {
        margin-right: 1rem;
    }
    .success {
        background-color: rgb(206, 255, 160);
        border: 1px solid green;
        color: green;
    }
</style>