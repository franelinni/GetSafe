<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828030453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE download_history (
            id INT AUTO_INCREMENT NOT NULL, 
            image_id INT NOT NULL, 
            status VARCHAR(255) NOT NULL, 
            retries INT NOT NULL, 
            log_message VARCHAR(255) NOT NULL, 
            error_message VARCHAR(255) DEFAULT NULL, 
            started_at DATETIME NOT NULL, 
            downloaded_at DATETIME DEFAULT NULL, 
            restarted_at DATETIME DEFAULT NULL, 
            UNIQUE INDEX unique_image_id (image_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (
            id INT AUTO_INCREMENT NOT NULL, 
            url VARCHAR(255) NOT NULL, 
            size INT DEFAULT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME DEFAULT NULL, 
            UNIQUE INDEX unique_url (url), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE input_file (
            id INT AUTO_INCREMENT NOT NULL, 
            filename VARCHAR(255) NOT NULL, 
            source VARCHAR(255) NOT NULL, 
            size INT DEFAULT NULL, 
            n_lines INT DEFAULT NULL, 
            destination VARCHAR(255) NOT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME DEFAULT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE input_file_image_relation (
            id INT AUTO_INCREMENT NOT NULL, 
            input_file_id INT NOT NULL, 
            image_id INT NOT NULL, 
            UNIQUE INDEX unique_input_file_id_image_file_id (input_file_id, image_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validation_log (
            id INT AUTO_INCREMENT NOT NULL, 
            image_id INT NOT NULL, 
            is_valid TINYINT(1) NOT NULL, 
            error_message VARCHAR(255) DEFAULT NULL, 
            created_at DATETIME NOT NULL, 
            updated_at DATETIME DEFAULT NULL, 
            UNIQUE INDEX unique_image_id (image_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_history ADD CONSTRAINT FK_8BC4718E3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE validation_log ADD CONSTRAINT FK_8F2276EA3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE download_history DROP FOREIGN KEY FK_8BC4718E3DA5256D');
        $this->addSql('ALTER TABLE validation_log DROP FOREIGN KEY FK_8F2276EA3DA5256D');
        $this->addSql('DROP TABLE download_history');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE input_file');
        $this->addSql('DROP TABLE input_file_image_relation');
        $this->addSql('DROP TABLE validation_log');
    }
}
