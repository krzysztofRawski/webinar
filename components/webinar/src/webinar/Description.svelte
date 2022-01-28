<script>
    // Import data
    import appData from '../stores/appData.js';
</script>

<section>
    <h2>{$appData.translations.description}</h2>
    <div class="decription">
        <header>
            <div class="cropped-img" style="background-image: url('{$appData.basicData.webinarImage}'"></div>
        </header>
        <section>
            {@html $appData.basicData.description}
        </section>
    </div>
</section>

<style>
    .cropped-img {
        width: 100%;
        height: 200px;
        background-position: center center;
        background-size: cover;
        background-repeat: no-repeat;
        margin-bottom: 2rem;
        border-radius: 10px;
        box-shadow: 1px 2px 4px rgba(0,0,0,0.2);
        filter: brightness(50%);
    }
</style>

