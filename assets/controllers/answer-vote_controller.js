import { Controller } from 'stimulus';
import axios from 'axios';

export default class extends Controller {
    static targets = ['voteTotal'];
    static values = {
        url: String,
    }

    clickVote(event) {
        event.preventDefault();
        const button = event.currentTarget;

        axios.post(this.urlValue, {
            data: { direction: button.value }
            //JSON.stringify({direction....})
        })
            .then((response) => {
                this.voteTotalTarget.innerHTML = response.data.votes;
            })
        ;
    }
}
