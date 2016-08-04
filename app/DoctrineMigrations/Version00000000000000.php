<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Schema creation.
 */
class Version00000000000000 extends AbstractMigration
{
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE task_task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE "user_user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE project_project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE task (task_id INT NOT NULL, project_id INT NOT NULL, user_id INT NOT NULL, date_time_begin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_time_end TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(task_id))');
		$this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
		$this->addSql('CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)');
		$this->addSql('CREATE TABLE "user" (user_id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON DEFAULT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(user_id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
		$this->addSql('CREATE TABLE project (project_id INT NOT NULL, user_id INT DEFAULT NULL, no INT NOT NULL, description VARCHAR(255) DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(project_id))');
		$this->addSql('CREATE INDEX IDX_2FB3D0EEA76ED395 ON project (user_id)');
		$this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25A76ED395');
		$this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EEA76ED395');
		$this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25166D1F9C');
		$this->addSql('DROP SEQUENCE task_task_id_seq CASCADE');
		$this->addSql('DROP SEQUENCE "user_user_id_seq" CASCADE');
		$this->addSql('DROP SEQUENCE project_project_id_seq CASCADE');
		$this->addSql('DROP TABLE task');
		$this->addSql('DROP TABLE "user"');
		$this->addSql('DROP TABLE project');
	}

}
