<?php
/**
 * @name        ShipmentRate
 * @package		BiberLtd\Core\ShipmentGatewayBundle
 *
 * @author      Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        22.04.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\ShipmentGatewayBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;
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
     */
    private $id;

    /** 
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $rate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $other_restrictions;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion",
     *     inversedBy="shipment_rates"
     * )
     * @ORM\JoinColumn(name="region", referencedColumnName="id", onDelete="CASCADE")
     */
    private $shipment_gateway_region;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ProductManagementBundle\Entity\ProductCategory")
     * @ORM\JoinColumn(name="product_category", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $product_category;
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
     * @name                  setProductCategory ()
     *                                           Sets the product_category property.
     *                                           Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $product_category
     *
     * @return          object                $this
     */
    public function setProductCategory($product_category) {
        if(!$this->setModified('product_category', $product_category)->isModified()) {
            return $this;
        }
		$this->product_category = $product_category;
		return $this;
    }

    /**
     * @name            getProductCategory ()
     *                                     Returns the value of product_category property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->product_category
     */
    public function getProductCategory() {
        return $this->product_category;
    }

    /**
     * @name                  setRate ()
     *                                Sets the rate property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $rate
     *
     * @return          object                $this
     */
    public function setRate($rate) {
        if(!$this->setModified('rate', $rate)->isModified()) {
            return $this;
        }
		$this->rate = $rate;
		return $this;
    }

    /**
     * @name            getRate ()
     *                          Returns the value of rate property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->rate
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * @name                  setShipmentGatewayRegion ()
     *                                                 Sets the shipment_gateway_region property.
     *                                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shipment_gateway_region
     *
     * @return          object                $this
     */
    public function setShipmentGatewayRegion($shipment_gateway_region) {
        if(!$this->setModified('shipment_gateway_region', $shipment_gateway_region)->isModified()) {
            return $this;
        }
		$this->shipment_gateway_region = $shipment_gateway_region;
		return $this;
    }

    /**
     * @name            getShipmentGatewayRegion ()
     *                                           Returns the value of shipment_gateway_region property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shipment_gateway_region
     */
    public function getShipmentGatewayRegion() {
        return $this->shipment_gateway_region;
    }

    /**
     * @name            setOtherRestrictions()
     *                  Sets the other_restrictions property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @use             $this->setModified()
     *
     * @param           mixed $other_restrictions
     *
     * @return          object                $this
     */
    public function setOtherRestrictions($other_restrictions) {
        if($this->setModified('other_restrictions', $other_restrictions)->isModified()) {
            $this->other_restrictions = $other_restrictions;
        }

        return $this;
    }

    /**
     * @name            getOtherRestrictions()
     *                  Returns the value of other_restrictions property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->other_restrictions
     */
    public function getOtherRestrictions() {
        return $this->other_restrictions;
    }


}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Can Berkol
 * 22.04.2014
 * **************************************
 * A getOtherRestrictions()
 * A setOtherRestrictions()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getId()
 * A getProductCategory()
 * A getRate()
 * A getShipmentGatewayRegion()
 * A setProductCategory()
 * A setRate()
 * A setShipmentGatewayRegion()
 *
 */
