<?php
namespace App\Service\Vendor;
use App\Entity\Vendor\Vendor;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
class VendorCacheService {
  public function __construct(private CacheInterface $cache) {}
  public function get(string $key, callable $callback, int $ttl=3600) {
    return $this->cache->get($key,function(ItemInterface $item) use ($callback,$ttl){
      $item->expiresAfter($ttl);
      return $callback();
    });
  }
}
