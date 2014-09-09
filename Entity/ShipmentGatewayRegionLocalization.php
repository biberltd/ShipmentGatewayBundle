<?php
/**
 * @name        ShipmentGatewayRegionLocalization
 * @package		BiberLtd\Bundle\CoreBundle\ShipmentGatewayBundle
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
namespace BiberLtd\Bundle\ShipmentGatewayBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="shipment_gateway_region_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_shipment_gateway_region_localization", columns={"region","language"}),
 *         @ORM\UniqueConstraint(name="idx_u_shipment_gateway_region_localization_url_key", columns={"language","url_key"})
 *     }
 * )
 */
class ShipmentGatewayRegionLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url_key;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion",
     *     inversedBy="localizations"
     * )
     * @ORM\JoinColumn(name="region", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $shipment_gateway_regions;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $languages;

    /**
     * @name                  setLanguages ()
     *                                     Sets the languages property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $languages
     *
     * @return          object                $this
     */
    public function setLanguages($languages) {
        if(!$this->setModified('languages', $languages)->isModified()) {
            return $this;
        }
		$this->languages = $languages;
		return $this;
    }

    /**
     * @name            getLanguages ()
     *                               Returns the value of languages property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->languages
     */
    public function getLanguages() {
        return $this->languages;
    }

    /**
     * @name                  setName ()
     *                                Sets the name property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $name
     *
     * @return          object                $this
     */
    public function setName($name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

    /**
     * @name            getName ()
     *                          Returns the value of name property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @name                  setShipmentGatewayRegions ()
     *                                                  Sets the shipment_gateway_regions property.
     *                                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shipment_gateway_regions
     *
     * @return          object                $this
     */
    public function setShipmentGatewayRegions($shipment_gateway_regions) {
        if(!$this->setModified('shipment_gateway_regions', $shipment_gateway_regions)->isModified()) {
            return $this;
        }
		$this->shipment_gateway_regions = $shipment_gateway_regions;
		return $this;
    }

    /**
     * @name            getShipmentGatewayRegions ()
     *                                            Returns the value of shipment_gateway_regions property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shipment_gateway_regions
     */
    public function getShipmentGatewayRegions() {
        return $this->shipment_gateway_regions;
    }

    /**
     * @name                  setUrlKey ()
     *                                  Sets the url_key property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $url_key
     *
     * @return          object                $this
     */
    public function setUrlKey($url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
		$this->url_key = $url_key;
		return $this;
    }

    /**
     * @name            getUrlKey ()
     *                            Returns the value of url_key property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url_key
     */
    public function getUrlKey() {
        return $this->url_key;
    }
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getLanguages()
 * A getName()
 * A getShipmentGatewayRegions()
 * A getUrlKey()
 *
 * A setLanguages()
 * A setName()
 * A setShipmentGatewayRegions()
 * A setUrlKey()
 *
 */
