<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author 
 * @package ecopyahu
 *
 */
class denuncias extends SMG_Controller{
    /**
     * @var denuncias_m
     */
    var $denuncias;
   	
    /**
     * Constructor donde levanta las librerias: 
     */
    public function __construct(){
        parent::__construct();
        $this->load->model('denuncias_m', 'denuncias');	
        $this->load->model('multimedias_m', 'multimedias');	
    }
	
    /**
     * Metodo que se ejecuta por defecto en el controlador.
     * @return void
     */
    /*public function index(){
        $this->basico('true');
    }*/
    
    public function guardarDenuncia(){
    }

    /**
     * Metodo que consulta al modelo y recupera un listado de denuncias.
     */
    public function getDenuncias(){
    }
    
    /**
     * Metodo que consulta el detalle de la denuncia.
     * @param string p_denuncia_id [via GET]
     */
    public function getDenuncia(){
    }
	
    /**
     * Funcion que arma un array con nodo hoja o nodo hijo dependiendo de la latitud y longitud.
     * Ejemplo: si en el mismo punto (lat,lon) tiene varias denuncias, entonces tiene un padre con hijos;
     * caso contrario es nodo hoja.
     * Despues de armar todo, se envia esa nueva estrutura al mapa.
     * @method getDenunciasEnMapa
     * @param json $puntos_json [via POST]
     * @param json $puntos_json [via POST]
     * @return void
     */
   // public function getDenunciasEnMapa(){
    public function index(){
	$v_start = $this->input->get('start',true);
	$v_limit = $this->input->get('limit',true);
	$v_page  = $this->input->get('page',true);
	$v_sort  = json_decode($this->input->get('sort'));
	$v_offset = $v_limit*$v_page - $v_limit;
	$v_data['cantidadTotal'] = 0;
	$v_data['success'] = false;
	$v_data['datos'] = array();	
	$consulta = $this->denuncias->get_lista_denuncias($v_limit, $v_offset, $v_sort);
	$puntos = array();
	$contador_denuncias_omitidos = 0;
	$contador_denuncias_ubicados = 0;
	if($consulta->num_rows() > 0){
            foreach ($consulta->result() as $punto){
		$tmp = new stdClass();
                $tmp->denuncia_id = $punto->denuncia_id;
                $tmp->denuncia_desc = $punto->denuncia_desc;
                $tmp->fecha_registro = $punto->denuncia_fecha;
                $tmp->latitud = $punto->denuncia_lat;
		$tmp->longitud = $punto->denuncia_lon;
		$tmp->denuncia_estado = $punto->denuncia_estado;
		
		$tmp->estado = $punto->denuncia_estado;
		if($tmp->latitud != null && $tmp->latitud != ''){
                    $contador_denuncias_ubicados++;
                    $puntos[]=$tmp;
		}else{
	 	     $contador_denuncias_omitidos++;
		}
            }
	}
	$this->data['contador_omitidos'] =$contador_denuncias_omitidos;
	$this->data['contador_ubicados'] =$contador_denuncias_ubicados;
	
	// Ordenar el array de objetos.
	/*
	 * Funcion interna para comparar la latitud
	 * @param double $p_punto1
	 * @param double $p_punto2
	 * @return integer
	 */
	function cmp($p_punto1, $p_punto2){
            if (($p_punto1->latitud == $p_punto2->latitud)){
                return 0;
            }elseif($p_punto1->longitud == $p_punto2->longitud){
	         return 0;
            }else
                 return ($p_punto1->latitud < $p_punto2->latitud) ? -1 : 1;
        }
	usort($puntos, "cmp");
        
	$v_indice_nodo = 0;
	$v_indice_hijos_denuncia = 0;
	$v_primer_nodo = true;
	$v_es_hoja = false;
        
        // Se crea un array vacio.
	$puntos_nodos = array();

	for($i = 0; $i < count ($puntos); $i++){
            if($v_primer_nodo){
                $puntos_nodos[] = new stdClass();
                $puntos_nodos[$v_indice_nodo]->latitud  = $puntos[$i]->latitud;
                $puntos_nodos[$v_indice_nodo]->longitud = $puntos[$i]->longitud;
                $puntos_nodos[$v_indice_nodo]->cantidad = 0;
		$v_primer_nodo = false;
            }
            if($v_es_hoja){
                $puntos_nodos[] = new stdClass();
                $puntos_nodos[$v_indice_nodo]->latitud  = $puntos[$i]->latitud;
		$puntos_nodos[$v_indice_nodo]->longitud = $puntos[$i]->longitud;
		$puntos_nodos[$v_indice_nodo]->cantidad = 0;
		$v_es_hoja = false;
            }
		
            //if(isset($puntos[$i+1]->latitud) && $puntos[$i]->latitud == $puntos[$i+1]->latitud){
            if(isset($puntos[$i+1]->latitud) && ($puntos[$i]->latitud == $puntos[$i+1]->latitud) && ($puntos[$i]->longitud == $puntos[$i+1]->longitud)){
                // Nodo Padre e hijos.
                $puntos_nodos[$v_indice_nodo]->cantidad = $puntos_nodos[$v_indice_nodo]->cantidad + 1;
                $puntos_nodos[$v_indice_nodo]->estado = 'null';
                $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia] = new stdClass();
                $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->denuncia_id = ($puntos[$i]->denuncia_id);
                $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->estado = $puntos[$i]->denuncia_estado;
                $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->fecha_registro = $puntos[$i]->fecha_registro;
                $v_indice_hijos_denuncia = $v_indice_hijos_denuncia + 1;
            }else{
	         if(isset($puntos_nodos[$v_indice_nodo]->estado) && $puntos_nodos[$v_indice_nodo]->estado == 'null'){
		     $puntos_nodos[$v_indice_nodo]->cantidad = $puntos_nodos[$v_indice_nodo]->cantidad + 1;
                     $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia] = new stdClass();
		     $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->denuncia_id = ($puntos[$i]->denuncia_id);
                     $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->estado = $puntos[$i]->denuncia_estado;
		     $puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->fecha_registro = $puntos[$i]->fecha_registro;
                }else{
		     // Nodo hoja.
		     $puntos_nodos[$v_indice_nodo]->cantidad = 1;
                     $puntos_nodos[$v_indice_nodo]->estado = $puntos[$i]->denuncia_estado;
                     $puntos_nodos[$v_indice_nodo]->denuncia_id = /*dechex*/($puntos[$i]->denuncia_id);
                     $puntos_nodos[$v_indice_nodo]->fecha_registro = $puntos[$i]->fecha_registro;	
		}
		$v_indice_nodo = $v_indice_nodo + 1;
		$v_indice_hijos_denuncia = 0;
		$v_es_hoja = true;	
            }
	}
	$this->data['puntos'] = $puntos_nodos;
        $this->load->view('denuncias/lista_mapa', $this->data);
    }
    
    /**
     * Metodo que consulta el detalle de la denuncia.
     * @method consulta_detalle_denuncia
     * @param string p_denuncia_id [via GET]
     */
    public function consulta_detalle_denuncia(){
        //$this->validar_permiso(array("administrador","usuario","institucion"));
        $v_denuncia_id = $this->input->get('denuncia_id', true);

        // Se obtiene los detalles de la denuncia.
        $v_datos_denuncia = $this->denuncias->get_denuncia($v_denuncia_id);
        
        //echo "Last query es: " . $this->db->last_query(). "\n\n";

        if($v_datos_denuncia->num_rows() > 0){
            $v_datos_denuncia = $v_datos_denuncia->result_array();
            $v_datos_denuncia = $v_datos_denuncia[0];
            $this->data["denuncia_id"] = $v_denuncia_id;
            $v_textos_denuncias_array = $this->get_denuncia_texto_narrado($v_datos_denuncia);
            
            $this->data["datos_denuncia"] = $v_textos_denuncias_array;
                    
            // Detalles de las imagenes.
            //$codigo_decimal = hexdec($p_denuncia_id);
            //$this->data["imagenes"] = $this->denuncias->get_denuncias_detalle_imagenes($codigo_decimal);
            $this->data["imagenes"] = $this->multimedias->get_imagenes($v_denuncia_id);
            $this->data["imagenes"] = $this->data["imagenes"]->result_array();
            $this->load->view('denuncias/denuncias_detalle', $this->data);
        }else{
             //redirect("/denuncias/index", 'refresh');
             //redirect("/denuncias/basico", 'refresh');
             //redirect('index', 'refresh');
        }
    }
	
    /**
     * Metodo para guardar la imagen, ya debe crear la denuncia como borrador y devolver el id para que cuando guarde la denuncia solo actualice la info
     */ 
    public function subirImagen(){        
    } // Fin de la funcion publica denuncias_paso2.
	
    /**
     * borrar imagen
     */
    public function borrarImagen(){
    }
    
    /**
     * recupera las imagenes cargadas 
     */
    public function recuperarImagenes(){
    }
    
    
    /******************************
     * METODOS PRIVADOS           *
     *****************************/
    /**
     * Function privada que devuelve un array con todos textos a usarse al convertir a denuncia narrada.
     * @method get_denuncia_texto_narrado
     * @param string p_datos un array con los datos cargados por el usuario a partir del formulario o movil.
     * @return array
     */
    private function get_denuncia_texto_narrado($p_datos) {
        $v_textos = array();    // Inicializamos el array.
    
        if($p_datos) {
            // print_r($p_datos);
            $v_textos["tipo_denuncia"] = "";
            
            // Excepcion si vienen los datos de base de datos.
            //---------------------------------------------------------
            if(isset($p_datos["denuncia_id"])) {
                $v_textos["fecha_registro"] = "Denuncia registrada el ".$p_datos["denuncia_fecha"];
                if(isset($p_datos["denuncia_estado"])){
                    $v_textos["denuncia_estado"] = "Estado: ".$p_datos["denuncia_estado"];
                }else{
                     $v_textos["denuncia_estado"] = "Estado: ". 'Nuevo';
                }
                if(isset($p_datos["categoria_nombre"])){
                    $v_textos["categoria_nombre"] = "Denuncia realizada por : ". $p_datos["categoria_nombre"];
                }
                
                if(isset($p_datos["denuncia_desc"])){
                    $v_textos["denuncia_desc"] = "Descripci&oacute;n: ". $p_datos["denuncia_desc"];
                }
                
                if(isset($p_datos["denuncia_fuente"])){
                    $v_textos["denuncia_fuente"] = "Enviado por: ". $p_datos["denuncia_fuente"];
                }
                // Si hay coordenadas o Localizacion.
                
            }
        }
        return $v_textos;
    } // Fin de la funcion public get_denuncia_texto_narrado.
} // Fin del controlador denuncias.