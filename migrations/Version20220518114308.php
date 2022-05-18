<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220518114308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cell (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, map_id INT NOT NULL, type VARCHAR(50) NOT NULL, coord_X INT NOT NULL, coord_Y INT NOT NULL, UNIQUE INDEX UNIQ_CB8787E299E6F5DF (player_id), INDEX IDX_CB8787E253C55F64 (map_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, active_player_turn INT DEFAULT NULL, map_id INT DEFAULT NULL, state VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_232B318CD6C5B03F (active_player_turn), UNIQUE INDEX UNIQ_232B318C53C55F64 (map_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (game_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_264E43A6E48FD905 (game_id), UNIQUE INDEX UNIQ_264E43A699E6F5DF (player_id), PRIMARY KEY(game_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map (id INT AUTO_INCREMENT NOT NULL, height INT NOT NULL, width INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cell ADD CONSTRAINT FK_CB8787E299E6F5DF FOREIGN KEY (player_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE cell ADD CONSTRAINT FK_CB8787E253C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CD6C5B03F FOREIGN KEY (active_player_turn) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C53C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A6E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A699E6F5DF FOREIGN KEY (player_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A6E48FD905');
        $this->addSql('ALTER TABLE cell DROP FOREIGN KEY FK_CB8787E253C55F64');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C53C55F64');
        $this->addSql('ALTER TABLE cell DROP FOREIGN KEY FK_CB8787E299E6F5DF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CD6C5B03F');
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A699E6F5DF');
        $this->addSql('DROP TABLE cell');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE players');
        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE participant');
    }
}
