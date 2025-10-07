<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251006_somecomment extends AbstractMigration
{
    public function getDescription(): string { return 'Init orders, order_items, order_payments, order_shipments'; }

    public function up(Schema $schema): void
    {
        // orders
        $this->addSql("CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, status VARCHAR(32) NOT NULL, CONSTRAINT orders_uuid_unique UNIQUE (uuid))");
        // order_items
        $this->addSql("CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER NOT NULL, CONSTRAINT order_items_order_fk FOREIGN KEY(order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE)");
        // order_payments
        $this->addSql("CREATE TABLE order_payments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER NOT NULL, CONSTRAINT order_payments_order_fk FOREIGN KEY(order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE)");
        // order_shipments
        $this->addSql("CREATE TABLE order_shipments (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER NOT NULL, CONSTRAINT order_shipments_order_fk FOREIGN KEY(order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE)");
        // indexes
        $this->addSql("CREATE INDEX idx_orders_status ON orders (status)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE order_shipments");
        $this->addSql("DROP TABLE order_payments");
        $this->addSql("DROP TABLE order_items");
        $this->addSql("DROP TABLE orders");
    }
}
