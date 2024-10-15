<?php

namespace Kamlesh\Promotional\Model\Consumer;

use Psr\Log\LoggerInterface;
use Magento\Framework\MessageQueue\ConsumerInterface;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ProcessPromotionalProduct
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Consumer handler of promotional product messages
     *
     * @param string $message
     *
     * @return void
     */
    public function execute($message)
    {
        $decodedData = $this->serializer->unserialize($message);
        try {
            // Log the message data to specific log file for this consumer
            $this->logger->info('Received message from RabbitMQ: ' . json_encode($decodedData));

        } catch (\Throwable $t) {
            $this->logger->critical($t->getMessage(), $t->getTrace());
        }
    }
}
