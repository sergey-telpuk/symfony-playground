<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212193349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "games" (id UUID NOT NULL, team_one_id UUID DEFAULT NULL, team_two_id UUID DEFAULT NULL, winner_id UUID DEFAULT NULL, team_one_score INT NOT NULL, team_two_score INT NOT NULL, game_type VARCHAR(255) NOT NULL, played_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF232B318D8189CA ON "games" (team_one_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31E6DD6E05 ON "games" (team_two_id)');
        $this->addSql('CREATE INDEX IDX_FF232B315DFCD4B8 ON "games" (winner_id)');
        $this->addSql('COMMENT ON COLUMN "games".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "games".team_one_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "games".team_two_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "games".winner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "teams" (id UUID NOT NULL, name VARCHAR(255) NOT NULL, division VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "teams".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "games" ADD CONSTRAINT FK_FF232B318D8189CA FOREIGN KEY (team_one_id) REFERENCES "teams" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "games" ADD CONSTRAINT FK_FF232B31E6DD6E05 FOREIGN KEY (team_two_id) REFERENCES "teams" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "games" ADD CONSTRAINT FK_FF232B315DFCD4B8 FOREIGN KEY (winner_id) REFERENCES "teams" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "games" DROP CONSTRAINT FK_FF232B318D8189CA');
        $this->addSql('ALTER TABLE "games" DROP CONSTRAINT FK_FF232B31E6DD6E05');
        $this->addSql('ALTER TABLE "games" DROP CONSTRAINT FK_FF232B315DFCD4B8');
        $this->addSql('DROP TABLE "games"');
        $this->addSql('DROP TABLE "teams"');
    }
}
