<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220501192236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD small_description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6496b9dd454');
        $this->addSql('DROP INDEX uniq_8d93d6496b9dd454');
        $this->addSql('ALTER TABLE "user" DROP user_profile_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article DROP small_description');
        $this->addSql('ALTER TABLE article DROP image');
        $this->addSql('ALTER TABLE "user" ADD user_profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6496b9dd454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d6496b9dd454 ON "user" (user_profile_id)');
    }
}
