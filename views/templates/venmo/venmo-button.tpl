
{include file="module:paypal/views/templates/_partials/javascript.tpl"}

<div paypal-venmo-button-container></div>

<script>
    function waitPaypalSDKIsLoaded() {
        if (typeof totPaypalSdkButtons === 'undefined' || typeof Venmo === 'undefined') {
            setTimeout(waitPaypalSDKIsLoaded, 200);
            return;
        }

        var venmoButton = new Venmo({
            container: '[paypal-venmo-button-container]'
        });

        venmoButton.initButton();
    }

    waitPaypalSDKIsLoaded();
</script>
