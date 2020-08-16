<?php

namespace App\Handlers;

class TimeMesureHandler{

    private $starDate=null;
    private $endDate=null;
    private $startMesureTime=null;
    private $endMesureTime=null;


    /**
     * Constructor de clase
     *
     * @param boolean $start
     */
    public function __construct(bool $start=true)
    {
        if($start){
            $this->starDate=date('Y-m-d H:m:s');
            $this->startMesureTime=microtime(true);
        }
    }


    /**
     * Inicia el contador de tiempo
     *
     * @return void
     */
    public function startMesure(){
        $this->starDate=date('Y-m-d H:m:s');
        $this->startMesureTime=microtime(true);
        return $this;
    }

    /**
     * Termina el contador 
     *
     * @return void
     */
    public function endMesure(){
        $this->endMesureTime=microtime(true);
        $this->endDate=date("Y-m-d H:i:s");
        return $this;
    }

    /**
     * Obtiene el tiempo transcurrido en segundos
     *
     * @return float
     */
    public function getTimeEnlapseInSeconds(){
        if(is_null($this->startMesureTime) || is_null($this->endMesureTime)){
            return 0;
        }
        return $this->endMesureTime - $this->startMesureTime;
    }
    
    /**
     * Obtiene el tiempo transcurrido en minutos
     *
     * @return float
     */
    public function getTimeEnlapseInMinutes(){
        return ($this->getTimeEnlapseInSeconds()==0) ? 0 : number_format($this->getTimeEnlapseInSeconds() /60,1);
    }

    /**
     * Obtiene el tiempo transcurrido en horas
     *
     * @return float
     */
    public function getTimeEnlapseInHours(){
        return ($this->getTimeEnlapseInSeconds()==0) ? 0 : number_format($this->getTimeEnlapseInMinutes() /60,2);
    }


    public function reset(){
        $this->starDate=null;
        $this->endDate=null;
        $this->startMesureTime=null;
        $this->endMesureTime=null;

        return $this;
    }


    /**
     * Obtiene la fecha en que se inicio el contador de tiempo
     */ 
    public function getStarDate()
    {
        return $this->starDate;
    }

    /**
     * Obtiene la fecha en que se inicio el contador de tiempo
     */ 
    public function getEndDate()
    {
        return $this->endDate;
    }
}