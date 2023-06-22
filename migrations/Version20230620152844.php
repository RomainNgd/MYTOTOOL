<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230620152844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA144E81F03D');
        $this->addSql('ALTER TABLE parts DROP FOREIGN KEY FK_6940A7FE166D1F9C');
        $this->addSql('DROP TABLE parts');
        $this->addSql('DROP INDEX IDX_CFBDFA144E81F03D ON note');
        $this->addSql('ALTER TABLE note CHANGE parts_id project_id INT NOT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14166D1F9C ON note (project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parts (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6940A7FE166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE parts ADD CONSTRAINT FK_6940A7FE166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14166D1F9C');
        $this->addSql('DROP INDEX IDX_CFBDFA14166D1F9C ON note');
        $this->addSql('ALTER TABLE note CHANGE project_id parts_id INT NOT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA144E81F03D FOREIGN KEY (parts_id) REFERENCES parts (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CFBDFA144E81F03D ON note (parts_id)');
    }
}
