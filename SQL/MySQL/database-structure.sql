/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        26.12.2015
 */

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for shipment_gateway
-- ----------------------------
DROP TABLE IF EXISTS `shipment_gateway`;
CREATE TABLE `shipment_gateway` (
  `id` int(10) unsigned NOT NULL COMMENT 'System given id.',
  `date_added` datetime NOT NULL COMMENT 'Date when the gateway is added.',
  `settings` text COLLATE utf8_turkish_ci COMMENT 'Serialized base64 encoded settings.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that gateway belongs to.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for shipment_gateway_localization
-- ----------------------------
DROP TABLE IF EXISTS `shipment_gateway_localization`;
CREATE TABLE `shipment_gateway_localization` (
  `gateway` int(10) unsigned NOT NULL COMMENT 'Localized gateway.',
  `language` int(10) unsigned NOT NULL COMMENT 'Localization language.',
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized name of gateway.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized url key of shipment gateway.',
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized description.',
  PRIMARY KEY (`gateway`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for shipment_gateway_region
-- ----------------------------
DROP TABLE IF EXISTS `shipment_gateway_region`;
CREATE TABLE `shipment_gateway_region` (
  `id` int(10) unsigned NOT NULL COMMENT 'System given id.',
  `gateway` int(10) unsigned NOT NULL COMMENT 'Gateway.',
  `city` int(10) unsigned DEFAULT NULL COMMENT 'City to ship',
  `state` int(10) unsigned DEFAULT NULL COMMENT 'State to ship.',
  `country` int(10) unsigned DEFAULT NULL COMMENT 'Country to ship.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for shipment_gateway_region_localization
-- ----------------------------
DROP TABLE IF EXISTS `shipment_gateway_region_localization`;
CREATE TABLE `shipment_gateway_region_localization` (
  `region` int(10) unsigned NOT NULL COMMENT 'Localized gateway region',
  `language` int(10) unsigned NOT NULL COMMENT 'Localization language',
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized name.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized url key.',
  PRIMARY KEY (`region`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for shipment_rate
-- ----------------------------
DROP TABLE IF EXISTS `shipment_rate`;
CREATE TABLE `shipment_rate` (
  `id` int(10) unsigned NOT NULL COMMENT 'Sytem given id.',
  `region` int(10) unsigned DEFAULT NULL COMMENT 'Region that rate is valid for.',
  `product_category` int(10) unsigned DEFAULT NULL COMMENT 'Product category that rate is valid for.',
  `rate` decimal(10,2) unsigned NOT NULL,
  `other_restrictions` text COLLATE utf8_turkish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;
