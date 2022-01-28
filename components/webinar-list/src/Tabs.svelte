<script>
    import {onMount, onDestroy} from 'svelte';
    import statesStore from './stores/states.js';
    import Webinar from './Webinar.svelte';
    import Tab from './Tab.svelte';

    $: tabs = $statesStore.tabs;
    $: activeTab = $statesStore.activeTab;
    let feched = false;
    let timer = null

    $: if(feched) {
        if(!tabs.find(tab=>{return tab.slug == activeTab})) {
            let newState = $statesStore;
            newState.activeTab = tabs[0].slug;
            statesStore.set(newState);
            getTabs();
        }
    }

    function getTabs() {
        fetch(webinarData.homeUrl + '/wp-json/webinar/statuses',
        {
			method: 'GET',
			headers: {
				'X-WP-Nonce': webinarData.security
			},
		})
		.then(res => res.json())
		.then(data => {
            let newState = $statesStore;
            if(activeTab == '') {
                newState = {
                    activeTab: data[0].slug,
                    tabs: data
                }
            } else if(activeTab != '') {
                newState.tabs = data;
            }
            statesStore.set(newState);
            feched = true;
            }
        )
		.catch(err=> console.log(err))
    }
    
    onMount(()=>{
        getTabs();
        timer = setInterval(()=>{
            console.log('new data')
        getTabs();
    }, 10000);
    })

    onDestroy(()=> {
        clearInterval(timer)
    })
</script>

<div class="tabs">
    <ul>
    {#each tabs as tab}
        <Tab {tab}/>
    {/each}
    </ul>
    <div class="posts-grid">
        {#each tabs as tab}
            {#if activeTab === tab.slug}
                <Webinar statusId={tab.term_id} items={tab.count}/>
            {/if}
        {/each}
    </div>
</div>

<style>
    ul {
        display: flex;
        list-style-type: none;
        padding: 0;
        margin: 0 0 2rem 0;
    }
	.posts-grid {
		display: grid;
		grid-template-columns: 1fr;
		gap: 2rem;
	}
	@media (min-width: 800px) {
		.posts-grid {
			grid-template-columns: repeat(2, 1fr);
		}
    }
    @media (min-width: 1200px) {
		.posts-grid {
			grid-template-columns: repeat(3, 1fr);
		}
	}
</style>