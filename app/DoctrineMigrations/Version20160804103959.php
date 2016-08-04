<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds user.day_length column.
 */
class Version20160804103959 extends AbstractMigration
{
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE "user" ADD day_length DOUBLE PRECISION NOT NULL DEFAULT 7.5');
		$this->addSql('ALTER TABLE "user" ALTER day_length DROP DEFAULT');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE "user" DROP day_length');
	}

}
