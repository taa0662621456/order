<?php

namespace App\Service\Vendor;

use App\DTO\Order\OrderCreateDTO;
use App\Entity\Vendor\Vendor;
use App\Entity\Vendor\VendorCustomerOrder;
use App\Enum\OrderStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Vendor\OrderPlacedEvent;
use App\ValueObject\Money;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use RuntimeException;

readonly class VendorOrderService
{
    // Constructor to inject dependencies like EntityManager, EventDispatcher, and Logger
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger
    ) {}

    /**
     * Creates a new order for a vendor.
     *
     * @throws InvalidArgumentException If the order amount is invalid.
     * @throws RuntimeException|\Exception If there is an issue with the order limit or customer data.
     */
    public function create(int $vendorId, OrderCreateDTO $dto): VendorCustomerOrder
    {
        // Log the attempt to create a new order
        $this->logger->info('Attempting to create a new order', [
            'customerId' => $dto->customerId,
            'vendorId' => $vendorId,
            'amount' => $dto->amount
        ]);

        // Validate the order amount (throws exception if invalid)
        $this->validateAmount($dto->amount);

        // Get the customer and vendor details from the database
        $customer = $this->getCustomer($dto->customerId);
        $vendor = $this->getVendor($vendorId);

        // Check if the customer has exceeded their order limit
        $this->checkOrderLimit($customer);

        // Create the Money object for the order to ensure correct currency and amount precision
        // This assumes that the currency is always USD, this can be parameterized
        $money = Money::fromMinor((int)($dto->amount * 100), 'USD'); // USD is hardcoded here; consider making it dynamic

        // Start a database transaction to ensure atomicity
        $this->em->beginTransaction();
        try {
            // Persist the new order in the database
            $order = new VendorCustomerOrder($customer, $vendor, $money);
            $this->em->persist($order);
            $this->em->flush();

            // Dispatch the event that the order has been placed
            $this->dispatcher->dispatch(new OrderPlacedEvent($order, new \DateTimeImmutable()));

            // Commit the transaction after the successful creation of the order
            $this->em->commit();

            // Log success after order creation
            $this->logger->info('Order successfully created', ['orderId' => $order->getId()]);

            return $order;
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            $this->em->rollback();

            // Log the error with detailed information
            $this->logger->error('Error creating order', [
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);

            // Rethrow the exception to let the caller handle it
            throw $e;
        }
    }

    /**
     * Validates the order amount to ensure it's within acceptable limits.
     *
     * @throws InvalidArgumentException if the amount is invalid.
     */
    private function validateAmount(float $amount): void
    {
        // Check if the amount is greater than 0
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }

        // You can adjust the limit depending on the currency
        // USD limit is 10,000, but it could be different for other currencies
        $limit = 10000; // This could be dynamic, based on currency
        if ($amount > $limit) {
            throw new InvalidArgumentException('Amount too large');
        }
    }

    /**
     * Retrieves a customer by their ID, ensuring they are not deleted.
     *
     * @throws RuntimeException if the customer is invalid or deleted.
     */
    private function getCustomer(int $customerId): Vendor
    {
        // Using findOneBy for more flexibility (can include additional criteria like 'isDeleted')
        $customer = $this->em->getRepository(Vendor::class)->findOneBy(['id' => $customerId, 'isDeleted' => false]);

        if (!$customer) {
            // If no customer is found or the customer is marked as deleted
            throw new RuntimeException('Customer is invalid or deleted');
        }

        return $customer;
    }

    /**
     * Retrieves a vendor by their ID.
     *
     * @throws RuntimeException if the vendor is not found.
     */
    private function getVendor(int $vendorId): Vendor
    {
        // Ensure that a valid vendor is found
        $vendor = $this->em->getRepository(Vendor::class)->find($vendorId);

        if (!$vendor) {
            throw new RuntimeException('Vendor not found');
        }

        return $vendor;
    }

    /**
     * Checks if the customer has reached their limit of open orders.
     *
     * @throws RuntimeException if the customer exceeds the order limit.
     */
    private function checkOrderLimit(Vendor $customer): void
    {
        try {
            // Check how many open orders the customer has
            $openOrderCount = $this->em->getRepository(VendorCustomerOrder::class)
                ->createQueryBuilder('o')
                ->select('COUNT(o.id)')
                ->where('o.customer = :customer')
                ->andWhere('o.status = :status') // Order status should be pending
                ->setParameters([
                    'customer' => $customer,
                    'status' => OrderStatusEnum::PENDING,
                ])
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            // No orders found, set count to 0
            $openOrderCount = 0;
        } catch (NonUniqueResultException $e) {
            // If there are multiple results, count them explicitly
            $openOrderCount = $this->em->getRepository(VendorCustomerOrder::class)
                ->count(['customer' => $customer, 'status' => OrderStatusEnum::PENDING]);
        }

        // If the customer has more than 10 open orders, throw an exception
        if ($openOrderCount >= 10) {
            throw new RuntimeException('Order limit exceeded');
        }
    }
}
