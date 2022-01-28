<script>
	// Import libraries
    import {onMount} from 'svelte';
    import {flip} from 'svelte/animate';
    
    // Import data
    import questionsStore from '../stores/questions.js';
    import appData from '../stores/appData.js';
    import stateData from '../stores/stateData.js';

    // Import components
    import Question from './Question.svelte';
    
    // Variables
    let fetched = false;
    $: limitOfQuestions = $appData.basicData.limitOfQuestions;
    let currentQuestions = [];
    let missingQuestions;
    let mini = false;

    // Logic
    $: if(limitOfQuestions > 3){
        mini = true;
    } else if(limitOfQuestions <= 3) {
        mini = false;
    }

    $: if(fetched) {
        currentQuestions = $questionsStore.map(question => question.id);
    }

    function getQuestions(limitOfQuestions, missingQuestions, currentQuestions) {
        currentQuestions = JSON.stringify(currentQuestions);
           
        fetch(webinarData.homeUrl 
        + '/wp-json/webinar/questions/?limitOfQuestions=' + limitOfQuestions 
        + '&missingQuestions=' + missingQuestions 
        + '&webinarId=' + webinarData.webinarId
        + '&currentQuestions=' + currentQuestions,  
        {
			method: 'GET',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
		})
		.then(res => res.json())
		.then(data => {
            data.forEach(post => {
				let question = {
                    id: post.question_id,
                    userId: post.user_id,
					question: post.question_content,
					time: post.question_time,
				};
				questionsStore.addQuestion(question);
            });
            fetched = true;
		})
		.catch(err=> console.log(err))
    }
    
    function getStats() {
           
        fetch(webinarData.homeUrl + '/wp-json/webinar/stats/?webinarId='+ webinarData.webinarId,
        {
			method: 'GET',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
		})
		.then(res => res.json())
		.then(data => {
            let newStateData = $stateData;
            newStateData.questionsTotal = data;
            stateData.set(newStateData);
		})
		.catch(err=> console.log(err))
	}

	onMount(()=>{
        getQuestions(limitOfQuestions, 0, []);
        getStats();
    })

    setInterval(()=>{
        if(fetched && currentQuestions.length < limitOfQuestions) {
            missingQuestions = limitOfQuestions - currentQuestions.length;
            getQuestions(limitOfQuestions, missingQuestions, currentQuestions);
        }
        getStats();
    }, 3000)
    

</script>

<section>
    <h2>{$appData.translations.questions} <span>(Liczba wszystkich pytań: {$stateData.questionsTotal})</span></h2>
    <div class:mini={mini}>
        {#each $questionsStore as question (question.id)}
        <div animate:flip="{{duration: 400}}">
            <Question {question} {mini}/>
        </div>
        {:else}
            <p>{$appData.translations.noNewQuestions}</p>
        {/each}
    </div>
</section>

<style>
    .mini {
        max-height: 600px;
        overflow: auto;
        padding: 0 1rem;
    }
    h2 span {
        font-size: 0.9rem;
    }
</style>

