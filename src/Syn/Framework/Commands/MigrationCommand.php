<?php namespace Syn\Framework\Commands;

use App;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class MigrationCommand extends MigrateCommand
{
	protected $name = 'migration';
	protected $description = 'Enabling complete vendor folders to be migrated based on timestamp';

	/**
	 * The migrator instance.
	 *
	 * @var \Illuminate\Database\Migrations\Migrator
	 */
	protected $migrator;

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ( ! $this->confirmToProceed()) return;

		$directories = [];

		// in development we also screen the workbench directories
		$directories[]		= "workbench";

		// always search the vendor directory (from composer includes)
		$directories[]		= "vendor";

		$this->prepareDatabase();

		// The pretend option can be used for "simulating" the migration and grabbing
		// the SQL queries that would fire if the migration were to be run against
		// a database for real, which is helpful for double checking migrations.
		$pretend = $this->input->getOption('pretend');

		$vendor = $this->input->getOption('vendor');

		$force	= $this -> input -> getOption('force');

		$files 				= [];

		foreach($directories as $workbench)
		{
			$globExpression		= sprintf('%s/%s/*/src/migrations/*_*.php', $workbench, $vendor);
			$migrations 		= glob($globExpression);

			foreach($migrations as $migration)
			{
				require_once $migration;
				preg_match('/([0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_.*)\.php$/', $migration, $m);
				$files[] 			= basename($m[1]);
			}
		}
		sort($files);


		$this->notes = array();

		// Once we grab all of the migration files for the path, we will compare them
		// against the migrations that have already been run for this package then
		// run each of the outstanding migrations against a database connection.
		$ran = $this->migrator->getRepository()->getRan();

		$migrations = array_diff($files, $ran);
        if(!count($migrations))
        {
            $this -> info('No migrations to run.');
            return;
        }
		$this -> line('Running migrations:');
		foreach($migrations as $migration)
			$this -> info("* {$migration}");

		if($force || $this -> confirm("Are you sure you want to continue? [yes|no]"))
		{
			$this -> migrator -> runMigrationList($migrations, $pretend);

			// Once the migrator has run we will grab the note output and send it out to
			// the console screen, since the migrator itself functions without having
			// any instances of the OutputInterface contract passed into the class.
			foreach ($this->migrator->getNotes() as $note)
			{
				$this->output->writeln($note);
			}

			// Finally, if the "seed" option has been given, we will re-run the database
			// seed task to re-populate the database, which is convenient when adding
			// a migration and a seed at the same time, as it is only this command.
			if ($this->input->getOption('seed'))
			{
				$this->call('db:seed', ['--force' => true]);
			}
		}
		else
			$this -> error('Cancelled.');
	}
	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('bench', null, InputOption::VALUE_OPTIONAL, 'The name of the workbench to migrate.', null),

			array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'),

			array('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'),

			array('vendor', null, InputOption::VALUE_REQUIRED, 'The vendor to run all migrations of.', null),

			array('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),

			array('seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'),
		);
	}
}