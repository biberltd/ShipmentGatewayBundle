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
 *     name="shipment_rate",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_u_shipment_rate_id", columns={"id"})}
 * )
 */
class ShipmentRate extends CoreEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /** 
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $rate;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $other_restrictions;

    /** 
     * @ORM\ManyToOne(targetEntity="ShipmentGatewayRegion", inversedBy="shipment_rates")
     * @ORM\JoinColumn(name="region", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion
     */
    private $shipment_gateway_region;

    /** 
     * @ORM\ManyToOne(targetEntity="ProductCategory")
     * @ORM\JoinColumn(name="product_category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ProductManagementBundle\Entity\ProductCategory
     */
    private $product_category;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param \BiberLtd\Bundle\ProductManagementBundle\Entity\ProductCategory $product_category
     *
     * @return $this
     */
    public function setProductCategory(\BiberLtd\Bundle\ProductManagementBundle\Entity\ProductCategory $product_category) {
        if(!$this->setModified('product_category', $product_category)->isModified()) {
            return $this;
        }
		$this->product_category = $product_category;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ProductManagementBundle\Entity\ProductCategory
     */
    public function getProductCategory() {
        return $this->product_category;
    }

    /**
     * @param float $rate
     *
     * @return $this
     */
    public function setRate(float $rate) {
        if(!$this->setModified('rate', $rate)->isModified()) {
            return $this;
        }
		$this->rate = $rate;
		return $this;
    }

    /**
     * @return float
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * @param \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion $shipment_gateway_region
     *
     * @return $this
     */
    public function setShipmentGatewayRegion(\BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion $shipment_gateway_region) {
        if(!$this->setModified('shipment_gateway_region', $shipment_gateway_region)->isModified()) {
            return $this;
        }
		$this->shipment_gateway_region = $shipment_gateway_region;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion
     */
    public function getShipmentGatewayRegion() {
        return $this->shipment_gateway_region;
    }

    /**
     * @param string $other_restrictions
     *
     * @return $this
     */
    public function setOtherRestrictions(string $other_restrictions) {
        if($this->setModified('other_restrictions', $other_restrictions)->isModified()) {
            $this->other_restrictions = $other_restrictions;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOtherRestrictions() {
        return $this->other_restrictions;
    }
}