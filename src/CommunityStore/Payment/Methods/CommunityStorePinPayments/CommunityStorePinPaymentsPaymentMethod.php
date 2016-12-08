<?php
namespace Concrete\Package\CommunityStorePinPayments\Src\CommunityStore\Payment\Methods\CommunityStorePinPayments;

use Concrete\Package\CommunityStore\Controller\SinglePage\Dashboard\Store;
use Core;
use Config;
use Exception;
use Omnipay\Omnipay;

use \Concrete\Package\CommunityStore\Src\CommunityStore\Payment\Method as StorePaymentMethod;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Utilities\Calculator as StoreCalculator;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Customer\Customer as StoreCustomer;

class CommunityStorePinPaymentsPaymentMethod extends StorePaymentMethod
{

    public function dashboardForm()
    {
        $this->set('mode', Config::get('community_store_pin_payments.mode'));
        $this->set('currency',Config::get('community_store_pin_payments.currency'));
        $this->set('pinPaymentsTestPublicApiKey',Config::get('community_store_pin_payments.testPublicApiKey'));
        $this->set('pinPaymentsLivePublicApiKey',Config::get('community_store_pin_payments.livePublicApiKey'));
        $this->set('pinPaymentsTestPrivateApiKey',Config::get('community_store_pin_payments.testPrivateApiKey'));
        $this->set('pinPaymentsLivePrivateApiKey',Config::get('community_store_pin_payments.livePrivateApiKey'));
        $this->set('form',Core::make("helper/form"));

        $gateways = array(
            'pin_form'=>'Pin Payments (Form)'
        );

        $this->set('gateways',$gateways);

        $currencies = array(
        	'AUD'=>t('Australian Dollar'),
            'USD'=>t('United States Dollar'),
            'NZD'=>t('New Zealand Dollar'),
            'SGD'=>t('Singaporean Dollar'),
            'EUR'=>t('Euro'),
            'GBP'=>t('Pound Sterling'),
            'CAD'=>t('Canadian Dollar'),
            'HKD'=>t('Hong Kong Dollar'),
            'JPY'=>t('Japenese Yen'),
            'MYR'=>t('Malaysian ringgit'),
            'THB'=>t('Thai Baht'),
            'PHP'=>t('Philipine Pesp'),
            'ZAR'=>t('South African Rand'),
            'IDR'=>t('Indonesian Rupiah')
        );

        $this->set('currencies',$currencies);
    }
    
    public function save(array $data = [])
    {
        Config::save('community_store_pin_payments.mode',$data['pinPaymentsMode']);
        Config::save('community_store_pin_payments.currency',$data['pinPaymentsCurrency']);
        Config::save('community_store_pin_payments.testPublicApiKey',$data['pinPaymentsTestPublicApiKey']);
        Config::save('community_store_pin_payments.livePublicApiKey',$data['pinPaymentsLivePublicApiKey']);
        Config::save('community_store_pin_payments.testPrivateApiKey',$data['pinPaymentsTestPrivateApiKey']);
        Config::save('community_store_pin_payments.livePrivateApiKey',$data['pinPaymentsLivePrivateApiKey']);
    }
    public function validate($args,$e)
    {
        return $e;
    }
    public function checkoutForm()
    {
        $mode = Config::get('community_store_pin_payments.mode');
        $this->set('mode',$mode);
        $this->set('currency',Config::get('community_store_pin_payments.currency'));

        if ($mode == 'live') {
            $this->set('publicAPIKey',Config::get('community_store_pin_payments.livePublicApiKey'));
        } else {
            $this->set('publicAPIKey',Config::get('community_store_pin_payments.testPublicApiKey'));
        }

        $customer = new StoreCustomer();

        $this->set('email', $customer->getEmail());
        $this->set('form',Core::make("helper/form"));
        $this->set('amount',  number_format(StoreCalculator::getGrandTotal() * 100, 0, '', ''));

        $pmID = StorePaymentMethod::getByHandle('community_store_pin_payments')->getID();
        $this->set('pmID',$pmID);
        $years = array();
        $year = date("Y");
        for($i=0;$i<15;$i++){
            $years[$year+$i] = $year+$i;
        }
        $this->set("years",$years);
    }
    
    public function submitPayment()
    {
        $customer = new StoreCustomer();
        $email = trim($customer->getEmail());

        $gateway = Omnipay::create('Pin');
        $currency = Config::get('community_store_pin_payments.currency');
        $mode =  Config::get('community_store_pin_payments.mode');

        if ($mode == 'test') {
            $privateKey = Config::get('community_store_pin_payments.testPrivateApiKey');
        } else {
            $privateKey = Config::get('community_store_pin_payments.livePrivateApiKey');
        }


        $gateway->initialize(array(
            'secretKey' => $privateKey,
            'testMode'  => ($mode == 'test'),
       ));

        $token = $_POST['pinToken'];

        $card = new \Omnipay\Common\CreditCard(array(
                'number'       => '4200000000000000', // dummy card that always validates, the card details here aren't actually processed
                'expiryMonth'  => '01',
                'expiryYear'   => date('Y') + 1,
                'cvv'          => '123',
                'email'        => $email

        ));

        $response = $gateway->purchase(
                array(
                    'email' => $email,
                    'description'=>t('Order placed at ') . Config::get('concrete.site'),
                    'amount' =>  number_format(StoreCalculator::getGrandTotal(), 2, '.', ''),
                    'currency' => $currency,
                    'card'=>$card,
                    'token' => $token

                    )
                )->send();


        if ($response->isSuccessful()) {
            // payment was successful: update database
            return array('error'=>0, 'transactionReference'=>$response->getTransactionReference());
        } else {
            // payment failed: display message to customer
            return array('error'=>1,'errorMessage'=> $response->getMessage());
        }
    }

    public function getPaymentMethodName(){
        return 'Pin Payments';
    }

    public function getPaymentMethodDisplayName()
    {
        return $this->getPaymentMethodName();
    }
    
}

return __NAMESPACE__;