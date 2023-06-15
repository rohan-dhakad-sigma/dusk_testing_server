<?php
# Generated code.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeCommerceEvents\Plugin\Framework;

use Magento\AdobeCommerceEventsClient\Event\Converter\EventDataConverter;
use Magento\AdobeCommerceEventsClient\Event\EventList;
use Magento\AdobeCommerceEventsClient\Event\EventStorageWriter;
use Magento\AdobeCommerceEventsClient\Event\EventSubscriber;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Auto generated plugin for Magento\Framework\Event\ManagerInterface
 */
class ManagerInterfacePlugin
{
    // This event causes a segmentation error when bin/magento commands are run and the afterDispatch method handles it.
    private $ignoredEvent = 'core_collection_abstract_load_before';

    /**
     * @var EventList
     */
    private $eventList;

    /**
     * @var EventDataConverter
     */
    private $eventDataConverter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventStorageWriter
     */
    private $storageWriter;

    /**
     * @param EventList $eventList
     * @param EventDataConverter $eventDataConverter
     * @param LoggerInterface $logger
     * @param EventStorageWriter $storageWriter
     */
    public function __construct(
        EventList $eventList,
        EventDataConverter $eventDataConverter,
        LoggerInterface $logger,
        EventStorageWriter $storageWriter
    ) {
        $this->eventList = $eventList;
        $this->eventDataConverter = $eventDataConverter;
        $this->logger = $logger;
        $this->storageWriter = $storageWriter;
    }

    /**
     * Intercepts Dispatch method and invokes after subscriptions if they are available
     *
     * @param ManagerInterface $subject
     * @param mixed $result
     * @param string $eventName
     * @param array $data
     * @return mixed
     */
    public function afterDispatch(ManagerInterface $subject, $result, $eventName, array $data = [])
    {
        try {
            if ($eventName != $this->ignoredEvent) {
                $eventCode = EventSubscriber::EVENT_PREFIX_COMMERCE .
                    EventSubscriber::EVENT_TYPE_OBSERVER . '.' . $eventName;

                if ($this->eventList->isEventEnabled($eventCode)) {
                    $this->storageWriter->createEvent($eventCode, $this->eventDataConverter->convert($data));
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf(
                'Failed to save event data. Event code: "%s", Error: "%s"',
                $eventName,
                $e->getMessage()
            ));
        }

        return $result;
    }
}
