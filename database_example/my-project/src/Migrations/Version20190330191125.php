<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190330191125 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pur_order ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE pur_order ADD CONSTRAINT FK_473E572AA76ED395 FOREIGN KEY (user_id) REFERENCES login (id)');
        $this->addSql('CREATE INDEX IDX_473E572AA76ED395 ON pur_order (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pur_order DROP FOREIGN KEY FK_473E572AA76ED395');
        $this->addSql('DROP INDEX IDX_473E572AA76ED395 ON pur_order');
        $this->addSql('ALTER TABLE pur_order DROP user_id');
    }
}
