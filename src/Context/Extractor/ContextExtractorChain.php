<?php
declare(strict_types=1);

namespace Jaeger\Symfony\Context\Extractor;

use Jaeger\Span\Context\SpanContext;

class ContextExtractorChain implements ContextExtractorInterface
{
    private $queue;

    public function __construct(\SplPriorityQueue $queue)
    {
        $this->queue = $queue;
    }

    public function add(ContextExtractorInterface $extractor, int $priority = 0): ContextExtractorChain
    {
        $this->queue->insert($extractor, $priority);

        return $this;
    }

    public function extract(): ?SpanContext
    {
        $queue = clone $this->queue;
        while (false === $queue->isEmpty()) {
            if (null !== ($context = $queue->extract()->extract())) {
                return $context;
            }
        }

        return null;
    }
}
