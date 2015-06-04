<?php namespace Aliukevicius\LaravelRbac\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;
use Illuminate\Filesystem\Filesystem;

class CreateMigrationsCommand extends  Command {

    /** @var array List of migration files in projects migration directory */
    protected $migrationFiles;

    protected $migrationDirectoryPath;

    protected $files;

    protected $composer;

    /**
     * Console command name
     *
     * @var string
     */
    protected $name = 'laravel-rbac:create-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create package migration files';

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;

        $this->migrationDirectoryPath = base_path('/database/migrations');
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function fire()
    {
        $this->generateMigrationFile('create_roles_table');
        $this->generateMigrationFile('create_permissions_table');
        $this->generateMigrationFile('create_role_permission_table');
        $this->generateMigrationFile('create_user_role_table');

        $this->composer->dumpAutoloads();
        $this->info('Migrations created');

        return true;
    }

    /**
     * Generate migration file
     *
     * @param $migrationName
     */
    private function generateMigrationFile($migrationName)
    {
        // prevent creating migration files which already exist
        if ($this->migrationFileExists($migrationName) == false) {
            $stub = $this->getStub($migrationName);

            $this->files->put($this->getPath($migrationName), $stub);
        }
    }

    /**
     * Get stub file contents
     *
     * @param $migrationName
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub($migrationName)
    {
        $stubsDir =  __DIR__ .'/../../../migrations/Stubs/';

        return $this->files->get($stubsDir . $migrationName . '.stub');
    }

    /**
     * Generate migration path
     *
     * @param $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->migrationDirectoryPath .'/' . date('Y_m_d_His') . '_' . $name . '.php';
    }

    /**
     * Check if migration file is already in migrations directory
     *
     * @param $migrationName
     */
    protected function migrationFileExists($migrationName)
    {
        if (is_null($this->migrationFiles)) {
            $this->migrationFiles = array_diff(scandir($this->migrationDirectoryPath), array('..', '.', '.gitkeep'));
        }

        $pattern = '/^[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_'.$migrationName.'\.php$/';
        foreach ($this->migrationFiles as $fileName) {
            if (preg_match($pattern, $fileName)) {
                return true;
            }
        }

        return false;
    }
}