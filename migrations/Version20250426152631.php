<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426152631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, manager_id INT NOT NULL, employee_id INT NOT NULL, date_evaluation DATETIME NOT NULL, periode VARCHAR(20) NOT NULL, note_globale DOUBLE PRECISION NOT NULL, statut VARCHAR(20) NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, INDEX IDX_1323A575783E3463 (manager_id), INDEX IDX_1323A5758C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT NOT NULL, manager_id INT NOT NULL, employee_id INT NOT NULL, commentaire LONGTEXT NOT NULL, feedback LONGTEXT NOT NULL, INDEX IDX_D2294458456C5646 (evaluation_id), INDEX IDX_D2294458783E3463 (manager_id), INDEX IDX_D22944588C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, destinataire_id INT NOT NULL, message VARCHAR(255) NOT NULL, seen TINYINT(1) NOT NULL, INDEX IDX_BF5476CAA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE objectif (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, date_echeance DATETIME NOT NULL, poid DOUBLE PRECISION DEFAULT '0' NOT NULL, INDEX IDX_E2F86851783E3463 (manager_id), INDEX IDX_E2F868518C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE objectif_note (id INT AUTO_INCREMENT NOT NULL, objectif_id INT NOT NULL, evaluation_id INT NOT NULL, note DOUBLE PRECISION NOT NULL, commentaire LONGTEXT DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_BEE6009E157D1AD4 (objectif_id), INDEX IDX_BEE6009E456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, m_id INT DEFAULT NULL, statut VARCHAR(20) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5758C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D2294458456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D2294458783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback ADD CONSTRAINT FK_D22944588C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif ADD CONSTRAINT FK_E2F86851783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif ADD CONSTRAINT FK_E2F868518C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif_note ADD CONSTRAINT FK_BEE6009E157D1AD4 FOREIGN KEY (objectif_id) REFERENCES objectif (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif_note ADD CONSTRAINT FK_BEE6009E456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575783E3463
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5758C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458456C5646
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D2294458783E3463
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE feedback DROP FOREIGN KEY FK_D22944588C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA4F84F6E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif DROP FOREIGN KEY FK_E2F86851783E3463
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif DROP FOREIGN KEY FK_E2F868518C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif_note DROP FOREIGN KEY FK_BEE6009E157D1AD4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objectif_note DROP FOREIGN KEY FK_BEE6009E456C5646
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE evaluation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE feedback
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE objectif
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE objectif_note
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
