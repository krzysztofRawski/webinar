<script>
    // Import data
    import appData from '../stores/appData.js';
    import stateData from '../stores/stateData.js';
    import questionsStore from '../stores/questions.js';

    // Import components
    import TextEdiitor from '../ui/TextEdiitor.svelte';
    import Message from "../ui/Message.svelte";

    // Variables
    $: activeQuestion = $stateData.activeQuestion;
    let showMessage = false;
    let message = '';
    let success = false;
    let timeOut;
    let disableEditor;

    // Logic
    $: if(activeQuestion != null) {
        disableEditor = false;
    } else if(activeQuestion == null) {
        disableEditor = true;
    }

    function clearEditorData() {
        editor.setData('');
    }

    function hideMessage(time) {
        timeOut = setTimeout(() => {
            showMessage = false;
            message = '';
            success = false;
            }, time);
    }

    function stopMessage() {
        clearTimeout(timeOut);
        showMessage = false;
        message = '';
        success = false;
    }

    function deactivateQuestion() {
        let newStateData = $stateData;
        newStateData.activeQuestion = null;
        stateData.set(newStateData);
    }

    function sendAnswer(questionId, questionReply) {
        let body = {
            questionReply: questionReply,
		};
        body = JSON.stringify(body);
        
        fetch(webinarData.homeUrl + '/wp-json/webinar/answers/?questionId='
        + questionId,  
        {
			method: 'PUT',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
			body: body
		})
		.then(res => res.json())
		.then(data => {
            if(data.status == 200) {
                message = $appData.translations.answerAdded;
                success = true;
                showMessage = true;
                questionsStore.removeQuestion(activeQuestion);        
                deactivateQuestion(); 
                hideMessage(5000);
            } else if(data.status == 400) {
                message = $appData.translations.questionAddedError;
                success = false;
                showMessage = true;
                hideMessage(5000);
            }
		})
		.catch(err=> console.log(err))
	}


    function answerQuestion() {
        let questionReply = editor.getData();
        if(questionReply.length > 0) {
            clearEditorData();
            sendAnswer(activeQuestion, questionReply);
        } else if(questionReply.length == 0) {
            message = $appData.translations.typeQuestion;
            success = false;
            showMessage = true;
            hideMessage(3000);
        } 
    }

</script>

<section>
    <h2>{$appData.translations.answerQuestion}</h2>

    <TextEdiitor {disableEditor}>
        <button class='bcim-button' on:click={answerQuestion} on:mouseover={stopMessage} disabled={disableEditor}>{$appData.translations.answer}</button>
    </TextEdiitor>

    {#if showMessage}
        <Message  {message} {success}/>
    {/if}
</section>

