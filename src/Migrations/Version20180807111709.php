<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180807111709 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_14B784189D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, photo_id_id INT DEFAULT NULL, text LONGTEXT NOT NULL, INDEX IDX_9474526C9D86650F (user_id_id), INDEX IDX_9474526CC51599E0 (photo_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_like (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, photo_id_id INT DEFAULT NULL, INDEX IDX_2D52F2479D86650F (user_id_id), INDEX IDX_2D52F247C51599E0 (photo_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B784189D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC51599E0 FOREIGN KEY (photo_id_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE photo_like ADD CONSTRAINT FK_2D52F2479D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photo_like ADD CONSTRAINT FK_2D52F247C51599E0 FOREIGN KEY (photo_id_id) REFERENCES photo (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC51599E0');
        $this->addSql('ALTER TABLE photo_like DROP FOREIGN KEY FK_2D52F247C51599E0');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE photo_like');
    }
}
