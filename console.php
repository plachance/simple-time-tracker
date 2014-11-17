<?php

/**
 * php console.php orm:convert-mapping --from-database --namespace="SimpleTimeTracker\\SimpleTimeTracker\\Entities\\" XML protected/Entities/Metadata/
 * 
 * php console.php orm:generate-entities protected/Entities/
 * 
 * php console.php orm:generate-proxies
 */

use Doctrine\DBAL\Tools\Console\Command\ImportCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
use Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand;
use Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand;
use Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand;
use Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand;
use Doctrine\ORM\Tools\Console\Command\InfoCommand;
use Doctrine\ORM\Tools\Console\Command\RunDqlCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use SimpleTimeTracker\Commands\CreateUserCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$application = new TShellApplication('protected', false, TApplication::CONFIG_TYPE_PHP);
$application->run();

$console = new Application($application->getID());
$em = $application->getModule('doctrine')->getEntityManager();
$helperSet = $console->getHelperSet();
$helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
$helperSet->set(new EntityManagerHelper($em), 'em');
$console->addCommands(array(
	new CreateUserCommand(),
	// DBAL Commands
	new RunSqlCommand(),
	new ImportCommand(),
	// ORM Commands
	new MetadataCommand(),
	new ResultCommand(),
	new QueryCommand(),
	new CreateCommand(),
	new UpdateCommand(),
	new DropCommand(),
	new EnsureProductionSettingsCommand(),
	new ConvertDoctrine1SchemaCommand(),
	new GenerateRepositoriesCommand(),
	new GenerateEntitiesCommand(),
	new GenerateProxiesCommand(),
	new ConvertMappingCommand(),
	new RunDqlCommand(),
	new ValidateSchemaCommand(),
	new InfoCommand(),
));
$console->run();