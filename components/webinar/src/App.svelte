<script>
	// Import libraries
	import {onMount} from 'svelte';

	// Import data
	import currentUserData from './stores/currentUserData.js';
	import questionsStore from './stores/questions.js'
	import appData from './stores/appData.js';

	// Import components
	import AnswerQuestion from "./webinar/AnswerQuestion.svelte";
	import AnswersList from "./webinar/AnswersList.svelte";
	import QuestionsList from "./webinar/QuestionsList.svelte";
	import Details from "./webinar/Details.svelte";
	import Video from "./webinar/Video.svelte";
	import AskQuestion from "./webinar/AskQuestion.svelte";
	import Description from './webinar/Description.svelte';

	// Variables
	const webinarId = webinarData.webinarId;
	let trainer;
	let status;
	let interval;
	let appDataLoaded = false;
	let userDataLoaded = false;

	// Logic
	function clearQuestionStore() {
		questionsStore.set([]);
	}

	function getUserData() {
		fetch(webinarData.homeUrl + '/wp-json/webinar/user/',  {
				method: 'GET',
				headers: {
					'X-WP-Nonce': webinarData.security
				},
			})
			.then(res => res.json())
			.then(data => {
				currentUserData.set(data);
				userDataLoaded = true;
			})
			.catch(err=> console.log(err))
	}
	
	function getWebinarData() {
		fetch(webinarData.homeUrl + '/wp-json/webinar/data/?webinarId=' + webinarId + '&language=pl',  {
				method: 'GET',
				headers: {
					'X-WP-Nonce': webinarData.security
				},
			})
			.then(res => res.json())
			.then(data => {
				appData.set(data);
				appDataLoaded = true;
			})
			.catch(err=> console.log(err))
	}

	onMount(()=> {
		getWebinarData();
		getUserData()
	})

	$: if(userDataLoaded && appDataLoaded) {
		if($currentUserData.userRole == 'trainer') {
			trainer = true;
		} else if(!$currentUserData.userRole) {
			trainer = false;
		}
		status = $appData.basicData.webinarStatus;
	}

	$: if(status == 'planed') {
		clearQuestionStore();
		clearInterval(interval);
		interval = setInterval(() => {
			getWebinarData();
		}, 5000);
	} else if(status == 'online') {
		clearInterval(interval);
		interval = setInterval(() => {
			getWebinarData();
		}, 10000);	
	} else if(status == 'archive') {
		clearQuestionStore();
		clearInterval(interval)
	}
</script>

<article>
	{#if status === 'planed'}
		<div class="column-left">
			<Description />
		</div>
		<div class="column-right">
			<Details />
		</div>
	{:else if status === 'online'}
		{#if trainer}
			<div class="column-left">
				<QuestionsList />
			</div>
			<div class="column-right">
				<AnswerQuestion />
				<Details />
			</div>
			<div class="row-bottom">
				<AnswersList />
			</div>
		{:else}
			<div class="column-left">
				<Video />
			</div>
			<div class="column-right">
				<AskQuestion />
				<Details />
			</div>
			<div class="row-bottom">
				<AnswersList />
			</div>
		{/if}
	{:else if status === 'archive'}
		<div class="column-left">
			<Video />
		</div>
		<div class="column-right">
			<Details />
		</div>
		<div class="row-bottom">
			<AnswersList />
		</div>
	{/if}
</article>

<style>
	article {
		display: grid;
		grid-template-columns: 1fr;
		gap: 2rem;
	}
	@media (min-width: 1200px) {
		article {
			grid-template-columns: 2fr 1fr;
		}
		.column-left {
            grid-column: 1/2;
		}
		.column-right {
            grid-column: 2/3;
		}
		.row-bottom {
			grid-column: 1/3;
		}
	}
</style>