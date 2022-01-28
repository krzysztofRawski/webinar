<script>
	// Import libraries
    import {fade} from 'svelte/transition'

    // Import data
    import stateData from '../stores/stateData.js';
    import questionsStore from '../stores/questions.js';
    import appData from '../stores/appData.js';

    // Props
    export let mini = false;
    export let question;

    // Variables
    $: activeQuestion = $stateData.activeQuestion;
    let disabled = false;
    let active = false;

    // Logic
    function activateQuestion() {
        let newStateData = $stateData;
        newStateData.activeQuestion = question.id;
        stateData.set(newStateData);
    }

    function deactivateQuestion() {
        let newStateData = $stateData;
        newStateData.activeQuestion = null;
        stateData.set(newStateData);
    }
    
    function deleteQuestion() {  
        fetch(webinarData.homeUrl + '/wp-json/webinar/questions/?questionId='
            + question.id,  
        {
			method: 'DELETE',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
		})
		.then(res => res.json())
		.then(data => {
            if(data.status == 200) {
                questionsStore.removeQuestion(question.id);        
                deactivateQuestion(); 
            }
		})
		.catch(err=> console.log(err))
	}

    $: if(activeQuestion != null && activeQuestion == question.id) {
        active = true;
    } else if(activeQuestion != null && activeQuestion != question.id) {
        disabled = true;
    } else if(activeQuestion == null) {
        disabled = false;
    }
</script>

<div class="card question" class:active={active} class:mini={mini} in:fade>
    <header>
        <div>
            {$appData.translations.addDate}: {question.time}
        </div>
        <div class="buttons">
            <button class='bcim-button' on:click={activateQuestion} disabled={disabled || active}>
                {#if mini}
                    <span class="material-icons">replay</span>
                {:else}
                    {$appData.translations.answer}
                {/if}
            </button>
            <button class='bcim-button bcim-button-alt' on:click={deleteQuestion} disabled={disabled}>
                {#if mini}
                    <span class="material-icons">delete</span>
                {:else}
                    {$appData.translations.delete}
                {/if}
            </button>
        </div>
    </header>
    <div class="content">
        {@html question.question}
    </div>
</div>	

<style>
    button {
        font-size: 0.8rem;
        padding: 5px 10px;
    }
    .card.active {
        border: 2px solid green;
    }
    .mini {
        font-size: 0.9rem;
    }
    .mini:hover {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    }
    .mini button {
        padding: 1px 3px;
    }
    .mini button span{
        font-size: 1rem;
        line-height: inherit;
    }
    .mini header {
        padding: 0.3rem 0.5rem;
    }
    .mini .content {
        padding: 0.5rem;
    }
</style>