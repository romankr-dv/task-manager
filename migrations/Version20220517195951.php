<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220517195951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, task_id INT DEFAULT NULL, created_at DATETIME NOT NULL, type VARCHAR(20) NOT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_47CC8C92A76ED395 (user_id), INDEX IDX_47CC8C928DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C928DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE action');
    }
}
