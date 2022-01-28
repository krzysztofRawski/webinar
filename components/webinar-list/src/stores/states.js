import {writable} from 'svelte/store';

const statesStore = writable({
    activeTab: '',
    tabs: []
});

export default statesStore;