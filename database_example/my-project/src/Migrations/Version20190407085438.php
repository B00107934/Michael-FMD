<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190407085438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, item_no VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, quantity VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_on VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09E636D3F5');
        $this->addSql('DROP INDEX IDX_52EA1F09E636D3F5 ON order_item');
        $this->addSql('ALTER TABLE order_item ADD ord VARCHAR(255) NOT NULL, DROP ord_id');
        $this->addSql('ALTER TABLE pur_order DROP FOREIGN KEY FK_473E572AA76ED395');
        $this->addSql('DROP INDEX IDX_473E572AA76ED395 ON pur_order');
        $this->addSql('ALTER TABLE pur_order ADD user VARCHAR(255) NOT NULL, DROP user_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bacon');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('ALTER TABLE order_item ADD ord_id INT NOT NULL, DROP ord');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09E636D3F5 FOREIGN KEY (ord_id) REFERENCES pur_order (id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09E636D3F5 ON order_item (ord_id)');
        $this->addSql('ALTER TABLE pur_order ADD user_id INT NOT NULL, DROP user');
        $this->addSql('ALTER TABLE pur_order ADD CONSTRAINT FK_473E572AA76ED395 FOREIGN KEY (user_id) REFERENCES login (id)');
        $this->addSql('CREATE INDEX IDX_473E572AA76ED395 ON pur_order (user_id)');
    }
}
