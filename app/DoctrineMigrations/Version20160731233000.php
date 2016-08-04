<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add pinned project feature.
 */
class Version20160731233000 extends AbstractMigration
{
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE project ADD pinned BOOLEAN NOT NULL DEFAULT false');
		$this->addSql('ALTER TABLE project ALTER pinned DROP DEFAULT');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema)
	{
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql',
			'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE project DROP pinned');
	}

}
