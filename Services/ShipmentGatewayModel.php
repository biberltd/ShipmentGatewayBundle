<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        26.12.2015
 */
namespace BiberLtd\Bundle\ShipmentGatewayBundle\Services;

use BiberLtd\Bundle\CoreBundle\CoreModel;
use BiberLtd\Bundle\ShipmentGatewayBundle\Entity as BundleEntity;
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Services as MLSService;
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;
use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;

class ShipmentGatewayModel extends CoreModel {
    public $entity = array(
            'sg' => array('name' => 'ShipmentGatewayBundle:ShipmentGateway', 'alias' => 'sg'),
            'sgl' => array('name' => 'ShipmentGatewayBundle:ShipmentGatewayLocalization', 'alias' => 'sgl'),
            'sgr' => array('name' => 'ShipmentGatewayBundle:ShipmentGateway', 'alias' => 'sgr'),
            'sgrl' => array('name' => 'ShipmentGatewayBundle:ShipmentGatewayLocalization', 'alias' => 'sgrl'),
        );

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function deleteShipmentGateway($item)
    {
        return $this->deleteShipmentGateways(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteShipmentGateways(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\ShipmentGateway) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                $response = $this->getShipmentGateway($entry);
                if (!$response->error->exist) {
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if ($countDeleted < 0) {
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
        }
        $this->em->flush();
        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listShipmentGateways(array $filter = null, array $sortOrder = null, array$limit = null)
    {
        $timeStamp = microtime(true);
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT ' . $this->entity['sg']['alias']
            . ' FROM ' . $this->entity['sgl']['name'] . ' ' . $this->entity['sgl']['alias']
            . ' JOIN ' . $this->entity['sgl']['alias'] . '.shipment_gateway ' . $this->entity['sg']['alias'];

        if (!is_null($sortOrder)) {
            foreach ($sortOrder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'date_added':
                    case 'site':
                        $column = $this->entity['sg']['alias'] . '.' . $column;
                        break;
                    case 'name':
                    case 'description':
                    case 'url_key':

                        $column = $this->entity['sgl']['alias'] . '.' . $column;
                        break;
                }
                $oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY ' . $oStr . ' ';
        }

        if (!is_null($filter)) {
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE ' . $fStr;
        }

        $qStr .= $wStr . $gStr . $oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);

        $result = $q->getResult();

        $entities = [];
        foreach ($result as $entry) {
            /**
             * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayLocalization $entry
             */
            $id = $entry->getShipmentGateway()->getId();
            if (!isset($unique[$id])) {
                $unique[$id] = '';
                $entities[] = $entry->getShipmentGateway();
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $gateway
     *
     * @return ModelResponse
     */
    public function getShipmentGateway($gateway)
    {
        $timeStamp = microtime(true);
        if ($gateway instanceof BundleEntity\ShipmentGateway) {
            return new ModelResponse($gateway, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
        }
        $result = null;
        switch ($gateway) {
            case is_numeric($gateway):
                $result = $this->em->getRepository($this->entity['sg']['name'])->findOneBy(array('id' => $gateway));
                break;
            case is_string($gateway):
                $response = $this->getShipmentGatewayByUrlKey($gateway);
                if ($response->error->exist) {
                    return $response;
                }
                $result = $response->result->set;
                unset($response);
                break;
        }
        if (is_null($result)) {
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param string $urlKey
     * @param mixed|null $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getShipmentGatewayByUrlKey(string $urlKey, $language = null)
    {
        $timeStamp = microtime(true);
        if (!is_string($urlKey)) {
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['sgl']['alias'] . '.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if (!is_null($language)) {
            /**
             * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel @mModel
             */
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if (!$response->error->exist) {
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['sgl']['alias'] . '.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listShipmentGateways($filter, null, array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = microtime(true);
        $response->result->set = $response->result->set[0];

        return $response;
    }

    /**
     * @param mixed $gateway
     * @param bool $bypass
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
     */
    public function doesShipmentGatewayExist($gateway, bool $bypass = false)
    {
        $response = $this->getShipmentGateway($gateway);
        $exist = true;
        if ($response->error->exist) {
            $exist = false;
            $response->result->set = false;
        }
        if ($bypass) {
            return $exist;
        }
        return $response;
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function insertShipmentGateway($item)
    {
        return $this->insertShipmentGateways(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertShipmentGateways(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = [];
        $localizations = [];
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGateway) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                unset($data->id);
                $entity = new BundleEntity\ShipmentGateway();
                if (!property_exists($data, 'date_added')) {
                    $data->date_added = $now;
                }
                if (!property_exists($data, 'price')) {
                    $data->price = 0;
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'site':
                            /**
                             * @var \BiberLtd\Bundle\SiteManagementBundle\Services\SiteManagementModel $sModel
                             */
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value);
                            if ($response->error->exist) {
                                return $response;
                            }
                            $entity->$set($response->result->set);
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            }
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->em->flush();
            $this->insertShipmentGatewayLocalizations($localizations);
        }
        if ($countInserts > 0) {
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertShipmentGatewayLocalizations(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = [];
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGatewayLocalization) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                $entity = $data['entity'];
                foreach ($data['localizations'] as $locale => $translation) {
                    $lentity = new BundleEntity\ShipmentGatewayLocalization();
                    /**
                     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $lModel
                     */
                    $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $lModel->getLanguage($locale);
                    if ($response->error->exist) {
                        return $response;
                    }
                    $lentity->setLanguage($response->result->set);
                    unset($response);
                    $lentity->setShipmentGateway($entity);
                    foreach ($translation as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        switch ($column) {
                            default:
                                $entity->$set($value);
                                break;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;
                    $countInserts++;
                }
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function updateShipmentGateway($item)
    {
        return $this->updateShipmentGateways(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateShipmentGateways(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countUpdates = 0;
        $updatedItems = [];
        $localizations = [];
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGateway) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                $response = $this->getShipmentGateway($data->id);
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'Product with id / url_key / sku  ' . $data->id . ' does not exist in database.', 'E:D:002');
                }
                /**
                 * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGateway $oldEntity
                 */
                $oldEntity = $response->result->set;
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\ShipmentGatewayLocalization();
                                    /**
                                     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $mlsModel
                                     */
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode);
                                    $localization->setLanguage($response->result->set);
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
                            /**
                             * @var \BiberLtd\Bundle\SiteManagementBundle\Services\SiteManagementModel $sModel
                             */
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value);
                            if ($response->error->exist) {
                                return $response;
                            }
                            $oldEntity->$set($response->result->set);
                            unset($response, $sModel);
                            break;
                        case 'preview_file':
                            /**
                             * @var \BiberLtd\Bundle\FileManagementBundle\Services\FileManagementModel $fModel
                             */
                            $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                            $response = $fModel->getFile($value);
                            if ($response->error->exist) {
                                return $response;
                            }
                            $oldEntity->$set($response->result->set);
                            unset($response, $fModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function deleteShipmentGatewayRegion($item)
    {
        return $this->deleteShipmentGatewayRegions(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function deleteShipmentGatewayRegions(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\ShipmentGatewayRegion) {
                $this->em->remove($entry);
                $countDeleted++;
            } else {
                $response = $this->getShipmentGatewayRegion($entry);
                if (!$response->error->exist) {
                    $this->em->remove($response->result->set);
                    $countDeleted++;
                }
            }
        }
        if ($countDeleted < 0) {
            return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
        }
        $this->em->flush();
        return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
    }

    /**
     * @param array|null $filter
     * @param array|null $sortOrder
     * @param array|null $limit
     *
     * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function listShipmentGatewayRegions(array $filter = null, array $sortOrder = null, array$limit = null)
    {
        $timeStamp = microtime(true);
        if (!is_array($sortOrder) && !is_null($sortOrder)) {
            return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
        }
        $oStr = $wStr = $gStr = $fStr = '';

        $qStr = 'SELECT ' . $this->entity['sgr']['alias']
            . ' FROM ' . $this->entity['sgrl']['name'] . ' ' . $this->entity['sgrl']['alias']
            . ' JOIN ' . $this->entity['sgrl']['alias'] . '.region ' . $this->entity['sgr']['alias'];

        if (!is_null($sortOrder)) {
            foreach ($sortOrder as $column => $direction) {
                switch ($column) {
                    case 'id':
                        $column = $this->entity['sgr']['alias'] . '.' . $column;
                        break;
                    case 'name':
                    case 'url_key':
                        $column = $this->entity['sgrl']['alias'] . '.' . $column;
                        break;
                }
                $oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $oStr = rtrim($oStr, ', ');
            $oStr = ' ORDER BY ' . $oStr . ' ';
        }

        if (!is_null($filter)) {
            $fStr = $this->prepareWhere($filter);
            $wStr .= ' WHERE ' . $fStr;
        }

        $qStr .= $wStr . $gStr . $oStr;
        $q = $this->em->createQuery($qStr);
        $q = $this->addLimit($q, $limit);

        $result = $q->getResult();

        $entities = [];
        foreach ($result as $entry) {
            /**
             * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegionLocalization $entry
             */
            $id = $entry->getShipmentGatewayRegion()->getId();
            if (!isset($unique[$id])) {
                $unique[$id] = '';
                $entities[] = $entry->getShipmentGatewayRegion();
            }
        }
        $totalRows = count($entities);
        if ($totalRows < 1) {
            return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
        }
        return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param mixed $region
     *
     * @return ModelResponse
     */
    public function getShipmentGatewayRegion($region)
    {
        $timeStamp = microtime(true);
        if ($region instanceof BundleEntity\ShipmentGatewayRegion) {
            return new ModelResponse($region, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
        }
        $result = null;
        switch ($region) {
            case is_numeric($region):
                $result = $this->em->getRepository($this->entity['sgr']['name'])->findOneBy(array('id' => $region));
                break;
            case is_string($region):
                $response = $this->getShipmentGatewayRegionByUrlKey($region);
                if ($response->error->exist) {
                    return $response;
                }
                $result = $response->result->set;
                unset($response);
                break;
        }
        if (is_null($result)) {
            return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
        }

        return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
    }

    /**
     * @param string $urlKey
     * @param mixed|null $language
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function getShipmentGatewayRegionByUrlKey(string $urlKey, $language = null)
    {
        $timeStamp = microtime(true);
        if (!is_string($urlKey)) {
            return $this->createException('InvalidParameterValueException', '$urlKey must be a string.', 'E:S:007');
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['sgrl']['alias'] . '.url_key', 'comparison' => '=', 'value' => $urlKey),
                )
            )
        );
        if (!is_null($language))
        {
            /**
             * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $mModel
             */
            $mModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
            $response = $mModel->getLanguage($language);
            if (!$response->error->exist) {
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->entity['sgl']['alias'] . '.language', 'comparison' => '=', 'value' => $response->result->set->getId()),
                        )
                    )
                );
            }
        }
        $response = $this->listShipmentGatewayRegions($filter, null, array('start' => 0, 'count' => 1));
        if ($response->error->exist) {
            return $response;
        }
        $response->stats->execution->start = $timeStamp;
        $response->stats->execution->end = microtime(true);
        $response->result->set = $response->result->set[0];

        return $response;
    }

    /**
     * @param mixed $region
     * @param bool $bypass
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
     */
    public function doesShipmentGatewayRegionExist($region, bool $bypass = false)
    {
        $response = $this->getShipmentGatewayRegion($region);
        $exist = true;
        if ($response->error->exist) {
            $exist = false;
            $response->result->set = false;
        }
        if ($bypass) {
            return $exist;
        }
        return $response;
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function insertShipmentGatewayRegion($item)
    {
        return $this->insertShipmentGatewayRegions(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertShipmentGatewayRegions(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = [];
        $localizations = [];
        $now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGatewayRegion) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                unset($data->id);
                $entity = new BundleEntity\ShipmentGatewayRegion();
                if (!property_exists($data, 'date_added')) {
                    $data->date_added = $now;
                }
                if (!property_exists($data, 'price')) {
                    $data->price = 0;
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'city':
                        case 'state':
                        case 'country':
                            /**
                             * @var \BiberLtd\Bundle\LocationManagementBundle\Services\LocationManagementModel $lModel
                             */
                            $lModel = $this->kernel->getContainer()->get('locationmanagement.model');
                            $get = 'get'.ucfirst($column);
                            $set = 'set'.ucfirst($column);
                            $response = $lModel->$get($value);
                            if ($response->error->exist) {
                                return $response;
                            }
                            $entity->$set($response->result->set);
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            }
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->em->flush();
            $this->insertShipmentGatewayRegionLocalizations($localizations);
        }
        if ($countInserts > 0) {
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function insertShipmentGatewayRegionLocalizations(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countInserts = 0;
        $insertedItems = [];
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGatewayRegionLocalization) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                $entity = $data['entity'];
                foreach ($data['localizations'] as $locale => $translation) {
                    $lEntity = new BundleEntity\ShipmentGatewayRegionLocalization();
                    /**
                     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $lModel
                     */
                    $lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $lModel->getLanguage($locale);
                    if ($response->error->exist) {
                        return $response;
                    }
                    $lEntity->setLanguage($response->result->set);
                    unset($response);
                    $lEntity->setShipmentGatewayRegion($entity);
                    foreach ($translation as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        switch ($column) {
                            default:
                                $entity->$set($value);
                                break;
                        }
                    }
                    $this->em->persist($entity);
                    $insertedItems[] = $entity;
                    $countInserts++;
                }
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
            return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
    }

    /**
     * @param $item
     *
     * @return array
     */
    public function updateShipmentGatewayRegion($item)
    {
        return $this->updateShipmentGatewayRegions(array($item));
    }

    /**
     * @param array $collection
     *
     * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
     */
    public function updateShipmentGatewayRegions(array $collection)
    {
        $timeStamp = microtime(true);
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
        }
        $countUpdates = 0;
        $updatedItems = [];
        $localizations = [];
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShipmentGatewayRegion) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                $response = $this->getShipmentGatewayRegion($data->id);
                if ($response->error->exist) {
                    return $this->createException('EntityDoesNotExist', 'Shipment gateway with id / url_key / sku  ' . $data->id . ' does not exist in database.', 'E:D:002');
                }
                /**
                 * @var \BiberLtd\Bundle\ShipmentGatewayBundle\Entity\ShipmentGatewayRegion $oldEntity
                 */
                $oldEntity = $response->result->set;
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\ShipmentGatewayRegionLocalization();
                                    /**
                                     * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $mlsModel
                                     */
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode);
                                    $localization->setLanguage($response->result->set);
                                    $localization->setShipmentGatewayRegion($oldEntity);
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
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value);
                            if ($response->error->exist) {
                                return $response;
                            }
                            $oldEntity->$set($response->result->set);
                            unset($response, $sModel);
                            break;
                        case 'preview_file':
                            $fModel = $this->kernel->getContainer()->get('filemanagement.model');
                            $response = $fModel->getFile($value);
                            if ($response->error->exist) {
                                return $response;
                            }
                            $oldEntity->$set($response->result->set);
                            unset($response, $fModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
            return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
        }
        return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
    }
}