
{include file="module:paypal/views/templates/_partials/javascript.tpl"}

<div style="display: flex">
  <div paypal-venmo-button-container></div>
</div>

{literal}
<script>
    function waitPaypalSDKIsLoaded() {
        if (typeof totVenmoPaypalSdkButtons === 'undefined' || typeof Venmo === 'undefined') {
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
