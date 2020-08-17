<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Comunications\CURLAction;
use App\Handlers\TimeMesureHandler;
use Illuminate\Support\Facades\Log;
use NunoMaduro\Collision\Adapters\Phpunit\Printer;

class TestCURLAction extends TestCase
{

    /**
     * Send a CURL action test
     *  @test
     *  @after
     * @return void
     */
    public function SendNew_curlAction()
    {
        $CURLAction = new CURLAction();
        $CURLAction->setURL("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1/oauth/token?grant_type=password&username=e.mora@gme.mx&password=123456");
        $CURLAction->setPOSTOption();
        $CURLAction->addHEADERSOptions("Authorization: Basic c2VsbGVyY2VudGVyY2xpZW50aWQ6MTIzNDU2");
        $CURLAction->send();

        if($CURLAction->getHttpError()){
            $this->assertFalse(true);
        }else{
            $this->assertFalse(false);
        }
    }

    /**
     * Test CURL action with cache
     * @test
     * @return void
     */
    public function Send_curl_action_with_cache(){
        $CURLAction = new CURLAction();
        $CURLAction->setURL("http://elektrapreprod.mysellercenter.com/sellercenter/api/v1/oauth/token?grant_type=password&username=e.mora@gme.mx&password=123456");
        $CURLAction->setPOSTOption();
        $CURLAction->addHEADERSOptions("Authorization: Basic c2VsbGVyY2VudGVyY2xpZW50aWQ6MTIzNDU2");
        $CURLAction->setEnableCache(true);
        $CURLAction->send();
        
        $this->assertStringContainsString("token",$CURLAction->getResponse());
        $this->assertIsString($CURLAction->getResponse());
    } 

}
