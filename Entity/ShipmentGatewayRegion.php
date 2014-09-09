<?php
/**
 * @name        ShipmentGatewayRegion
 * @package		BiberLtd\Core\ShipmentGatewayBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        23.09.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Core\Bundles\ShipmentGatewayBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="shipment_gateway_region",
 *     options={"charset":"utf8","collate":"ut8_turkish_ci","engine":"innodb"}
 * )
 */
class ShipmentGatewayRegion extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $gateway;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $city;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $state;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $country;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\ShipmentGatewayBundle\Entity\ShipmentRate",
     *     mappedBy="shipment_gateway_region"
     * )
     */
    private $shipment_rates;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\ShipmentGatewayBundle\Entity\ShipmentGatewayRegionLocalization",
     *     mappedBy="shipment_gateway_regions"
     * )
     */
    protected $localizations;

    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
     *  				Gets $id property.
     * .
     * @author          Murat Ünal
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name                  setCity ()
     *                                Sets the city property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $city
     *
     * @return          object                $this
     */
    public function setCity($city) {
        if(!$this->setModified('city', $city)->isModified()) {
            return $this;
        }
		$this->city = $city;
		return $this;
    }

    /**
     * @name            getCity ()
     *                          Returns the value of city property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->city
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @name                  setCountry ()
     *                                   Sets the country property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $country
     *
     * @return          object                $this
     */
    public function setCountry($country) {
        if(!$this->setModified('country', $country)->isModified()) {
            return $this;
        }
		$this->country = $country;
		return $this;
    }

    /**
     * @name            getCountry ()
     *                             Returns the value of country property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @name                  setGateway ()
     *                                   Sets the gateway property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $gateway
     *
     * @return          object                $this
     */
    public function setGateway($gateway) {
        if(!$this->setModified('gateway', $gateway)->isModified()) {
            return $this;
        }
		$this->gateway = $gateway;
		return $this;
    }

    /**
     * @name            getGateway ()
     *                             Returns the value of gateway property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->gateway
     */
    public function getGateway() {
        return $this->gateway;
    }

    /**
     * @name                  setShipmentRates ()
     *                                         Sets the shipment_rates property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shipment_rates
     *
     * @return          object                $this
     */
    public function setShipmentRates($shipment_rates) {
        if(!$this->setModified('shipment_rates', $shipment_rates)->isModified()) {
            return $this;
        }
		$this->shipment_rates = $shipment_rates;
		return $this;
    }

    /**
     * @name            getShipmentRates ()
     *                                   Returns the value of shipment_rates property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shipment_rates
     */
    public function getShipmentRates() {
        return $this->shipment_rates;
    }

    /**
     * @name                  setState ()
     *                                 Sets the state property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $state
     *
     * @return          object                $this
     */
    public function setState($state) {
        if(!$this->setModified('state', $state)->isModified()) {
            return $this;
        }
		$this->state = $state;
		return $this;
    }

    /**
     * @name            getState ()
     *                           Returns the value of state property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->state
     */
    public function getState() {
        return $this->state;
    }

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getCity()
 * A getCountry()
 * A getGateway()
 * A getId()
 * A getShipmentGatewayRegion_localizations()
 * A getShipmentRates()
 * A getState()
 *
 * A setCity()
 * A setCountry()
 * A setGateway()
 * A setShipmentGatewayRegion_localizations()
 * A setShipmentRates()
 * A setState()
 *
 */
