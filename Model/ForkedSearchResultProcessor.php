<?php

declare(strict_types=1);

namespace Zepgram\MultiThreading\Model;

use InvalidArgumentException;
use Magento\Framework\Api\SearchCriteria;
use Zepgram\MultiThreading\Model\ForkedProcessorFactory;
use Zepgram\MultiThreading\Model\ItemProvider\SearchResultWrapper;
use Zepgram\MultiThreading\Model\ItemProvider\SearchResultWrapperFactory;
use Zepgram\MultiThreading\Model\Processor\ForkedProcessorRunner;

class ForkedSearchResultProcessor
{
    /** @var ForkedProcessorRunner */
    private $forkedProcessorRunner;

    /** @var SearchResultWrapperFactory */
    private $searchResultWrapperFactory;

    public function __construct(
        ForkedProcessorRunner $forkedProcessorRunner,
        SearchResultWrapperFactory $searchResultWrapperFactory
    ) {
        $this->forkedProcessorRunner = $forkedProcessorRunner;
        $this->searchResultWrapperFactory = $searchResultWrapperFactory;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param $repository
     * @param callable $callback
     * @param int $pageSize
     * @param int $maxChildrenProcess
     * @param bool $isParallelize
     * @return void
     */
    public function process(
        SearchCriteria $searchCriteria,
        $repository,
        callable $callback,
        int $pageSize = 100,
        int $maxChildrenProcess = 10,
        bool $isParallelize = true
    ): void {
        if (!method_exists($repository, 'getList')) {
            throw new InvalidArgumentException(
                sprintf('The repository class must have a method called "getList"')
            );
        }

        $searchCriteria->setPageSize($pageSize);

        /** @var SearchResultWrapper $itemProvider */
        $itemProvider = $this->searchResultWrapperFactory->create([
            'searchCriteria' => $searchCriteria,
            'repository' => $repository
        ]);

        $this->forkedProcessorRunner->run($itemProvider, $callback, $maxChildrenProcess, $isParallelize);
    }
}
