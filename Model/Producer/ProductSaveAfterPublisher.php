<?php

namespace Kamlesh\Promotional\Model\Producer;

use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;

class ProductSaveAfterPublisher
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     */
    public function __construct(PublisherInterface $publisher, LoggerInterface $logger)
    {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * Publish message to the topic
     *
     * @param array $data
     * @return void
     */
    public function publish(array $data)
    {
        try {
            // Serialize the array to a JSON string
            $jsonData = json_encode($data);

            // Log the data being published
            $this->logger->info('Publishing to topic: promotional.product.save.after', ['data' => $jsonData]);

            // Publish the message to the topic
            $this->publisher->publish('promotional.product.save.after', $jsonData);
        } catch (\Exception $e) {
            // Log any exceptions
            $this->logger->error('Error publishing message: ' . $e->getMessage());
        }
    }
}