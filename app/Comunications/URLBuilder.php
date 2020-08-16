<?php

namespace App\Comunications;

class URLBuilder
{

    /**
     * Parametros que llevara la URL;
     *
     * @var array
     */
    private $parameters = array();
    /**
     * Variable para guardar los datos dinamicos de una URL
     *
     * @var array
     */
    private $URLDinamic = array();
    /**
     * Variable de la URL original
     *
     * @var string
     */
    private $URLbase = null;
    /**
     * Variable para obtner el URL construido con los datos dinamicos
     *
     * @var string;
     */
    private $URLmerged=null;

    /**
     * Constructor de clase
     *
     * @param string $baseURL
     * @param array $URLDinamic
     * @param array $parameters
     */
    public function __construct(string $baseURL = null, array $URLDinamic = null, array $parameters = null)
    {
        $this->parameters = (!is_null($parameters)) ? $parameters : null;
        $this->URLDinamic = (!is_null($URLDinamic)) ? $URLDinamic : null;
        $this->URLbase = (!is_null($baseURL)) ? $baseURL : null;
    }

    /**
     * Agrega parametros a la URL
     *
     * @param string $parameter
     * @param string $value
     * @return self
     */
    public function addParameter(string $parameter, string $value)
    {
        if (!is_null($parameter) && !is_null($value)) {
            $this->parameters[$parameter] = $value;
        }
        return $this;
    }

    /**
     * Agrega parametros de forma masiva
     *
     * @param array $parameters
     * @return self
     */
    public function setParameters(array $parameters)
    {
        if (!is_null($parameters)) {
            if (count($parameters) > 0) {
                $this->parameters = $parameters;
            }
        }
        return $this;
    }

    /**
     * Agrega valores dianmicos a url
     *
     * @param string $value
     * @return self
     */
    public function addDinamicValuesToURL(string $value)
    {
        if(!is_null($value)){
            $checkedValue=$value;
            
            if($value[0]=="/"){
                $checkedValue=substr($value,1);
            }
            if($value[-1]=="/"){
                $checkedValue=rtrim($value,"/");
            }
            $this->URLDinamic[]=$checkedValue;
        }
        return $this;
    }

    /**
     * Concatena los valores dianmicos a la url
     *
     * @return void
     */
    protected function concatenateDinamicValuesToBaseURL(){
        if(count($this->URLDinamic)>0){
            foreach($this->URLDinamic as $value){

                // si la url base no tiene el / al final 
                if($this->URLbase[-1]!="/"){
                    $this->URLmerged= $this->URLbase . "/" . $value;
                }

                // si la url base si tiene el / al final
                if($this->URLbase[-1]=="/"){
                    $this->URLmerged= $this->URLbase . $value;
                }
            }
        }
    }

    /**
     * Obtiene la URL construida
     *
     * @return string
     */
    public function getURL(){
        return $this->BuildURL();
    }

    /**
     * Construye la URL en base a los parametros dinamicos y parametros
     *
     * @return string
     */
    protected function BuildURL(){
        $this->concatenateDinamicValuesToBaseURL();
        if(count($this->parameters)<=0){
            return $this->URLmerged;
        }else{
            return $this->URLmerged. "?".http_build_query($this->parameters, "", '&', PHP_QUERY_RFC3986) ;
        }
    }

    /**
     * Obtiene los parametros asignados.
     *
     * @return  array
     */ 
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get variable para guardar los datos dinamicos de una URL
     *
     * @return  array
     */ 
    public function getURLDinamic()
    {
        return $this->URLDinamic;
    }

    /**
     * Get variable de la URL original
     *
     * @return  string
     */ 
    public function getURLbase()
    {
        return $this->URLbase;
    }

    /**
     * Set variable de la URL original
     *
     * @param  string  $URLbase  Variable de la URL original
     *
     * @return  self
     */ 
    public function setURLbase(string $URLbase)
    {
        $this->URLbase = $URLbase;

        return $this;
    }
}
