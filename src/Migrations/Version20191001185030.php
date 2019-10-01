<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191001185030 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE qr_code_task');
        $this->addSql('ALTER TABLE qr_code ADD code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB527DAFE17 FOREIGN KEY (code_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_7D8B1FB527DAFE17 ON qr_code (code_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE qr_code_task (qr_code_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_A8F798498DB60186 (task_id), INDEX IDX_A8F7984912E4AD80 (qr_code_id), PRIMARY KEY(qr_code_id, task_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE qr_code_task ADD CONSTRAINT FK_A8F7984912E4AD80 FOREIGN KEY (qr_code_id) REFERENCES qr_code (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qr_code_task ADD CONSTRAINT FK_A8F798498DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB527DAFE17');
        $this->addSql('DROP INDEX IDX_7D8B1FB527DAFE17 ON qr_code');
        $this->addSql('ALTER TABLE qr_code DROP code_id');
    }
}
