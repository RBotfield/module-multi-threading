<?php

declare(strict_types=1);

namespace Zepgram\MultiThreading\Model;

use Magento\Framework\Data\Collection;
use Zepgram\MultiThreading\Model\ItemProvider\CollectionWrapper;
use Zepgram\MultiThreading\Model\ItemProvider\CollectionWrapperFactory;
use Zepgram\MultiThreading\Model\Processor\ForkedProcessorRunner;

class ForkedCollectionProcessor
{
    /** @var ForkedProcessorRunner */
    private $forkedProcessorRunner;

    /** @var CollectionWrapperFactory */
    private $collectionWrapperFactory;

    public function __construct(
        ForkedProcessorRunner $forkedProcessorRunner,
        CollectionWrapperFactory $collectionWrapperFactory
    ) {
        $this->forkedProcessorRunner = $forkedProcessorRunner;
        $this->collectionWrapperFactory = $collectionWrapperFactory;
    }

    /**
     * @param Collection $collection
     * @param callable $callback
     * @param int $pageSize
     * @param int $maxChildrenProcess
     * @param bool $isParallelize
     * @return void
     */
    public function process(
        Collection $collection,
        callable $callback,
        int $pageSize = 100,
        int $maxChildrenProcess = 10,
        bool $isParallelize = true
    ): void {
        $collection->setPageSize($pageSize);

        /** @var CollectionWrapper $itemProvider */
        $itemProvider = $this->collectionWrapperFactory->create([
            'collection' => $collection
        ]);

        $this->forkedProcessorRunner->run($itemProvider, $callback, $maxChildrenProcess, $isParallelize);
    }
}
