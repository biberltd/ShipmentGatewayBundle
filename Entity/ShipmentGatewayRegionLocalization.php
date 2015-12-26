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
     * @var string
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $url_key;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ShipmentGatewayRegion", inversedBy="localizations")
     * @ORM\JoinColumn(name="region", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $shipment_gateway_region;

    /**
     * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
     *
     * @return $this
     */
    public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
        if(!$this->setModified('languages', $language)->isModified()) {
            return $this;
        }
		$this->language = $languages;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(\string $name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion $shipment_gateway_regions
     *
     * @return $this
     */
    public function setShipmentGatewayRegion(\BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion $shipment_gateway_regions) {
        if(!$this->setModified('shipment_gateway_region', $shipment_gateway_regions)->isModified()) {
            return $this;
        }
		$this->shipment_gateway_region = $shipment_gateway_regions;
		return $this;
    }

    /**
     * @return mixed
     */
    public function getShipmentGatewayRegion() {
        return $this->shipment_gateway_region;
    }

    /**
     * @param string $url_key
     *
     * @return $this
     */
    public function setUrlKey(\string $url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
		$this->url_key = $url_key;
		return $this;
    }

    /**
     * @return string
     */
    public function getUrlKey() {
        return $this->url_key;
    }
}