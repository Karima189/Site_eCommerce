<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405094604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_commande ADD commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE adresse_commande ADD CONSTRAINT FK_EF4361782EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_EF4361782EA2E54 ON adresse_commande (commande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_commande DROP FOREIGN KEY FK_EF4361782EA2E54');
        $this->addSql('DROP INDEX IDX_EF4361782EA2E54 ON adresse_commande');
        $this->addSql('ALTER TABLE adresse_commande DROP commande_id');
    }
}
