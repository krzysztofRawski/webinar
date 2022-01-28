<script>
    // Import data
    import appData from '../stores/appData.js';
</script>

<section>
    <h2>{$appData.translations.details}</h2>
    <ul>
        <li><span class="material-icons">alarm_on</span><span>Status: <strong>{$appData.basicData.webinarStatusName}</strong></span></li>
        <li><span class="material-icons">bookmark_border</span><span>Nazwa: <strong>{@html $appData.basicData.webinarTitle}</strong></span></li>
        <li><span class="material-icons">event</span><span>Data: <strong>{$appData.basicData.webinarDate}</strong></span></li>
        <li><span class="material-icons">person_outline</span><span>Prowadzący: <strong>{$appData.basicData.webinarTrainer}</strong></span></li>
    </ul>
</section>


<style>
    ul {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }
    ul li {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    ul span {
        display: inline-block;
    }
    .material-icons {
        margin-right: 0.5rem;
    }
</style>