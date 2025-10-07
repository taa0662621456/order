<?php
declare(strict_types=1);
namespace App\Service;
use App\DTO\ReportRequestDTO;
final class ReportService {
    public function createJob(ReportRequestDTO $dto): string {
        return 'job_'.uniqid();
    }
    public function getStatus(string $id): array {
        return ['id'=>$id,'status'=>'completed','file'=>'/var/reports/'.$id.'.csv'];
    }
    public function download(string $id): string {
        return '/var/reports/'.$id.'.csv';
    }
}