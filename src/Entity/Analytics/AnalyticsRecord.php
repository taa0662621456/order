<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Analytics;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'analytics_records')]
class AnalyticsRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $orderId;

    public function __construct(string $type, string $orderId)
    { $this->type=$type; $this->orderId=$orderId; }
}
