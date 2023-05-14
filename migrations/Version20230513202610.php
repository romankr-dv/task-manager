<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230513202610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_task_settings DROP FOREIGN KEY FK_4B0BE7D98DB60186');
        $this->addSql('ALTER TABLE user_task_settings DROP FOREIGN KEY FK_4B0BE7D9A76ED395');
        $this->addSql('DROP TABLE user_task_settings');
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_47cc8c92a76ed395 TO IDX_9443C061A76ED395');
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_47cc8c928db60186 TO IDX_9443C0618DB60186');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_task_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, task_id INT NOT NULL, is_additional_panel_open TINYINT(1) NOT NULL, is_children_open TINYINT(1) NOT NULL, INDEX IDX_4B0BE7D9A76ED395 (user_id), INDEX IDX_4B0BE7D98DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_task_settings ADD CONSTRAINT FK_4B0BE7D98DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE user_task_settings ADD CONSTRAINT FK_4B0BE7D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_9443c061a76ed395 TO IDX_47CC8C92A76ED395');
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_9443c0618db60186 TO IDX_47CC8C928DB60186');
    }
}
