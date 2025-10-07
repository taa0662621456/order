<?php
declare(strict_types=1);

namespace OrderComponent\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202510062126_baseline extends AbstractMigration
{
    public function getDescription(): string: return 'Version202510062126_baseline: init tables orders, order_items, order_payments, order_shipments, outbox_messages, analytics_records';

    public function up(Schema $schema): void
    {
        // orders
        $this->addSql('CREATE TABLE IF NOT EXISTS orders (id SERIAL PRIMARY KEY, status VARCHAR(16) NOT NULL, currency VARCHAR(3) NOT NULL, subtotal INT NOT NULL DEFAULT 0, discount_total INT NOT NULL DEFAULT 0, tax_total INT NOT NULL DEFAULT 0, grand_total INT NOT NULL DEFAULT 0, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL)');
        // order_items
        $this->addSql('CREATE TABLE IF NOT EXISTS order_items (id SERIAL PRIMARY KEY, order_id INT NOT NULL, sku VARCHAR(64) NOT NULL, unit_price INT NOT NULL, quantity INT NOT NULL, discount INT NOT NULL DEFAULT 0, tax INT NOT NULL DEFAULT 0, final_price INT NOT NULL DEFAULT 0, CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_items_order ON order_items (order_id)');

        // order_payments
        $this->addSql('CREATE TABLE IF NOT EXISTS order_payments (id SERIAL PRIMARY KEY, order_id INT NOT NULL, gateway VARCHAR(32) NOT NULL, status VARCHAR(16) NOT NULL, amount INT NOT NULL, CONSTRAINT fk_pay_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_pay_order ON order_payments (order_id)');

        // order_shipments
        $this->addSql('CREATE TABLE IF NOT EXISTS order_shipments (id SERIAL PRIMARY KEY, order_id INT NOT NULL, carrier VARCHAR(32) NOT NULL, tracking_number VARCHAR(64) NULL, status VARCHAR(16) NOT NULL, CONSTRAINT fk_ship_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_ship_order ON order_shipments (order_id)');

        // outbox_messages
        $this->addSql('CREATE TABLE IF NOT EXISTS outbox_messages (id SERIAL PRIMARY KEY, event_name VARCHAR(128) NOT NULL, payload TEXT NOT NULL, idempotency_key VARCHAR(64) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_outbox_idem ON outbox_messages (idempotency_key)');

        // analytics_records
        $this->addSql('CREATE TABLE IF NOT EXISTS analytics_records (id SERIAL PRIMARY KEY, type VARCHAR(64) NOT NULL, order_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL)');
    }

    public function down(Schema $schema): void
    {   // intentionally empty for baseline }
}
