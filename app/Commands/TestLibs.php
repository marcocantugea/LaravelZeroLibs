<?php

namespace App\Commands;

use App\Cache\CacheManager;
use App\Comunications\CURLAction;
use App\Comunications\URLBuilder;
use App\Handlers\TimeMesureHandler;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TestLibs extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'testlibs';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Corre librerias en el metodo';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Test Load Cache Manager
        // $this->TestLoadCacheManagerFile();

        // //Test CURL cache manager
        // $this->TestCreateCacheManager();

        // //Test URL builder with CURLAction
        // $this->TestCURLActionWithURLBuilder();

        // //Test UrL constructor
        // $this->TestURLConstuctor();
        
        //Prueba de CURL
        $this->TestCURLAction();

        //Prueba medicion de tiempo
        $this->TestMesureLib();
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    protected function TestMesureLib()
    {

        //Inicia el mesuretime
        $MesureTimeEnlapse = new TimeMesureHandler();
        $this->info("Start process " . $MesureTimeEnlapse->getStarDate());
        sleep(10);

        $MesureTimeEnlapse->endMesure();
        $this->info("Process end " . $MesureTimeEnlapse->getEndDate());

        $this->info("Process Enlapse Time in seconds: " . $MesureTimeEnlapse->getTimeEnlapseInSeconds());
        $this->info("Process Enlapse Time in minutes: " . $MesureTimeEnlapse->getTimeEnlapseInMinutes());
        $this->info("Process Enlapse Time in Hrs: " . $MesureTimeEnlapse->getTimeEnlapseInHours());
    }

    protected function TestCURLAction()
    {

        $MesureTimeEnlapse = new TimeMesureHandler();
        $this->info("Start process " . $MesureTimeEnlapse->getStarDate());

        $CURLAction = new CURLAction();
        $CURLAction->setURL("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1/oauth/token?grant_type=password&username=e.mora@gme.mx&password=123456");
        $CURLAction->setPOSTOption();
        $CURLAction->addHEADERSOptions("Authorization: Basic c2VsbGVyY2VudGVyY2xpZW50aWQ6MTIzNDU2");
        $CURLAction->send();
        $this->info($CURLAction->getResponse());

        $MesureTimeEnlapse->endMesure();
        $this->info("Process end " . $MesureTimeEnlapse->getEndDate());

        $this->info("Process Enlapse Time in seconds: " . $MesureTimeEnlapse->getTimeEnlapseInSeconds());
        $this->info("Process Enlapse Time in minutes: " . $MesureTimeEnlapse->getTimeEnlapseInMinutes());
        $this->info("Process Enlapse Time in Hrs: " . $MesureTimeEnlapse->getTimeEnlapseInHours());
        dd("end");
    }

    protected function TestURLConstuctor(){
        //http://elektrapreprod.mysellercenter.com/sellercenter/api/v1/oauth/token?grant_type=password&username=e.mora@gme.mx&password=123456
        $URLConstructor = new URLBuilder("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1");
        $URLConstructor->addDinamicValuesToURL("/oauth/token");
        $URLConstructor->addParameter("grant_type","password")->addParameter("username","e.mora@gme.mx")->addParameter("password","123456");
        dd($URLConstructor->getURL());
        
    }

    protected function TestCURLActionWithURLBuilder(){

        $URLConstructor = new URLBuilder("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1");
        $URLConstructor->addDinamicValuesToURL("/oauth/token");
        $URLConstructor->addParameter("grant_type","password")->addParameter("username","e.mora@gme.mx")->addParameter("password","123456");

        $CURLAction = new CURLAction();
        $CURLAction->setURL($URLConstructor->getURL());
        $CURLAction->setPOSTOption();
        $CURLAction->addHEADERSOptions("Authorization: Basic c2VsbGVyY2VudGVyY2xpZW50aWQ6MTIzNDU2");
        $CURLAction->send();
        dd($CURLAction);
    }

    protected function TestCreateCacheManager(){
        $URLConstructor = new URLBuilder("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1");
        $URLConstructor->addDinamicValuesToURL("/oauth/token");
        $URLConstructor->addParameter("grant_type","password")->addParameter("username","e.mora@gme.mx")->addParameter("password","123456");

        $CURLAction = new CURLAction();
        $CURLAction->setURL($URLConstructor->getURL());
        $CURLAction->setPOSTOption();
        $CURLAction->addHEADERSOptions("Authorization: Basic c2VsbGVyY2VudGVyY2xpZW50aWQ6MTIzNDU2");
        $CURLAction->send();
        
        
        $CacheManager= new CacheManager($URLConstructor->getURL());
        $CacheManager->setContent($CURLAction->getResponse())->saveCacheFile();
        dd($CacheManager);
        
    }

    protected function TestLoadCacheManagerFile(){
        $URLConstructor = new URLBuilder("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1");
        $URLConstructor->addDinamicValuesToURL("/oauth/token");
        $URLConstructor->addParameter("grant_type","password")->addParameter("username","e.mora@gme.mx")->addParameter("password","123456");
        $CacheManager= new CacheManager($URLConstructor->getURL());
        var_dump($CacheManager);
        $CacheManager->loadCacheFile();
        dd($CacheManager->getContent());

    }
}
