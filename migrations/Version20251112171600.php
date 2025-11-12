<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251112171600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ограничение длины поля payment.note до 14 символов';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment CHANGE note note VARCHAR(14) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment CHANGE note note VARCHAR(255) DEFAULT NULL');
    }
}
