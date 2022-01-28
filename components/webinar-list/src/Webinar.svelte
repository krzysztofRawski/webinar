<script>
    import {fade} from 'svelte/transition'
    import {onMount} from 'svelte';
    
    export let statusId;
    export let items;

    let posts = [];

    $: if(items) {
        getWebinars();
    }

    function getWebinars() {
        fetch(webinarData.homeUrl + '/wp-json/webinar/list/?webinarStatus=' + statusId,
        {
			method: 'GET',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
		})
		.then(res => res.json())
		.then(data => {
            posts = data;
		})
		.catch(err=> console.log(err))
    }
    
    onMount(()=>{
        getWebinars();
    })
</script>

{#each posts as post}
<div class="post" in:fade>
    <a href={post.postLink}>
        <img src={post.postThumbnail} alt={post.postName}>
        <div class="data">
            <h4>{@html post.postName}</h4>
            <ul>
                <li><span class="material-icons">event</span><span>Data: <strong>{post.webinarDate}</strong></span></li>
                <li><span class="material-icons">person_outline</span><span>Prowadzący: <strong>{post.webinarTrainer}</strong></span></li>
            </ul>
        </div>
    </a>
</div>
{/each}

<style>
    .post {
        background-color: white;
        box-shadow: 1px 2px 4px rgba(0,0,0,0.2);
        border-radius: 10px;
        margin-bottom: 1rem;
        transition: 0.3s;
    }
    .post:hover {
        box-shadow: 3px 6px 12px 0 rgba(0,0,0,0.2);
    }
    .post img {
        border-radius: 10px 10px 0 0;
    }
    .post .data {
        padding: 0.8rem;
    }
    .post span {
        display: block;
        padding: 0;
        margin: 0;
        color: inherit;
        font-size: 0.9rem;
    }
    .post h4 {
        margin-bottom: 0.8rem;
    }
    .post ul {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }
    .post ul li {
        display: flex;
        align-items: center;
    }
    .post ul span {
        display: inline-block;
    }
    .post .material-icons {
        margin-right: 0.5rem;
        font-size: 1.4rem;
    }
</style>