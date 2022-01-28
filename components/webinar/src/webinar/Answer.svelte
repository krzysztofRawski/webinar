<script>
	// Import libraries
    import {fade} from 'svelte/transition'

    // Import data
    import appData from '../stores/appData.js';
    import currentUserData from '../stores/currentUserData.js'

    // Props
    export let answer;

    // Variables
    $: currentUserId = $currentUserData.userId;
    let questionUserId = answer.user_id;
    let myAnswer = false;

    // Logic
    $: if(currentUserId == questionUserId) {
        myAnswer = true;
    }
</script>

<div class="card" class:my-answer={myAnswer} in:fade>
    <header>
        <div>
            {$appData.translations.addDate}: {answer.question_reply_time}
        </div>
        {#if myAnswer}
            <div>
                {$appData.translations.myAnswer}
            </div>
        {/if}
    </header>
    <div class="content">
        <div class="question">
            {@html answer.question_content}
        </div>
        <div class="answer">
            {@html answer.question_reply}
        </div>
    </div>
</div>

<style>
    .question {
        font-size: 0.8rem;
        margin-bottom: 0.8rem;
        border-left: 2px solid lightgrey;
        padding-left: 1rem;
    }
    .my-answer {
        background-color: rgba(223,239,255,0.2) !important;
    }
    .card:hover {
        box-shadow: 1px 2px 4px rgba(0,0,0,0.2);
    }
</style>