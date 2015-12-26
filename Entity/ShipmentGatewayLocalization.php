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
 *     name="shipment_gateway_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_shipment_gateway_localization", columns={"gateway","language"}),
 *         @ORM\UniqueConstraint(name="idx_u_shipment_gateway_localization_url_key", columns={"language","url_key"})
 *     }
 * )
 */
class ShipmentGatewayLocalization extends CoreEntity
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $description;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ShipmentGateway", inversedBy="localizations")
     * @ORM\JoinColumn(name="gateway", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway
     */
    private $shipment_gateway;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language
     */
    private $language;

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(\string $description) {
        if(!$this->setModified('description', $description)->isModified()) {
            return $this;
        }
		$this->description = $description;
		return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param \BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language
     *
     * @return $this
     */
    public function setLanguage(\BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language $language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
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
     * @param \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway $shipment_gateway
     *
     * @return $this
     */
    public function setShipmentGateway(\BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway $shipment_gateway) {
        if(!$this->setModified('shipment_gateway', $shipment_gateway)->isModified()) {
            return $this;
        }
		$this->shipment_gateway = $shipment_gateway;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway
     */
    public function getShipmentGateway() {
        return $this->shipment_gateway;
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