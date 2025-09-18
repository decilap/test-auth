<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250918102025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_email_verification_tokens (id UUID NOT NULL, user_id UUID NOT NULL, token_hash VARCHAR(128) NOT NULL, expires_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, consumed_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4B66C24B3BC57DA ON user_email_verification_tokens (token_hash)');
        $this->addSql('CREATE INDEX idx_verification_user ON user_email_verification_tokens (user_id)');
        $this->addSql('COMMENT ON COLUMN user_email_verification_tokens.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_email_verification_tokens.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_email_verification_tokens.expires_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_email_verification_tokens.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_email_verification_tokens.consumed_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('ALTER TABLE user_email_verification_tokens ADD CONSTRAINT FK_C4B66C24A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_email_verification_tokens DROP CONSTRAINT FK_C4B66C24A76ED395');
        $this->addSql('DROP TABLE user_email_verification_tokens');
    }
}
