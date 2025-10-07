<?php
declare(strict_types=1);
namespace App\Service\Cms;
use App\DTO\BlockDTO;
use App\DTO\PageDTO;
final class CmsService {
    public function createPage(PageDTO $dto): string { return 'page_'.uniqid(); }
    public function createBlock(BlockDTO $dto): string { return 'block_'.uniqid(); }
}
