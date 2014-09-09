<?php
/**
 * ShipmentGatewayModel Class
 *
 * This class acts as a database proxy model for ShipmentGatewayBundle functionalities.
 *
 * @vendor      BiberLtd
 * @package     Core\Bundles\ShipmentGatewayModel
 * @subpackage  Services
 * @name        ShipmentGatewayModel
 *
 * @author      Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        19.03.2014
 *
 * @use         Biberltd\Core\Services
 * @use         Biberltd\Core\CoreModel
 * @use         Biberltd\Core\Services\Encryption
 * @use         BiberLtd\Bundle\ShipmentGatewayBundle\Entity
 * @use         BiberLtd\Bundle\ShipmentGatewayBundle\Services
 *
 */
namespace BiberLtd\Bundle\ShipmentGatewayBundle\Services;

/** Extends CoreModel */
use BiberLtd\Bundle\LocationManagementBundle\Services\LocationManagementModel;
use BiberLtd\Bundle\CoreBundle\CoreModel;
/** Entities to be used */
use BiberLtd\Bundle\ShipmentGatewayBundle\Entity as BundleEntity;
/** Helper Models */
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Services as MLSService;
/** Core Service */
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class ShipmentGatewayModel extends CoreModel {

    public $by_opts = array('entity', 'id', 'code', 'url_key', 'post');
    public $entity = array(
            'shipment_gateway' => array('name' => 'ShipmentGatewayBundle:ShipmentGateway', 'alias' => 'sg'),
            'shipment_gateway_localization' => array('name' => 'ShipmentGatewayBundle:ShipmentGatewayLocalization', 'alias' => 'sgl'),
            'shipment_gateway_region' => array('name' => 'ShipmentGatewayBundle:ShipmentGateway', 'alias' => 'sgr'),
            'shipment_gateway_region_localization' => array('name' => 'ShipmentGatewayBundle:ShipmentGatewayLocalization', 'alias' => 'sgrl'),
        );

    /**
     * @name        deleteShipmentGateway ()
     * Deletes an existing item from database.
     *
     * @since            1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteShipmentGateways()
     *
     * @param           mixed $item Entity, id or url key of item
     * @param           string $by
     *
     * @return          mixed           $response
     */
    public function deleteShipmentGateway($item, $by = 'entity')
    {
        return $this->deleteShipmentGateways(array($item), $by);
    }

    /**
     * @name            deleteShipmentGateways ()
     * Deletes provided items from database.
     *
     * @since        1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of ShipmentGateway entities, ids, or codes or url keys
     *
     * @return          array           $response
     */
    public function deleteShipmentGateways($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'err.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\ShipmentGateway) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                switch ($entry) {
                    case is_numeric($entry):
                        $response = $this->getShipmentGateway($entry, 'id');
                        break;
                    case is_string($entry):
                        $response = $this->getProductCategory($entry, 'url_key');
                        break;
                }
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'err.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }

        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.deleted',
        );
        return $this->response;
    }

    /**
     * @name            listShipmentGateways ()
     * Lists shipment_gateway data from database with given params.
     *
     * @author          Said İmamoğlu
     * @version         1.0.0
     * @since           1.0.0
     *
     * @param           array $filter
     * @param           array $sortOrder
     * @param           array $limit
     * @param           string $queryStr
     *
     * @use             $this->createException()
     * @use             $this->prepareWhere()
     * @use             $this->addLimit()
     *
     * @return          array $this->response
     */
    public function listShipmentGateways($filter = null, $sortOrder = null, $limit = null, $queryStr = null)
    {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }

        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['shipment_gateway']['alias']
                . ' FROM ' . $this->entity['shipment_gateway']['name'] . ' ' . $this->entity['shipment_gateway']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortOrder != null) {
            foreach ($sortOrder as $column => $direction) {
                $order_str .= ' ' . $this->entity['shipment_gateway']['alias'] . '.' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        $queryStr .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($queryStr);

        $query = $this->addLimit($query, $limit);
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $shipmentGateways = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getId();
            if (!isset($unique[$id])) {
                $shipmentGateways[$id] = $entry;
                $unique[$id] = $entry->getId();
            }
        }

        $total_rows = count($shipmentGateways);

        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $newCollection = array();
        foreach ($shipmentGateways as $stock) {
            $newCollection[] = $stock;
        }
        unset($shipmentGateways, $unique);

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $newCollection,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name        getShipmentGateway ()
     * Returns details of a gallery.
     *
     * @since        1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listShipmentGateways()
     *
     * @param           mixed $stock id, url_key
     * @param           string $by entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getShipmentGateway($stock, $by = 'id')
    {
        $this->resetResponse();
        $by_opts = array('id', 'sku', 'product');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValue', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($stock) && !is_numeric($stock) && !is_string($stock)) {
            return $this->createException('InvalidParameter', 'ProductCategory or numeric id', 'err.invalid.parameter.product_category');
        }
        if (is_object($stock)) {
            if (!$stock instanceof BundleEntity\ShipmentGateway) {
                return $this->createException('InvalidParameter', 'ProductCategory', 'err.invalid.parameter.product_category');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $stock,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $column = '';
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shipment_gateway']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $stock),
                )
            )
        );
        $response = $this->listShipmentGateways($filter, null, null, null, false);
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     *
     * @name        doesShipmentGatewayExist ()
     * Checks if entry exists in database.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getShipmentGateway()
     *
     * @param           mixed $item id, url_key
     * @param           string $by id, url_key
     *
     * @param           bool $bypass If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesShipmentGatewayExist($item, $by = 'id', $bypass = false)
    {
        $this->resetResponse();
        $exist = false;

        $response = $this->getShipmentGateway($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name        insertShipmentGateway ()
     * Inserts one or more item into database.
     *
     * @since        1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->insertFiles()
     *
     * @param           array $item Collection of entities or post data.
     *
     * @return          array           $response
     */

    public function insertShipmentGateway($item)
    {
        $this->resetResponse();
        return $this->insertShipmentGateways(array($item));
    }

    /**
     * @name            insertShipmentGateways ()
     * Inserts one or more items into database.
     *
     * @since           1.0.0
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */

    public function insertShipmentGateways($collection)
    {
        $countInserts = 0;
        $countLocalizations=0;
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGateway) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $localizations = array();
                $locationModel = new LocationManagementModel($this->kernel);
                $entity = new BundleEntity\ShipmentGateway();
                foreach ($data as $column => $value) {
                    $localeSet=false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'site':
                            $siteModel = $this->getContainer()->get('sitemanagement.model');
                            $response = $siteModel->getSite($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'Site can not found.');
                            }
                            $entity->$set($response['result']['set']);
                            unset($response,$siteModel);
                            break;
                        case 'date_added':
                        case 'date_updated':
                            new $entity->$set(\DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone'))));
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                unset($locationModel);
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        /**
         * Save data.
         */
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertShipmentGatewayLocalizations($localizations);
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            insertShipmentGatewayLocalizations ()
     *                  Inserts one or more tax rate  localizations into database.
     *
     * @since           1.0.0
     * @version         1.0.1
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertShipmentGatewayLocalizations($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\ShipmentGatewayLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $data) {
                    $entity = new BundleEntity\ShipmentGatewayLocalization;
                    $entity->setShipmentGateway($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    } else {
                        break 1;
                    }
                    foreach ($data as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            updateShipmentGateway()
     * Updates single item. The item must be either a post data (array) or an entity
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->resetResponse()
     * @use             $this->updateShipmentGateways()
     *
     * @param           mixed   $item     Entity or Entity id of a folder
     *
     * @return          array   $response
     *
     */

    public function updateShipmentGateway($item)
    {
        $this->resetResponse();
        return $this->updateShipmentGateways(array($item));
    }

    /**
     * @name            updateShipmentGateways()
     * Updates one or more item details in database.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listShipmentGateways()
     *
     *
     * @throws          InvalidParameterException
     *
     * @param           array   $collection     Collection of item's entities or array of entity details.
     *
     * @return          array   $response
     *
     */

    public function updateShipmentGateways($collection)
    {
        $countInserts = 0;
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGateway) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $response = $this->getShipmentGateway($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'ShipmentGateway with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                $locationModel = new LocationManagementModel($this->kernel);
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\ShipmentGatewayLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
                                    $localization->setShipmentGateway($oldEntity);
                                }
                                foreach ($translation as $transCol => $transVal) {
                                    $transSet = 'set' . $this->translateColumnName($transCol);
                                    $localization->$transSet($transVal);
                                }
                                if ($newLocalization) {
                                    $this->em->persist($localization);
                                }
                                $localizations[] = $localization;
                            }
                            $oldEntity->setLocalizations($localizations);
                            break;
                        case 'site':
                            $siteModel = $this->getContainer()->get('sitemanagement.model');
                            $response = $siteModel->getSite($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'Site can not found.');
                            }
                            $oldEntity->$set($response['result']['set']);
                            unset($response,$siteModel);
                            break;
                        case 'date_added':
                        case 'date_updated':
                            new $oldEntity->$set(\DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone'))));
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                    }
                }
                unset($locationModel);
                $this->em->persist($oldEntity);
                $insertedItems[] = $oldEntity;
                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        /**
         * Save data.
         */
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $oldEntity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name        deleteShipmentGatewayRegion ()
     * Deletes an existing item from database.
     *
     * @since            1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteShipmentGatewayRegions()
     *
     * @param           mixed $item Entity, id or url key of item
     * @param           string $by
     *
     * @return          mixed           $response
     */
    public function deleteShipmentGatewayRegion($item, $by = 'entity')
    {
        return $this->deleteShipmentGatewayRegions(array($item), $by);
    }

    /**
     * @name            deleteShipmentGatewayRegions ()
     * Deletes provided items from database.
     *
     * @since        1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of ShipmentGatewayRegion entities, ids, or codes or url keys
     *
     * @return          array           $response
     */
    public function deleteShipmentGatewayRegions($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'err.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\ShipmentGatewayRegion) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                switch ($entry) {
                    case is_numeric($entry):
                        $response = $this->getShipmentGatewayRegion($entry, 'id');
                        break;
                    case is_string($entry):
                        $response = $this->getProductCategory($entry, 'url_key');
                        break;
                }
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'err.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }

        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.deleted',
        );
        return $this->response;
    }

    /**
     * @name            listShipmentGatewayRegions ()
     * Lists shipment_gateway_region data from database with given params.
     *
     * @author          Said İmamoğlu
     * @version         1.0.0
     * @since           1.0.0
     *
     * @param           array $filter
     * @param           array $sortOrder
     * @param           array $limit
     * @param           string $queryStr
     *
     * @use             $this->createException()
     * @use             $this->prepareWhere()
     * @use             $this->addLimit()
     *
     * @return          array $this->response
     */
    public function listShipmentGatewayRegions($filter = null, $sortOrder = null, $limit = null, $queryStr = null)
    {
        $this->resetResponse();
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }

        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT ' . $this->entity['shipment_gateway_region']['alias']
                . ' FROM ' . $this->entity['shipment_gateway_region']['name'] . ' ' . $this->entity['shipment_gateway_region']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortOrder != null) {
            foreach ($sortOrder as $column => $direction) {
                $order_str .= ' ' . $this->entity['shipment_gateway_region']['alias'] . '.' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        $queryStr .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($queryStr);

        $query = $this->addLimit($query, $limit);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $shipmentGateways = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getId();
            if (!isset($unique[$id])) {
                $shipmentGateways[$id] = $entry;
                $unique[$id] = $entry->getId();
            }
        }

        $total_rows = count($shipmentGateways);

        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $newCollection = array();
        foreach ($shipmentGateways as $stock) {
            $newCollection[] = $stock;
        }
        unset($shipmentGateways, $unique);

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $newCollection,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name        getShipmentGatewayRegion ()
     * Returns details of a gallery.
     *
     * @since        1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listShipmentGatewayRegions()
     *
     * @param           mixed $stock id, url_key
     * @param           string $by entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getShipmentGatewayRegion($stock, $by = 'id')
    {
        $this->resetResponse();
        $by_opts = array('id', 'sku', 'product');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValue', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($stock) && !is_numeric($stock) && !is_string($stock)) {
            return $this->createException('InvalidParameter', 'ProductCategory or numeric id', 'err.invalid.parameter.product_category');
        }
        if (is_object($stock)) {
            if (!$stock instanceof BundleEntity\ShipmentGatewayRegion) {
                return $this->createException('InvalidParameter', 'ProductCategory', 'err.invalid.parameter.product_category');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $stock,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $column = '';
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shipment_gateway_region']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $stock),
                )
            )
        );
        $response = $this->listShipmentGatewayRegions($filter, null, null, null, false);
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name        doesShipmentGatewayRegionExist ()
     * Checks if entry exists in database.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->getShipmentGatewayRegion()
     *
     * @param           mixed $item id, url_key
     * @param           string $by id, url_key
     *
     * @param           bool $bypass If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesShipmentGatewayRegionExist($item, $by = 'id', $bypass = false)
    {
        $this->resetResponse();
        $exist = false;

        $response = $this->getShipmentGatewayRegion($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name        insertShipmentGatewayRegion ()
     * Inserts one or more item into database.
     *
     * @since        1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->insertFiles()
     *
     * @param           array $item Collection of entities or post data.
     *
     * @return          array           $response
     */

    public function insertShipmentGatewayRegion($item)
    {
        $this->resetResponse();
        return $this->insertShipmentGatewayRegions(array($item));
    }

    /**
     * @name            insertShipmentGatewayRegions ()
     * Inserts one or more items into database.
     *
     * @since           1.0.0
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */

    public function insertShipmentGatewayRegions($collection)
    {
        $countInserts = 0;
        $countLocalizations=0;
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGatewayRegion) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $localizations = array();
                $locationModel = $this->getContainer()->get('locationmanagement.model');
                $entity = new BundleEntity\ShipmentGatewayRegion();
                foreach ($data as $column => $value) {
                    $localeSet=false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'gateway':
                            $response = $this->getShipmentGateway($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $data);
                            }
                            $entity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'country':
                            $response = $locationModel->getCountry($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'Country can not found.');
                            }
                            $entity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'state':
                            $response = $locationModel->getState($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'State can not found.');
                            }
                            $entity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'city':
                            $response = $locationModel->getCity($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'City can not found.');
                            }
                            $entity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'site':
                            $siteModel = $this->getContainer()->get('sitemanagement.model');
                            $response = $siteModel->getSite($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'Site can not found.');
                            }
                            $entity->$set($response['result']['set']);
                            unset($response,$siteModel);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                unset($locationModel);
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        /**
         * Save data.
         */
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertShipmentGatewayRegionLocalizations($localizations);
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            insertShipmentGatewayRegionLocalizations ()
     *                  Inserts one or more tax rate  localizations into database.
     *
     * @since           1.0.0
     * @version         1.0.1
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertShipmentGatewayRegionLocalizations($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\ShipmentGatewayRegionLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $data) {
                    $entity = new BundleEntity\ShipmentGatewayRegionLocalization;
                    $entity->setShipmentGatewayRegion($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    } else {
                        break 1;
                    }
                    foreach ($data as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            updateShipmentGatewayRegion()
     * Updates single item. The item must be either a post data (array) or an entity
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->resetResponse()
     * @use             $this->updateShipmentGatewayRegions()
     *
     * @param           mixed   $item     Entity or Entity id of a folder
     *
     * @return          array   $response
     *
     */

    public function updateShipmentGatewayRegion($item)
    {
        $this->resetResponse();
        return $this->updateShipmentGatewayRegions(array($item));
    }

    /**
     * @name            updateShipmentGatewayRegions()
     * Updates one or more item details in database.
     *
     * @since           1.0.0
     * @version         1.0.0
     * @author          Said İmamoğlu
     *
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listShipmentGatewayRegions()
     *
     *
     * @throws          InvalidParameterException
     *
     * @param           array   $collection     Collection of item's entities or array of entity details.
     *
     * @return          array   $response
     *
     */

    public function updateShipmentGatewayRegions($collection)
    {
        $countInserts = 0;
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGatewayRegion) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $response = $this->getShipmentGatewayRegion($data->id, 'id');
                if ($response['error']) {
                    return new CoreExceptions\EntityDoesNotExistException($this->kernel,'ShipmentGatewayRegion with id :' . $data->id,'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                $locationModel = $this->getContainer()->get('locationmanagement.model');
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\ShipmentGatewayRegionLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
                                    $localization->setProduct($oldEntity);
                                }
                                foreach ($translation as $transCol => $transVal) {
                                    $transSet = 'set' . $this->translateColumnName($transCol);
                                    $localization->$transSet($transVal);
                                }
                                if ($newLocalization) {
                                    $this->em->persist($localization);
                                }
                                $localizations[] = $localization;
                            }
                            $oldEntity->setLocalizations($localizations);
                            break;
                        case 'gateway':
                            $response = $this->getShipmentGateway($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $data);
                            }
                            $oldEntity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'country':
                            $response = $locationModel->getCountry($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'Country can not found.');
                            }
                            $oldEntity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'state':
                            $response = $locationModel->getState($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'State can not found.');
                            }
                            $oldEntity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'city':
                            $response = $locationModel->getCity($value,'id');
                            if ($response['error']) {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, 'City can not found.');
                            }
                            $oldEntity->$set($response['result']['set']);
                            unset($response);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                }
                unset($locationModel);
                $this->em->persist($oldEntity);
                $insertedItems[] = $oldEntity;
                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        /**
         * Save data.
         */
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $oldEntity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

}
/**
 * Change Log:
 * * * **********************************
 * v1.0.0                      Said İmamoğlu
 * 21.03.2014
 * **************************************
 * A deleteShipmentGateway()
 * A deleteShipmentGateways()
 * A listShipmentGateway()
 * A getShipmentGateway()
 * A doesShipmentGatewayExist()
 * A inserShipmentGateway()
 * A inserShipmentGateways()
 * A updateShipmentGateway()
 * A updateShipmentGateways()
 * A deleteShipmentGatewayRegion()
 * A deleteShipmentGatewayRegions()
 * A listShipmentGatewayRegion()
 * A getShipmentGatewayRegion()
 * A doesShipmentGatewayRegionExist()
 * A inserShipmentGatewayRegion()
 * A inserShipmentGatewayRegions()
 * A updateShipmentGatewayRegion()
 * A updateShipmentGatewayRegions()
 * 
 */