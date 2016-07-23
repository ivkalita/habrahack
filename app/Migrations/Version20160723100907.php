<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160723100907 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE app__cities (id SERIAL NOT NULL, name VARCHAR(250) NOT NULL, lat DOUBLE PRECISION DEFAULT NULL, lon DOUBLE PRECISION DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE app__images (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, enabled BOOLEAN NOT NULL, provider_name VARCHAR(255) NOT NULL, provider_status INT NOT NULL, provider_reference VARCHAR(255) NOT NULL, provider_metadata TEXT DEFAULT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, length NUMERIC(10, 0) DEFAULT NULL, content_type VARCHAR(255) DEFAULT NULL, content_size INT DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, author_name VARCHAR(255) DEFAULT NULL, context VARCHAR(64) DEFAULT NULL, cdn_is_flushable BOOLEAN DEFAULT NULL, cdn_flush_identifier VARCHAR(64) DEFAULT NULL, cdn_flush_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cdn_status INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fixture BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN app__images.provider_metadata IS \'(DC2Type:json)\'');
        $this->addSql('CREATE TABLE users__access_tokens (id SERIAL NOT NULL, device_id INT DEFAULT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, player_id VARCHAR(400) DEFAULT NULL, since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_89395D2794A4C7D4 ON users__access_tokens (device_id)');
        $this->addSql('CREATE INDEX IDX_89395D27A76ED395 ON users__access_tokens (user_id)');
        $this->addSql('CREATE TABLE users__devices (id SERIAL NOT NULL, device_id VARCHAR(255) NOT NULL, platform VARCHAR(255) CHECK(platform IN (\'android\', \'ios\')) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E0E843D94A4C7D4 ON users__devices (device_id)');
        $this->addSql('COMMENT ON COLUMN users__devices.platform IS \'(DC2Type:PlatformType)\'');
        $this->addSql('CREATE TABLE users__phones (id SERIAL NOT NULL, user_id INT DEFAULT NULL, phone VARCHAR(255) NOT NULL, since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA1BBDBCA76ED395 ON users__phones (user_id)');
        $this->addSql('CREATE TABLE users__users (id SERIAL NOT NULL, city_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, birthday DATE DEFAULT NULL, secret VARCHAR(255) DEFAULT NULL, sms_code VARCHAR(255) DEFAULT NULL, sms_code_dt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, gender VARCHAR(255) CHECK(gender IN (\'male\', \'female\')) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_915CC7E58BAC62AF ON users__users (city_id)');
        $this->addSql('COMMENT ON COLUMN users__users.gender IS \'(DC2Type:GenderType)\'');
        $this->addSql('CREATE TABLE articles__articles (id SERIAL NOT NULL, title VARCHAR(1000) NOT NULL, video_url VARCHAR(3000) NOT NULL, placeholder_url VARCHAR(3000) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE users__access_tokens ADD CONSTRAINT FK_89395D2794A4C7D4 FOREIGN KEY (device_id) REFERENCES users__devices (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users__access_tokens ADD CONSTRAINT FK_89395D27A76ED395 FOREIGN KEY (user_id) REFERENCES users__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users__phones ADD CONSTRAINT FK_EA1BBDBCA76ED395 FOREIGN KEY (user_id) REFERENCES users__users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users__users ADD CONSTRAINT FK_915CC7E58BAC62AF FOREIGN KEY (city_id) REFERENCES app__cities (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users__users DROP CONSTRAINT FK_915CC7E58BAC62AF');
        $this->addSql('ALTER TABLE users__access_tokens DROP CONSTRAINT FK_89395D2794A4C7D4');
        $this->addSql('ALTER TABLE users__access_tokens DROP CONSTRAINT FK_89395D27A76ED395');
        $this->addSql('ALTER TABLE users__phones DROP CONSTRAINT FK_EA1BBDBCA76ED395');
        $this->addSql('DROP TABLE app__cities');
        $this->addSql('DROP TABLE app__images');
        $this->addSql('DROP TABLE users__access_tokens');
        $this->addSql('DROP TABLE users__devices');
        $this->addSql('DROP TABLE users__phones');
        $this->addSql('DROP TABLE users__users');
        $this->addSql('DROP TABLE articles__articles');
    }
}
