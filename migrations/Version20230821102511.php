<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821102511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, input_file_id_id INT NOT NULL, url VARCHAR(255) DEFAULT NULL, size INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_C53D045F103F3C37 (input_file_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE input_file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, size INT DEFAULT NULL, n_lines INT DEFAULT NULL, destination_folder VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validation_log (id INT AUTO_INCREMENT NOT NULL, input_file_id_id INT NOT NULL, image_id_id INT NOT NULL, is_valid TINYINT(1) DEFAULT NULL, error_mesage VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8F2276EA103F3C37 (input_file_id_id), UNIQUE INDEX UNIQ_8F2276EA68011AFE (image_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F103F3C37 FOREIGN KEY (input_file_id_id) REFERENCES input_file (id)');
        $this->addSql('ALTER TABLE validation_log ADD CONSTRAINT FK_8F2276EA103F3C37 FOREIGN KEY (input_file_id_id) REFERENCES input_file (id)');
        $this->addSql('ALTER TABLE validation_log ADD CONSTRAINT FK_8F2276EA68011AFE FOREIGN KEY (image_id_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F103F3C37');
        $this->addSql('ALTER TABLE validation_log DROP FOREIGN KEY FK_8F2276EA103F3C37');
        $this->addSql('ALTER TABLE validation_log DROP FOREIGN KEY FK_8F2276EA68011AFE');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE input_file');
        $this->addSql('DROP TABLE validation_log');
    }
}
