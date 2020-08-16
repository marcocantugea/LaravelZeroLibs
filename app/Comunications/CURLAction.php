<?php

namespace App\Comunications;

use App\Cache\CacheManager;
use App\Comunications\URLBuilder;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Support\Facades\Storage;

class CURLAction{

    private $response=null;
    private $options=array();
    private $CURLHeader=array();
    private $HttpError=false;
    private $ResponseError;
    private $AttachFile=false;
    private $AttachFileAsRaw=false;
    private $AttachFileAsTransfer=false;
    /**
     * Propiedad para objeto de URLBuilder
     *
     * @var URLBuilder
     */
    private $URLBuilder=null;

    /**
     * Cache manager object
     *
     * @var CacheManager
     */
    private $CacheManager=null;

    public function __construct()
    {
        $this->addOption(CURLOPT_RETURNTRANSFER,true);
    }

    /**
     * Envia Peticion CURL
     *
     * @return void
     */
    public function send(){

        if(count($this->options)<=1){
            return null;
        }

        if($this->CacheManager->ExistChache() && !$this->CacheManager->expire()){
                var_dump("loaded from cache");
                $this->response= $this->CacheManager->getContent();
                return $this;
        }

        $curl = curl_init();

        if(count($this->CURLHeader)>0){
            $this->setHEADERS();
        }
        
        curl_setopt_array($curl,$this->options);

        $this->response = curl_exec($curl);
        //guarda respuesta en cache
        $this->CacheManager->setContent($this->response)->saveCacheFile();

        if(curl_error($curl)!=""){
            $this->HttpError=true;
            $this->ResponseError=curl_error($curl);
        }
        return $this;
    }

    /**
     * Agrega opcion CURL
     *
     * @param integer $CURLoption
     * @param mixed $value
     * @return self
     */
    public function addOption(int $CURLoption,$value){
        if($CURLoption<0){
            return null;
        }
        $this->options[$CURLoption]=$value;
        return $this;
    }

    /**
     * Agrega la URL a las opciones del CURL
     *
     * @param string $URL
     * @return self
     */
    public function setURL(string $URL){

        //Inicializa cache manager
        if(is_null($this->CacheManager)){
            $this->CacheManager= new CacheManager($URL);
        }

        //cargca cache si existe
        $this->CacheManager->loadCacheFile();

        if(!is_null($URL)){
            $this->addOption(CURLOPT_URL,$URL);
        }
        return $this;
    }

    /**
     * Asigna el tipo de Request
     *
     * @param string $Request
     * @return void
     */
    protected function typeOfRequest(string $Request){
        if(!is_null($Request)){
            $this->addOption(CURLOPT_CUSTOMREQUEST,$Request);
        }
        return $this;
    }

    /**
     * Configura el metodo HTTP como  GET
     *
     * @return self
     */
    public function setGETOption(){
        $this->typeOfRequest("GET");
        return $this;
    }

    /**
     * Configura el metodo HTTP como  POST
     *
     * @return self
     */
    public function setPOSTOption(){
        $this->typeOfRequest("POST");
        $this->addOption(CURLOPT_POST,1);
        return $this;
    }

    /**
     * Configura el metodo HTTP como  PUT
     *
     * @return self
     */
    public function setPUTOption(){
        $this->typeOfRequest("PUT");
        $this->addOption(CURLOPT_PUT,1);
        return $this;
    }

    /**
     * Configura el metodo HTTP como  DELETE
     *
     * @return self
     */
    public function setDELETEOption(){
        $this->typeOfRequest("DELETE");
        return $this;
    }

    /**
     * Agrega HEADERS a las opciones del CURL
     *
     * @param string $option
     * @return self
     */
    public function addHEADERSOptions(string $option){
        if(!is_null($option)){
            $this->CURLHeader[]=$option;
        }
        return $this;
    }

    /**
     * Integra los HEADS a las opciones del CURL
     *
     * @return void
     */
    public function setHEADERS(){
        if(count($this->CURLHeader)>0){
            $this->addOption(CURLOPT_HTTPHEADER,$this->CURLHeader);
        }
        return $this;
    }
    
    /**
     * Asiga el valor true o false a la bandera para enviar archivos
     *
     * @return  self
     */ 
    public function setAttachFile(bool $AttachFile)
    {
        $this->AttachFile = $AttachFile;

        return $this;
    }

    /**
     * Bandera para saber si es contenido Crudo
     *
     * @param string $stream
     * @return self
     */
    public function AttachFileRaw(string $stream){
        if(!is_null($stream)){
            $this->addOption(CURLOPT_POSTFIELDS,$stream);
        }
        return $this;
    }

    /**
     * Bandera para saber si es contenido en archivo
     *
     * @param string $stream
     * @param string $fileExts
     * @return self
     */
    public function AttachFileTranser(string $stream,string $fileExts){
        if(!is_null($stream)){
            $this->addOption(CURLOPT_POSTFIELDS,$this->createTempFile($stream,$fileExts));
        }
        return $this;
    }

    /**
     * Crear archivo temporal para enviar
     *
     * @param string $stream
     * @param string $fileExts
     * @return array
     */
    protected function createTempFile(string $stream,string $fileExts){
        $fileNameCreated="tmp". mt_rand().$fileExts;
        Storage::put($fileNameCreated,$stream);
        return array('file'=> new \CURLFILE(".".Storage::url($fileNameCreated)));
    }

    /**
     * Funcion para agregar archivo al envio 
     *
     * @param mixed $stream
     * @param string $FileExts
     * @return self
     */
    public function AttachFileToRequest($stream,string $FileExts){
        if($this->AttachFile){
            if($this->AttachFileAsRaw && !$this->AttachFileAsTransfer){
                $this->AttachFileRaw($stream,$FileExts);
            }else{
                $this->AttachFileTranser($stream,$FileExts);
            }
        }
    }

    /**
     * Obtiene la respuesta de error obtenida
     */ 
    public function getResponseError()
    {
        return $this->ResponseError;
    }

    /**
     * Obtiene el Error HTTP
     */ 
    public function getHttpError()
    {
        return $this->HttpError;
    }

    /**
     * Obtiene el valor de la respuesta
     */ 
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Obtiene las opciones establecidas
     */ 
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get propiedad para objeto de URLBuilder
     *
     * @return  URLBuilder
     */ 
    public function getURLBuilder()
    {
        return $this->URLBuilder;
    }

    /**
     * Set propiedad para objeto de URLBuilder
     *
     * @param  URLBuilder  $URLBuilder  Propiedad para objeto de URLBuilder
     *
     * @return  self
     */ 
    public function setURLBuilder(URLBuilder $URLBuilder)
    {
        $this->URLBuilder = $URLBuilder;

        return $this;
    }
}