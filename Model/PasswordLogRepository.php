<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Thomas\CustomerPassword\Api\Data\PasswordLogInterface;
use Thomas\CustomerPassword\Api\Data\PasswordLogInterfaceFactory;
use Thomas\CustomerPassword\Api\Data\PasswordLogSearchResultsInterfaceFactory;
use Thomas\CustomerPassword\Api\PasswordLogRepositoryInterface;
use Thomas\CustomerPassword\Model\ResourceModel\PasswordLog as ResourcePasswordLog;
use Thomas\CustomerPassword\Model\ResourceModel\PasswordLog\CollectionFactory as PasswordLogCollectionFactory;

class PasswordLogRepository implements PasswordLogRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var PasswordLogInterfaceFactory
     */
    protected $passwordLogFactory;

    /**
     * @var PasswordLogCollectionFactory
     */
    protected $passwordLogCollectionFactory;

    /**
     * @var PasswordLog
     */
    protected $searchResultsFactory;

    /**
     * @var ResourcePasswordLog
     */
    protected $resource;


    /**
     * @param ResourcePasswordLog $resource
     * @param PasswordLogInterfaceFactory $passwordLogFactory
     * @param PasswordLogCollectionFactory $passwordLogCollectionFactory
     * @param PasswordLogSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePasswordLog $resource,
        PasswordLogInterfaceFactory $passwordLogFactory,
        PasswordLogCollectionFactory $passwordLogCollectionFactory,
        PasswordLogSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->passwordLogFactory = $passwordLogFactory;
        $this->passwordLogCollectionFactory = $passwordLogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(PasswordLogInterface $passwordLog)
    {
        try {
            $this->resource->save($passwordLog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the passwordLog: %1',
                $exception->getMessage()
            ));
        }
        return $passwordLog;
    }

    /**
     * @inheritDoc
     */
    public function get($passwordLogId)
    {
        $passwordLog = $this->passwordLogFactory->create();
        $this->resource->load($passwordLog, $passwordLogId);
        if (!$passwordLog->getId()) {
            throw new NoSuchEntityException(__('PasswordLog with id "%1" does not exist.', $passwordLogId));
        }
        return $passwordLog;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->passwordLogCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(PasswordLogInterface $passwordLog)
    {
        try {
            $passwordLogModel = $this->passwordLogFactory->create();
            $this->resource->load($passwordLogModel, $passwordLog->getPasswordlogId());
            $this->resource->delete($passwordLogModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the PasswordLog: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($passwordLogId)
    {
        return $this->delete($this->get($passwordLogId));
    }
}

