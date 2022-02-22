
{include file="module:paypal/views/templates/_partials/javascript.tpl"}

<div paypal-venmo-button-container></div>
{literal}
<script>
    function waitPaypalSDKIsLoaded() {
        if (typeof totPaypalSdkButtons === 'undefined' || typeof Venmo === 'undefined') {
            setTimeout(waitPaypalSDKIsLoaded, 200);
            return;
        }

        var venmoButton = new Venmo({
            container: '[paypal-venmo-button-container]',
            controller: '{/literal}{Context::getContext()->link->getModuleLink('paypal', 'ScInit')|addslashes}{literal}',
            validationController: '{/literal}{Context::getContext()->link->getModuleLink('paypal', 'ecValidation')|addslashes}{literal}'
        });
        window.venmoObj = venmoButton;

        venmoButton.initButton();
    }

    waitPaypalSDKIsLoaded();
</script>
{/literal}
