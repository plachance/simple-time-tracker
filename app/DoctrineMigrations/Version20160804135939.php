<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Delete task.description.
 */
class Version20160804135939 extends AbstractMigration
{
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task DROP description');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ADD description VARCHAR(255) DEFAULT NULL');
	}

}
