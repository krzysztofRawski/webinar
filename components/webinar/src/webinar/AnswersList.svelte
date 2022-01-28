<script>
	// Import libraries
    import {flip} from 'svelte/animate';
    import {onMount} from 'svelte';

    // Import components
    import Answer from './Answer.svelte';
    import answersStore from '../stores/answers.js';
    import appData from '../stores/appData.js';

    // Variables
    let dataLoaded = false;

    // Logic
    function getAnswers() {
    fetch(webinarData.homeUrl + '/wp-json/webinar/answers/?webinarId=' + webinarData.webinarId,  {
            method: 'GET',
            headers: {
                'X-WP-Nonce': webinarData.security
            },
        })
        .then(res => res.json())
        .then(data => {
            answersStore.set(data);
            dataLoaded = true;
        })
        .catch(err=> console.log(err))
    }

    onMount(()=>{
        getAnswers();
        dataLoaded = true;
    })

    setInterval(()=>{
        if(dataLoaded) {
            getAnswers();
        }
    }, 3000)

</script>

<section>
    <h2>{$appData.translations.answers}</h2>
    {#each $answersStore as answer (answer.question_id)}
    <div animate:flip="{{duration: 400}}">
        <Answer {answer} />
    </div>
    {:else}
        <p>{$appData.translations.noNewAnswers}</p>
    {/each}
</section>