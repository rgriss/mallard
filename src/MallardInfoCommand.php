<?php

namespace Ryang\Mallard;
use Illuminate\Console\Command;

class MallardInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mallard:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a little info about the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->warn("WARNING: This program is for administrators.  If you don't know what you are doing, please leave now.  Otherwise, type your name to continue.");
        //The Storage Facade does NOT work as expected in the CLI environment on homestead:
        //$x=Storage::makeDirectory($storageSqlDirectory);
        //the following code uses this instead:
        //$foo = mkdir("storage/".$desiredFolderName);

        $doesFileExist=function($filename){
            if (file_exists($filename) && is_file($filename)) {
                echo "The file $filename exists".PHP_EOL;
                return true;
            } else {
                echo "The file $filename does not exist".PHP_EOL;
                return false;
            }
        };

        $doesDirectoryExist=function($filename){
            if (file_exists($filename) && is_dir($filename)) {
                echo "The directory $filename exists".PHP_EOL;
                return true;
            } else {
                echo "The directory $filename does not exist".PHP_EOL;
                return false;
            }
        };

        $sqlDirectory = "sqldumpfiles";
        $storageSqlDirectory = "storage/".$sqlDirectory;

        if(!$doesDirectoryExist($storageSqlDirectory)){
            if(!$this->confirm("Would you like to create the 'storage/".$sqlDirectory."' folder now?")){
                $this->comment("Your project is untouched.");
                return;
            }
            mkdir($storageSqlDirectory);
            file_put_contents($storageSqlDirectory."/.gitignore","*\n!.gitignore");
            $this->comment("The ".$storageSqlDirectory." directory has been created!");
        }

        $gitignore = $storageSqlDirectory."/.gitignore";
        if(!$doesFileExist($gitignore)){
            if(!$this->confirm("Would you like to create the '".$gitignore."' file now?")){
                $this->comment("Your project is untouched.");
                return;
            }
            file_put_contents($gitignore,"*\n!.gitignore");
            $this->comment("The file has been created!");
        }

        $this->comment("Here is a little info about your current database connection:");

        $default = config('database.default');

//        $currentsettings = config('database.connections.'.$default);
//        $this->info(print_r($currentsettings));

        $db = config('database.connections.'.$default.'.database');
        $un = config('database.connections.'.$default.'.username');
        $pw = config('database.connections.'.$default.'.password');
        echo PHP_EOL;

        $this->info("\tdatabase: ".$db);
        $this->info("\tusername: ".$un);
        $this->info("\tpassword: ".$pw);
        echo PHP_EOL;

        $this->alert("Backup");
        $this->comment("To backup the database, you can manually issue the following command:");
        $this->info("EX:\tmysqldump [database] > /path/to/desired_filename_db_backup.dump");
        echo PHP_EOL;
        $this->info("\tmysqldump ".$db." > ".$storageSqlDirectory."/backup.sql");
        echo PHP_EOL;

        $this->alert("Restore");
        $this->comment("To restore the database from the file you just created, you can manually issue the following command:");
        $this->info("EX:\tmysql -p[password] -u [user] [database] < db_backup.dump");
        echo PHP_EOL;
        $this->info("1:\tmysql -p -u ".$un." ".$db." < "." ".$storageSqlDirectory."/backup.sql");
        $this->info("2:\tmysql -p"."$pw"." -u ".$un." ".$db." < "." ".$storageSqlDirectory."/backup.sql");
        echo PHP_EOL;
        $this->comment("If you use option 1, you'll have to type your password.");
        $this->comment("If you use option 2, the restore will run instantly.");

    }
}
