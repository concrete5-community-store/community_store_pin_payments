<?php
namespace Concrete\Package\CommunityStorePinPayments;

use Package;
use Whoops\Exception\ErrorException;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Payment\Method as PaymentMethod;

require 'vendor/autoload.php';

class Controller extends Package
{
    protected $pkgHandle = 'community_store_pin_payments';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion = '1.0';

    public function getPackageDescription()
    {
        return t("Pin Payments Method for Community Store");
    }

    public function getPackageName()
    {
        return t("Pin Payments Payment Method");
    }
    
    public function install()
    {
        $installed = Package::getInstalledHandles();
        if(!(is_array($installed) && in_array('community_store',$installed)) ) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        } else {
            $pkg = parent::install();
            $pm = new PaymentMethod();
            $pm->add('community_store_pin_payments','Pin Payments',$pkg);
        }
        
    }
    public function uninstall()
    {
        $pm = PaymentMethod::getByHandle('community_store_pin_payments');
        if ($pm) {
            $pm->delete();
        }
        $pkg = parent::uninstall();
    }

}
?>