<?php

namespace App\Console\Commands\Mallard;

use Illuminate\Console\Command;

class MallardQuackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mallard:quack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quack Randomly.';

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
        //
        $string = "";
        for($i=1;$i<=rand(1,3);$i++){
            $string.="Quack! ";
        }
        $this->alert($string);
    }
}