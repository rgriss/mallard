<?php

namespace Ryang\Mallard;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MallardBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mallard:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take a snapshot of your database';

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

        $this->warn("WARNING: This program is for administrators.  If you don't know what you are doing, please leave now.");
        if(!$this->confirm("Would you like to continue? A positive answer will execute mysqldump on the database.")){
            return false;
        }
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
        $storage_path_sql_directory= storage_path($sqlDirectory);

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

        $default = config('database.default');
        $db = config('database.connections.'.$default.'.database');

        //conventional timestamp format: Y-m-d H:i:s
        $date = date('Y_m_d_H_i_s');
        $filename = $date.".sql";

        $fullFileName = $storageSqlDirectory."/".$filename;
//        $fullFileName = $storage_path_sql_directory."/".$filename;
        $this->info($filename);
        $this->info($fullFileName);

        //I could not get this next line to work.  Not the first time I've had trouble passing multiple arguments into the Process.
//        $process = new Process(['mysqldump',$db,'>',$fullFileName]);
        //Maybe due to linux virtual machine running on windows?

        $process = new Process(['mysqldump',$db]);
        $process->run();
        $result = $process->getOutput();

        file_put_contents($fullFileName,$result);

        $doesFileExist($fullFileName);

        $this->comment("It is done.");
    }
}
