<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821103053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE download_history (id INT AUTO_INCREMENT NOT NULL, image_id_id INT NOT NULL, status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', error_message VARCHAR(255) DEFAULT NULL, n_retries INT NOT NULL, last_retry_at DATETIME NOT NULL, downloaded_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8BC4718E68011AFE (image_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_history ADD CONSTRAINT FK_8BC4718E68011AFE FOREIGN KEY (image_id_id) REFERENCES image (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE download_history DROP FOREIGN KEY FK_8BC4718E68011AFE');
        $this->addSql('DROP TABLE download_history');
    }
}
