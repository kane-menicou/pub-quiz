<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302164248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quiz_id INTEGER NOT NULL, name VARCHAR(16) NOT NULL, answers CLOB NOT NULL --(DC2Type:json)
        , CONSTRAINT FK_D79F6B11853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D79F6B11853CD175 ON participant (quiz_id)');
        $this->addSql('CREATE TABLE quiz (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, friendly_id VARCHAR(6) NOT NULL, question_set VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, current_question INTEGER NOT NULL, last_question_start DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , seconds_per_question INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE quiz');
    }
}
