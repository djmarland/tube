<?php

namespace TubeService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151212213849 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, line_id INT DEFAULT NULL, is_disrupted TINYINT(1) NOT NULL, short_title VARCHAR(255) NOT NULL, title VARCHAR(1000) NOT NULL, description VARCHAR(10000) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7B00651C4D7B7542 (line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651C4D7B7542 FOREIGN KEY (line_id) REFERENCES tube_lines (id)');
        $this->addSql('ALTER TABLE tube_lines ADD latest_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tube_lines ADD CONSTRAINT FK_99A7CE23B1CC65A1 FOREIGN KEY (latest_status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_99A7CE23B1CC65A1 ON tube_lines (latest_status_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tube_lines DROP FOREIGN KEY FK_99A7CE23B1CC65A1');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP INDEX IDX_99A7CE23B1CC65A1 ON tube_lines');
        $this->addSql('ALTER TABLE tube_lines DROP latest_status_id');
    }
}
