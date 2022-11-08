<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221108104415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate_skills (candidate_id INT NOT NULL, skills_id INT NOT NULL, INDEX IDX_610248AC91BD8781 (candidate_id), INDEX IDX_610248AC7FF61858 (skills_id), PRIMARY KEY(candidate_id, skills_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, candidate_id INT NOT NULL, job_offer_id INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_E33BD3B891BD8781 (candidate_id), INDEX IDX_E33BD3B83481D195 (job_offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, salary INT NOT NULL, INDEX IDX_288A3A4E979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_offer_skills (job_offer_id INT NOT NULL, skills_id INT NOT NULL, INDEX IDX_307F4FE3481D195 (job_offer_id), INDEX IDX_307F4FE7FF61858 (skills_id), PRIMARY KEY(job_offer_id, skills_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate_skills ADD CONSTRAINT FK_610248AC91BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_skills ADD CONSTRAINT FK_610248AC7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B891BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B83481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id)');
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE job_offer_skills ADD CONSTRAINT FK_307F4FE3481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_offer_skills ADD CONSTRAINT FK_307F4FE7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate_skills DROP FOREIGN KEY FK_610248AC91BD8781');
        $this->addSql('ALTER TABLE candidate_skills DROP FOREIGN KEY FK_610248AC7FF61858');
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B891BD8781');
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B83481D195');
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4E979B1AD6');
        $this->addSql('ALTER TABLE job_offer_skills DROP FOREIGN KEY FK_307F4FE3481D195');
        $this->addSql('ALTER TABLE job_offer_skills DROP FOREIGN KEY FK_307F4FE7FF61858');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE candidate_skills');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE job_offer');
        $this->addSql('DROP TABLE job_offer_skills');
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
