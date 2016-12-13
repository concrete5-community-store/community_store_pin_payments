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
    <?=$form->label('pinPaymentsTestPrivateApiKey',t('Test Secret Key'))?>
    <input type="text" name="pinPaymentsTestPrivateApiKey" value="<?= $pinPaymentsTestPrivateApiKey?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('pinPaymentsTestPublicApiKey',t('Test Publishable Key'))?>
    <input type="text" name="pinPaymentsTestPublicApiKey" value="<?= $pinPaymentsTestPublicApiKey?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('pinPaymentsLivePrivateApiKey',t('Live Secret Key'))?>
    <input type="text" name="pinPaymentsLivePrivateApiKey" value="<?= $pinPaymentsLivePrivateApiKey?>" class="form-control">
</div>

<div class="form-group">
    <?=$form->label('pinPaymentsLivePublicApiKey',t('Live Publishable Key'))?>
    <input type="text" name="pinPaymentsLivePublicApiKey" value="<?= $pinPaymentsLivePublicApiKey?>" class="form-control">
</div>

