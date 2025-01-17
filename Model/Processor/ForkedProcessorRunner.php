<?php

declare(strict_types=1);

namespace Zepgram\MultiThreading\Model\Processor;

use Zepgram\MultiThreading\Model\Processor\ForkedProcessorFactory;
use Zepgram\MultiThreading\Model\ItemProvider\ItemProviderInterface;

class ForkedProcessorRunner
{
    /** @var ForkedProcessorFactory */
    private $forkedProcessorFactory;

    public function __construct(
        ForkedProcessorFactory $forkedProcessorFactory
    ) {
        $this->forkedProcessorFactory = $forkedProcessorFactory;
    }

    /**
     * @param ItemProviderInterface $itemProvider
     * @param callable $callback
     * @param int $maxChildrenProcess
     * @param bool $isParallelize
     * @return void
     */
    public function run(
        ItemProviderInterface $itemProvider,
        callable $callback,
        int $maxChildrenProcess,
        bool $isParallelize
    ): void {
        /** @var $forkedProcessor ForkedProcessor */
        $forkedProcessor = $this->forkedProcessorFactory->create([
            'itemProvider' => $itemProvider,
            'callback' => $callback,
            'maxChildrenProcess' => $maxChildrenProcess,
            'isParallelize' => $isParallelize
        ]);

        $forkedProcessor->process();
    }
}
