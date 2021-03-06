<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140108103543 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs

		$this->addSql("ALTER TABLE  `test_paper_result` CHANGE  `finishedTime`  `limitedTime` INT( 11 ) NOT NULL COMMENT  '试卷限制时间(秒)'");

		$this->addSql("ALTER TABLE  `test_paper_result` CHANGE  `score`  `score` INT( 11 ) NOT NULL DEFAULT  '0.0' COMMENT  '分数'");

		$this->addSql("ALTER TABLE  `test_paper_result` CHANGE  `status`  `status` ENUM(  'doing',  'paused',  'reviewing',  'finished' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '状态'");
		
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
