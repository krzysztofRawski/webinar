import {writable} from 'svelte/store';

function questionsStore() {
    const questions = writable([]);

    return {
        subscribe: questions.subscribe,
        set: questions.set,
        addQuestion: question => {
            questions.update(items=>{
                return [question, ...items]
            })
        },
        removeQuestion: questionId => {
            questions.update(items => {
                return items.filter(question => questionId != question.id)
            })
        }
    }
}

export default questionsStore();