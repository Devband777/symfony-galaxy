<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405200719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atom (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galaxy (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, atoms LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE star (id INT AUTO_INCREMENT NOT NULL, galaxy_id INT NOT NULL, name VARCHAR(255) NOT NULL, radius DOUBLE PRECISION NOT NULL, temperature DOUBLE PRECISION NOT NULL, rotation_frequency DOUBLE PRECISION NOT NULL, atoms LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_C9DB5A14B61FAB2 (galaxy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE star ADD CONSTRAINT FK_C9DB5A14B61FAB2 FOREIGN KEY (galaxy_id) REFERENCES galaxy (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE star DROP FOREIGN KEY FK_C9DB5A14B61FAB2');
        $this->addSql('DROP TABLE atom');
        $this->addSql('DROP TABLE galaxy');
        $this->addSql('DROP TABLE star');
    }
}
