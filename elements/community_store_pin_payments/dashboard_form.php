<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 
extract($vars);
?>
<div class="form-group">
    <?=$form->label('pinPaymentsCurrency',t('Currency'))?>
    <?=$form->select('pinPaymentsCurrency',$currencies,$currency)?>
</div>

<div class="form-group">
    <?=$form->label('pinPaymentsMode',t('Mode'))?>
    <?=$form->select('pinPaymentsMode',array('test'=>t('Test'), 'live'=>t('Live')),$mode)?>
</div>

<div class="form-group">
    <label><?=t("Test Secret Key")?></label>
    <input type="text" name="pinPaymentsTestPrivateApiKey" value="<?= $pinPaymentsTestPrivateApiKey?>" class="form-control">
</div>

<div class="form-group">
    <label><?=t("Test Publishable Key")?></label>
    <input type="text" name="pinPaymentsTestPublicApiKey" value="<?= $pinPaymentsTestPublicApiKey?>" class="form-control">
</div>

<div class="form-group">
    <label><?=t("Live Secret Key")?></label>
    <input type="text" name="pinPaymentsLivePrivateApiKey" value="<?= $pinPaymentsLivePrivateApiKey?>" class="form-control">
</div>

<div class="form-group">
    <label><?=t("Live Publishable Key")?></label>
    <input type="text" name="pinPaymentsLivePublicApiKey" value="<?= $pinPaymentsLivePublicApiKey?>" class="form-control">
</div>

