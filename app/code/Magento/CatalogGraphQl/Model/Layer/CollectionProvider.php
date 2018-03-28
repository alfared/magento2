<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogGraphQl\Model\Layer;

use \Magento\CatalogSearch\Model\ResourceModel\Advanced\Collection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor;
use Magento\Framework\Registry;

/**
 * Class CollectionProvider
 * @package Magento\CatalogGraphQl\Model\Layer
 */
class CollectionProvider implements \Magento\Catalog\Model\Layer\ItemCollectionProviderInterface
{
    /**
     * @var \Magento\CatalogSearch\Model\ResourceModel\Advanced\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\CatalogSearch\Model\ResourceModel\Advanced\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        if (!$this->collection) {
            $collection = $this->collectionFactory->create();
            $this->collection = clone $collection;
            /** @var Registry $registry */
            $registry = \Magento\Framework\App\ObjectManager::getInstance()->get(Registry::class);
            $searchCriteria = $registry->registry('graphql_search_criteria');
            /** @var CollectionProcessor $collectionProcessor */
            $this->collectionProcessor->process($searchCriteria, $collection);
            $ids = $collection->getAllIds();
            // layered navigation filter can work only with IDs
            $this->collection->addIdFilter($ids);
        }
        return $this->collection;
    }
}