<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250109184123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates exchange_rate table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE binance_exchange_rate (
                id INT AUTO_INCREMENT NOT NULL,
                currency_pair VARCHAR(20) NOT NULL,
                rate DOUBLE PRECISION NOT NULL,
                timestamp DATETIME NOT NULL,
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE INDEX idx_currency_pair ON exchange_rate (currency_pair)');
        $this->addSql('CREATE INDEX idx_timestamp ON exchange_rate (timestamp)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE binance_exchange_rate');
    }
}
