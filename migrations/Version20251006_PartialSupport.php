<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251006_PartialSupport extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Partial payment/refund/shipment tables and order status columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE orders ADD paid_total NUMERIC(12,2) NOT NULL DEFAULT 0, ADD refunded_total NUMERIC(12,2) NOT NULL DEFAULT 0, ADD status VARCHAR(32) NOT NULL DEFAULT 'draft'");

        $this->addSql("CREATE TABLE order_payment (
            id SERIAL PRIMARY KEY,
            order_id UUID NOT NULL,
            amount NUMERIC(12,2) NOT NULL,
            currency VARCHAR(3) NOT NULL,
            external_ref VARCHAR(64) NOT NULL UNIQUE,
            is_partial BOOLEAN NOT NULL DEFAULT TRUE,
            captured_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT fk_payment_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
        )");

        $this->addSql("CREATE TABLE order_refund (
            id SERIAL PRIMARY KEY,
            order_id UUID NOT NULL,
            amount NUMERIC(12,2) NOT NULL,
            currency VARCHAR(3) NOT NULL,
            reason VARCHAR(128),
            is_partial BOOLEAN NOT NULL DEFAULT TRUE,
            refunded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT fk_refund_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
        )");

        $this->addSql("CREATE TABLE order_shipment_item (
            id SERIAL PRIMARY KEY,
            order_id UUID NOT NULL,
            quantity INT NOT NULL,
            note VARCHAR(64),
            CONSTRAINT fk_shipment_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
        )");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE orders DROP COLUMN paid_total, DROP COLUMN refunded_total, DROP COLUMN status");
        $this->addSql("DROP TABLE order_payment");
        $this->addSql("DROP TABLE order_refund");
        $this->addSql("DROP TABLE order_shipment_item");
    }
}
