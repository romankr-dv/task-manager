<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230513225959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracked_period DROP FOREIGN KEY FK_48A4EF168DB60186');
        $this->addSql('ALTER TABLE tracked_period DROP FOREIGN KEY FK_48A4EF16A76ED395');
        $this->addSql('DROP TABLE tracked_period');
        $this->addSql('ALTER TABLE task DROP tracked_time, DROP children_tracked_time');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tracked_period (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, user_id INT NOT NULL, started_at DATETIME NOT NULL, finished_at DATETIME DEFAULT NULL, INDEX IDX_48A4EF168DB60186 (task_id), INDEX IDX_48A4EF16A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tracked_period ADD CONSTRAINT FK_48A4EF168DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE tracked_period ADD CONSTRAINT FK_48A4EF16A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD tracked_time INT DEFAULT 0 NOT NULL, ADD children_tracked_time INT DEFAULT 0 NOT NULL');
    }
}
