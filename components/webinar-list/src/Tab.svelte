<script>
    import statesStore from './stores/states.js';
    export let tab;
    $: activeTab = $statesStore.activeTab;

    let icons = {
        planed: 'today',
        online: 'devices',
        archive: 'all_inbox'
    }

    function setActiveTab() {
        let newState = $statesStore;
        newState.activeTab = tab.slug;
        statesStore.set(newState);
    }
</script>

<li class:active={tab.slug === activeTab} on:click={setActiveTab}>
    <span class="material-icons">{icons[tab.slug]}</span><span>{tab.name} ({tab.count})</span>
</li>

<style>
    .material-icons {
        margin-right: 0.6rem;
    }
    li {
        padding: 2px 10px;
        margin-right: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    li:hover {
        color: #007cba;
    }
    .active {
        border-bottom: 2px solid lightgrey;
    }
</style>