<?php

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Proxy\Autoloader;
use Doctrine\ORM\Tools\Setup;

Prado::using('Application.Data.XDoctrineDriverType');

/**
 * Class XDoctrine.
 *
 * Doctrine module for Prado.
 *
 * @author Patrice Lachance <patricelachance@gmail.com>
 */
class XDoctrine extends TModule
{
	/**
	 * @var string[] Paths of Doctrine metadata files relative to Prado basepath.
	 */
	private $metadataPaths;

	/**
	 * @var XDoctrineDriverType Doctrine driver type.
	 */
	private $driverType = XDoctrineDriverType::Xml;

	/**
	 * @var string[] Namespaces of entities. 
	 */
	private $entityNamespaces;

	/**
	 * @var string Proxy directory.
	 */
	private $proxyDir = 'doctrine';

	/**
	 * @var string Namespace where proxy classes reside.
	 */
	private $proxyNamespace = 'Application\Doctrine\Proxies';

	/**
	 * @var string Connection module ID
	 */
	private $connectionID = '';

	/**
	 * @var EntityManager Doctrine entity manager.
	 */
	private $entityManager;

	/**
	 * @var string|null SQL logger class name.
	 */
	private $sqlLoggerClass;

	/**
	 * @var string|null Doctrine cache class name.
	 */
	private $cacheClass;

	/**
	 * @return string[] Paths of Doctrine metadata files (absolute or relative to Prado basepath).
	 */
	public function getMetadataPaths()
	{
		return $this->metadataPaths;
	}

	/**
	 * @param string[] $value Paths of Doctrine metadata files (absolute or relative to Prado basepath).
	 */
	public function setMetadataPaths($value)
	{
		$this->metadataPaths = TPropertyValue::ensureArray($value);

		$basePath = $this->getApplication()->getBasePath();
		foreach($this->metadataPaths as &$path)
		{
			if(strpos($path, DIRECTORY_SEPARATOR) !== 0)
			{
				$path = $basePath . DIRECTORY_SEPARATOR . $path;
			}
		}
	}

	/**
	 * @return XDoctrineDriverType Doctrine driver type.
	 */
	public function getDriverType()
	{
		return $this->driverType;
	}

	/**
	 * @param XDoctrineDriverType $value Doctrine driver type.
	 */
	public function setDriverType($value)
	{
		$this->driverType = TPropertyValue::ensureEnum($value, 'XDoctrineDriverType');
	}

	/**
	 * @return string[] Namespaces of entities. 
	 */
	public function getEntityNamespaces()
	{
		return $this->entityNamespaces;
	}

	/**
	 * @param string[] $value Namespaces of entities. 
	 */
	public function setEntityNamespaces($value)
	{
		$this->entityNamespaces = TPropertyValue::ensureArray($value);
	}

	/**
	 * @return string Proxy directory.
	 */
	public function getProxyDir()
	{
		return $this->proxyDir;
	}

	/**
	 * @param string $value Proxy directory (absolute or relative to protected/runtime). Do not set it inside protected/runtime/<config_filename>-<prado_version>/.
	 * @
	 */
	public function setProxyDir($value)
	{
		if($value != null && strpos($value, DIRECTORY_SEPARATOR) !== 0)
		{
			$value = $this->getApplication()->getRuntimePath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $value;
		}

		$this->proxyDir = $value;
	}

	/**
	 * @return string Namespace where proxy classes reside.
	 */
	public function getProxyNamespace()
	{
		return $this->proxyNamespace;
	}

	/**
	 * @param string $value Namespace where proxy classes reside.
	 */
	public function setProxyNamespace($value)
	{
		$this->proxyNamespace = $value;
	}

	/**
	 * @return string Connection module ID.
	 */
	public function getConnectionID()
	{
		return $this->connectionID;
	}

	/**
	 * The module ID of another TDataSourceConfig. The {@link getDbConnection DbConnection}
	 * property of this configuration will equal to {@link getDbConnection DbConnection}
	 * of the given TDataSourceConfig module.
	 * @param string $value Module ID.
	 */
	public function setConnectionID($value)
	{
		$this->connectionID = $value;
	}

	/**
	 * @return EntityManager Doctrine entity manager.
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @return string|null SQL logger class name.
	 */
	public function getSqlLoggerClass()
	{
		return $this->sqlLoggerClass;
	}

	/**
	 * @param string|null $value SQL logger class name.
	 */
	public function setSqlLoggerClass($value)
	{
		$this->sqlLoggerClass = TPropertyValue::ensureNullIfEmpty($value);
	}

	/**
	 * @return string|null Doctrine cache class name.
	 */
	public function getCacheClass()
	{
		return $this->cacheClass;
	}

	/**
	 * @param string|null $value Doctrine cache class name.
	 */
	public function setCacheClass($value)
	{
		$this->cacheClass = TPropertyValue::ensureNullIfEmpty($value);
	}

	public function __construct()
	{
		parent::__construct();

		$this->setProxyDir('doctrine');
	}

	/**
	 * Create proxy directory if it doesn't exists and ensure it's writable.
	 * @throws TConfigurationException if unable to create directory or directory is not writable.
	 */
	protected function ensureProxyDirExists()
	{
		$this->proxyDir;
		if(!is_dir($this->proxyDir))
		{
			if(@mkdir($this->proxyDir, PRADO_CHMOD) === false)
			{
				throw new TConfigurationException(Prado::localize('Unable to create proxy directory "{dir}".', array('dir' => $this->proxyDir)));
			}
		}
		else if(!is_writable($this->proxyDir))
		{
			throw new TConfigurationException(Prado::localize('Unable to create proxy directory "{dir}".', array('dir' => $this->proxyDir)));
		}
	}

	/**
	 * @return PDO PDO instance.
	 */
	protected function getPdoInstance()
	{
		$con = $this->findConnectionByID($this->getConnectionID());
		$pdo = $con->getPdoInstance();
		if($pdo === null)
		{
			$con->setActive(true);
			$pdo = $con->getPdoInstance();
		}

		return $pdo;
	}

	/**
	 * @return Configuration Doctrine metadata configuration.
	 * @throws TConfigurationException if the driver type is unsupported.
	 */
	protected function createMetadataConfiguration()
	{
		$isDevMode = $this->getApplication()->getMode() == TApplicationMode::Debug;

		switch($this->getDriverType())
		{
			case XDoctrineDriverType::Xml:
				$dConfig = Setup::createXMLMetadataConfiguration($this->getMetadataPaths(), $isDevMode, $this->getProxyDir());
				break;

			case XDoctrineDriverType::Annotation:
				$dConfig = Setup::createAnnotationMetadataConfiguration($this->getMetadataPaths(), $isDevMode, $this->getProxyDir());
				break;

			case XDoctrineDriverType::Yaml:
				$dConfig = Setup::createYAMLMetadataConfiguration($this->getMetadataPaths(), $isDevMode, $this->getProxyDir());
				break;

			default:
				throw new TConfigurationException(Prado::localize('Unsupported driver type "{type}".', array('type' => $this->getDriverType())));
		}

		return $dConfig;
	}

	public function init($config)
	{
		parent::init($config);

		$this->ensureProxyDirExists();

		$dConfig = $this->createMetadataConfiguration();

		$dConfig->setEntityNamespaces($this->getEntityNamespaces());
		$dConfig->setProxyNamespace($this->getProxyNamespace());

		//Cache config
		$cacheClass = $this->getCacheClass();
		if($cacheClass != null)
		{
			$cache = new $cacheClass();
			$dConfig->setMetadataCacheImpl($cache);
			$dConfig->setQueryCacheImpl($cache);
			$dConfig->setHydrationCacheImpl($cache);
		}

		$sqlLoggerClass = $this->getSqlLoggerClass();
		if($sqlLoggerClass != null)
		{
			$dConfig->setSQLLogger(new $sqlLoggerClass());
		}

		if(isset($config['CustomStringFunction']))
		{
			foreach($config['CustomStringFunction'] as $fct => $class)
			{
				$dConfig->addCustomStringFunction($fct, $class);
			}
		}

		//Autoload proxies
		Autoloader::register($this->getProxyDir(), $this->getProxyNamespace());

		if($this->getApplication()->getMode() == TApplicationMode::Normal)
		{
			$dConfig->ensureProductionSettings();
		}

		$dbParams = array(
//			'driver' => 'pdo_mysql',
//			'user' => 'root',
//			'password' => '',
//			'dbname' => 'foo',
			'pdo' => $this->getPdoInstance(),
		);
		$this->entityManager = EntityManager::create($dbParams, $dConfig);
	}

	/**
	 * Finds the database connection instance from the Application modules.
	 * @param string $id Database connection module ID.
	 * @return TDbConnection Database connection.
	 * @throws TConfigurationException when module is not of TDbConnection or TDataSourceConfig.
	 */
	protected function findConnectionByID($id)
	{
		$conn = $this->getApplication()->getModule($id);
		if($conn instanceof TDbConnection)
		{
			return $conn;
		}
		else if($conn instanceof TDataSourceConfig)
		{
			return $conn->getDbConnection();
		}
		else
		{
			throw new TConfigurationException('datasource_dbconnection_invalid', $id);
		}
	}

}
