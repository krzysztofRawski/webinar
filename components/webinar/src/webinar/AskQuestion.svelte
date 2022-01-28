<script>
    // Import components
    import TextEdiitor from '../ui/TextEdiitor.svelte';
    import Message from "../ui/Message.svelte"

    // Import data
    import appData from '../stores/appData.js';

    // Variables
    let showMessage = false;
    let message = '';
    let success = false;
    let timeOut;
    let disableEditor = false;

    // Logic
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

    function sendQuestion(webinarId, questionContent) {
        let body = {
            questionContent: questionContent,
		};
        body = JSON.stringify(body);
        
        fetch(webinarData.homeUrl + '/wp-json/webinar/questions/?webinarId='
        + webinarId,  
        {
			method: 'POST',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
			body: body
		})
		.then(res => res.json())
		.then(data => {
            if(data.status == 200) {
                message = $appData.translations.questionAdded;
                success = true;
                showMessage = true;
                hideMessage(5000);
            } else if(data.status == 400) {
                message = $appData.translations.questionAddedError;
                success = false;
                showMessage = true;
                hideMessage(5000);
            } else if(data.status == 403) {
                message = $appData.translations.questionLimit;
                success = false;
                showMessage = true;
                hideMessage(7000);
            }
		})
		.catch(err=> console.log(err))
	}

    function askQuestion() {
        let webinarId = webinarData.webinarId;
        let questionContent = editor.getData();
        if(questionContent.length > 0) {
            clearEditorData();
            sendQuestion(webinarId, questionContent);
        } else if(questionContent.length == 0) {
            message = $appData.translations.typeQuestion;
            success = false;
            showMessage = true;
            hideMessage(3000);
        } 
    }

</script>

<section>
    <h2>{$appData.translations.askQuestion}</h2>

    <TextEdiitor {disableEditor}>
        <button class='bcim-button' on:click={askQuestion} on:mouseover={stopMessage} disabled={disableEditor}>{$appData.translations.ask}</button>
    </TextEdiitor>

    {#if showMessage}
        <Message {message} {success}/>
    {/if}
</section>

