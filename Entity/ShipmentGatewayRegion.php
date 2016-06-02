<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        26.12.2015
 */
namespace BiberLtd\Bundle\ShipmentGatewayBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
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
     * @var int
     */
    private $id;

    /** 
     * @ORM\ManyToOne(targetEntity="ShipmentGateway")
     * @ORM\JoinColumn(name="gateway", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway
     */
    private $gateway;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\City")
     * @ORM\JoinColumn(name="city", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\LocationManagementBundle\Entity\City
     */
    private $city;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\State")
     * @ORM\JoinColumn(name="state", referencedColumnName="id")
     * @var \BiberLtd\Bundle\LocationManagementBundle\Entity\State
     */
    private $state;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LocationManagementBundle\Entity\Country")
     * @ORM\JoinColumn(name="country", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\LocationManagementBundle\Entity\Country
     */
    private $country;

    /** 
     * @ORM\OneToMany(targetEntity="ShipmentRate", mappedBy="shipment_gateway_region")
     * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentRate
     */
    private $shipment_rates;

    /** 
     * @ORM\OneToMany(targetEntity="ShipmentGatewayRegionLocalization", mappedBy="shipment_gateway_region")
     * @var array
     */
    protected $localizations;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\City $city
     *
     * @return $this
     */
    public function setCity(\BiberLtd\Bundle\LocationManagementBundle\Entity\City $city) {
        if(!$this->setModified('city', $city)->isModified()) {
            return $this;
        }
		$this->city = $city;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\City
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\Country $country
     *
     * @return $this
     */
    public function setCountry(\BiberLtd\Bundle\LocationManagementBundle\Entity\Country $country) {
        if(!$this->setModified('country', $country)->isModified()) {
            return $this;
        }
		$this->country = $country;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @param \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway $gateway
     *
     * @return $this
     */
    public function setGateway(\BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway $gateway) {
        if(!$this->setModified('gateway', $gateway)->isModified()) {
            return $this;
        }
		$this->gateway = $gateway;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway
     */
    public function getGateway() {
        return $this->gateway;
    }

    /**
     * @param \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentRate $shipment_rates
     *
     * @return $this
     */
    public function setShipmentRates(\BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentRate $shipment_rates) {
        if(!$this->setModified('shipment_rates', $shipment_rates)->isModified()) {
            return $this;
        }
		$this->shipment_rates = $shipment_rates;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentRate
     */
    public function getShipmentRates() {
        return $this->shipment_rates;
    }

    /**
     * @param \BiberLtd\Bundle\LocationManagementBundle\Entity\State $state
     *
     * @return $this
     */
    public function setState(\BiberLtd\Bundle\LocationManagementBundle\Entity\State $state) {
        if(!$this->setModified('state', $state)->isModified()) {
            return $this;
        }
		$this->state = $state;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\LocationManagementBundle\Entity\State
     */
    public function getState() {
        return $this->state;
    }

}
