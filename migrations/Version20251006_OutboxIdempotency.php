<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251006_OutboxIdempotency extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create outbox_message and idempotency_key tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE outbox_message (
            id BIGSERIAL PRIMARY KEY,
            topic VARCHAR(128) NOT NULL,
            payload JSON NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            published BOOLEAN NOT NULL DEFAULT FALSE
        )");

        $this->addSql("CREATE TABLE idempotency_key (
            key VARCHAR(128) PRIMARY KEY,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE outbox_message");
        $this->addSql("DROP TABLE idempotency_key");
    }
}
