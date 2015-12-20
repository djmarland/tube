<?php

namespace TubeService\Data\Database\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151212164332 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `tube_lines` (id INT AUTO_INCREMENT NOT NULL, url_key VARCHAR(255) NOT NULL, tfl_key VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, short_name VARCHAR(255) NOT NULL, display_order INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE `tube_lines`');
    }


    public function postUp(Schema $schema)
    {
        $lineData = [
            ['bakerloo-line', 'bakerloo', 'Bakerloo', 'Bakerloo Line'],
            ['central-line', 'central', 'Central', 'Central Line'],
            ['circle-line', 'circle', 'Circle', 'Circle Line'],
            ['district-line', 'district', 'District', 'District Line'],
            ['hammersmith-city-line', 'hammersmith-city', 'Hammersmith & City', 'Hammersmith & City Line'],
            ['jubilee-line', 'jubilee', 'Jubilee', 'Jubilee Line'],
            ['metropolitan-line', 'metropolitan', 'Metropolitan', 'Metropolitan Line'],
            ['northern-line', 'northern', 'Northern', 'Northern Line'],
            ['piccadilly-line', 'piccadilly', 'Piccadilly', 'Piccadilly Line'],
            ['victoria-line', 'victoria', 'Victoria', 'Victoria Line'],
            ['waterloo-city-line', 'waterloo-city', 'Waterloo & City', 'Waterloo & City Line'],
            ['dlr', 'dlr', 'DLR', 'DLR'],
            ['london-overground', 'london-overground', 'London Overground', 'London Overground'],
            ['tfl-rail', 'tfl-rail', 'TFL Rail', 'TFL Rail'],
        ];

        $now = new \DateTimeImmutable();
        $iso = $now->format('c');

        foreach($lineData as $i => $line) {
            $this->addSql(
                'INSERT INTO `tube_lines` (url_key, tfl_key, short_name, name, display_order, created_at, updated_at) VALUES (?,?,?,?,?,?,?)',
                [
                    $line[0],
                    $line[1],
                    $line[2],
                    $line[3],
                    ($i+1),
                    $iso,
                    $iso
                ]
            );
        }
    }
}
