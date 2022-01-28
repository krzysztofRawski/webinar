<script>
    // Import data
    import appData from '../stores/appData.js';
</script>

<section>
    <h2>{$appData.translations.webinar}</h2>
            <div class="fluid-width-video-wrapper">
                {@html $appData.basicData.viedoEmbedCode}
            </div>
</section>