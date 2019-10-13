<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191012214548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB527DAFE17');
        $this->addSql('DROP INDEX IDX_7D8B1FB527DAFE17 ON qr_code');
        $this->addSql('ALTER TABLE qr_code ADD task_id INT NOT NULL, ADD code VARCHAR(255) NOT NULL, DROP code_id');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB58DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_7D8B1FB58DB60186 ON qr_code (task_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB58DB60186');
        $this->addSql('DROP INDEX IDX_7D8B1FB58DB60186 ON qr_code');
        $this->addSql('ALTER TABLE qr_code ADD code_id INT DEFAULT NULL, DROP task_id, DROP code');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB527DAFE17 FOREIGN KEY (code_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_7D8B1FB527DAFE17 ON qr_code (code_id)');
    }
}
