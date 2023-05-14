<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210530161733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP deleted_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task ADD deleted_at DATETIME DEFAULT NULL');
    }
}
