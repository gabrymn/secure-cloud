export default class Polling {
    
    constructor (action, ms) {
        this.timer = undefined;
        this.action = action;
        this.ms = ms;
    }

    Start = () => this.timer = setInterval(this.action, this.ms);
    Stop = () => clearInterval(this.timer);
}