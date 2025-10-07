<?php
declare(strict_types=1);
namespace App\Service\Search;
use App\DTO\AutocompleteDTO;
use App\DTO\SearchQueryDTO;
final class SearchService {
    public function runQuery(SearchQueryDTO $dto): array {
        // stub: return empty results with paging meta
        return ['items'=>[], 'total'=>0, 'page'=>$dto->page, 'perPage'=>$dto->perPage, 'sort'=>$dto->sort];
    }
    public function autocomplete(AutocompleteDTO $dto): array {
        // stub suggestions
        return ['suggestions'=>[]];
    }
}
