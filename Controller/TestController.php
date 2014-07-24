<?php

/**
 * TestController
 *
 * This controller is used to install default / test values to the system.
 * The controller can only be accessed from allowed IP address.
 *
 * @package		MemberManagementBundleBundle
 * @subpackage	Controller
 * @name	    TestController
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 *
 */

namespace BiberLtd\Core\Bundles\ShipmentGatewayBundle\Controller;
use BiberLtd\Core\CoreController;


class TestController extends CoreController {

    public function testAction(){
        $sgModel = $this->get('shipmentgateway.model');
        $sg = new \stdClass();
        $sg->id = 1;
        $sg->settings = 'settingss';
        $sg->local = new \stdClass();
        $sg->local->tr = new \stdClass();
        $sg->local->en = new \stdClass();
        $sg->local->tr->name = 'Yurtiçi Kargo';
        $sg->local->tr->url_key = 'yurtici-kargo';
        $sg->local->tr->description = 'Türkiyenin Önde Gelen Kargo Firması';
        $sg->local->en->name = 'Yurtici Cargo';
        $sg->local->en->url_key = 'yurtici-cargo';
        $sg->local->en->description = 'Most famous shipment gateway of Turkey.';


////        $response = $sgModel->insertShipmentGateway($sg);
//        $response = $sgModel->updateShipmentGateway($sg);
//        if ($response['error']) {
//            exit('Kaydedilmedi');
//        }
//        foreach ($response['result']['set'] as $item) {
//            echo $item->getId();die;
//        }
////
        $response = $sgModel->getShipmentGateway(1,'id');
        if ($response['error']) {
            exit('tax bulunamadı0');
        }
        echo $response['result']['set']->getLocalization('en')->getName();die;

//        $response = $sgModel->deleteStock(7);
//        if ($response['error']) {
//            exit('tax bulunamadı0');
//        }
//        echo $response['result']['total_rows'].' row(s) deleted.';die;

    }
}
