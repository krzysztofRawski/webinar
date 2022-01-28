import {writable} from 'svelte/store';

const stateData = writable(
    {
        activeQuestion: null,
        questionsTotal: 0
    }
);

export default stateData;