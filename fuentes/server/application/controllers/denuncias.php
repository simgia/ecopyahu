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
		//$this->load->model('denuncias_m','denuncias');	
	}
	
	/**
	 * Metodo que se ejecuta por defecto en el controlador.
	 * @return void
	 */
	public function index(){
		$this->basico('true');
	}
    
    
    
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
	 * @param json $puntos_json [via POST]
	 * @param json $puntos_json [via POST]
	 * @return void
	 */
	public function getDenunciasEnMapa(){
		$this->data['p_menu_actual'] = 'denuncias';
		$this->data['selector']='consultarEnMapa';
		$this->data['es_administrador'] = $this->validar_permiso(array('administrador'),true);
		//$puntos_json = $this->input->post('puntos',true);
		$filtros = json_decode($this->input->post('filtros',true));
		
		$this->data['filtros_post'] = $filtros;
		//$this->data['filtros']=$filtros;
		//$this->data['filtros'] =$filtros_json;
		$v_start = $this->input->get('start',true);
		$v_limit = $this->input->get('limit',true);
		$v_page  = $this->input->get('page',true);
		$v_sort  = json_decode($this->input->get('sort'));
		## posibles filtros ##
		if($filtros!=null){
			$v_desde = $filtros->fecha_desde;
			$v_hasta = $filtros->fecha_hasta;
			$v_texto = $filtros->texto_buscado;	
			$v_limit = 100;
			$v_page =1;
			$v_sort = null;
			
		}else{
			$v_desde  = $this->input->get('fecha_desde',true);
			$v_hasta  = $this->input->get('fecha_hasta',true);
			$v_texto  = $this->input->get('texto_buscado',true);
		}
		## fin filtros ##		
		$v_offset = $v_limit*$v_page - $v_limit;
		$v_data['cantidadTotal'] =0;
		$v_data['success']=false;
		$v_data['datos'] = array();		
		$consulta = $this->denuncias->get_lista_denuncias($v_limit,$v_offset,$v_sort,null,null,$v_desde,$v_hasta,$v_texto);
		$puntos = array();
		$contador_denuncias_omitidos =0;
		$contador_denuncias_ubicados =0;
		if($consulta->num_rows()>0){
			foreach ($consulta->result() as $punto){
				$tmp = new stdClass();
				$tmp->latitud = $punto->DENUNCIA_LATITUD;
				$tmp->longitud = $punto->DENUNCIA_LONGITUD;
				$tmp->fecha_registro = $punto->DENUNCIA_FECHA_REGISTRO;
				$tmp->denuncia_estado = $punto->DENUNCIA_ESTADO;
				$tmp->denuncia_id = $punto->DENUNCIA_ID;
				$tmp->estado = $punto->DENUNCIA_ESTADO;
				if($tmp->latitud!=null&&$tmp->latitud!=''){
					$contador_denuncias_ubicados++;
					$puntos[]=$tmp;
				}else{
					$contador_denuncias_omitidos++;
				}
			}
		}
		$this->data['contador_omitidos'] =$contador_denuncias_omitidos;
		$this->data['contador_ubicados'] =$contador_denuncias_ubicados;
		//$puntos = json_decode($puntos_json);
		
		
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
		
		//print_r($puntos);

		$v_indice_nodo = 0;
		$v_indice_hijos_denuncia = 0;
		$v_primer_nodo = true;
		$v_es_hoja = false;
		
		for($i = 0; $i < count ($puntos); $i++){
			//echo "<br>Decimal: " . $puntos[$i]->denuncia_id . " hexa: " . dechex($puntos[$i]->denuncia_id);
			if($v_primer_nodo){
				$puntos_nodos[$v_indice_nodo]->latitud  = $puntos[$i]->latitud;
				$puntos_nodos[$v_indice_nodo]->longitud = $puntos[$i]->longitud;
				$puntos_nodos[$v_indice_nodo]->cantidad = 0;
				$v_primer_nodo = false;
			}
			if($v_es_hoja){
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
				$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->id_denuncia = /*dechex*/($puntos[$i]->denuncia_id);
				$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->estado = $puntos[$i]->denuncia_estado;
				$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->fecha_registro = $puntos[$i]->fecha_registro;
				$v_indice_hijos_denuncia = $v_indice_hijos_denuncia + 1;
			}else{
				if(isset($puntos_nodos[$v_indice_nodo]->estado) && $puntos_nodos[$v_indice_nodo]->estado == 'null'){
					$puntos_nodos[$v_indice_nodo]->cantidad = $puntos_nodos[$v_indice_nodo]->cantidad + 1;
					$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->id_denuncia = /*dechex*/($puntos[$i]->denuncia_id);
					$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->estado = $puntos[$i]->denuncia_estado;
					$puntos_nodos[$v_indice_nodo]->denuncias[$v_indice_hijos_denuncia]->fecha_registro = $puntos[$i]->fecha_registro;
				}else{
					// Nodo hoja.
					$puntos_nodos[$v_indice_nodo]->cantidad = 1;
					$puntos_nodos[$v_indice_nodo]->estado = $puntos[$i]->denuncia_estado;
					$puntos_nodos[$v_indice_nodo]->id_denuncia = /*dechex*/($puntos[$i]->denuncia_id);
					$puntos_nodos[$v_indice_nodo]->fecha_registro = $puntos[$i]->fecha_registro;	
				}
				$v_indice_nodo = $v_indice_nodo + 1;
				$v_indice_hijos_denuncia = 0;
				$v_es_hoja = true;	
			}
		}
/*
		echo "<br><br>El nuevo array es: <br>";
		print_r($puntos_nodos);
		echo "<br>Fin del nuevo array!!!";
*/
		//$this->data['puntos'] = $puntos;
		$this->data['puntos'] = $puntos_nodos;
		foreach($filtros as $key=>$value){
			if(!empty($value)){
				$this->data['filtros'][$key]=$value;
			}
		}
	    $this->load->view('denuncias/lista_denuncia_mapa',$this->data);
	}
	
                /**
	 * metodo para guardar la imagen, ya debe crear la denuncia como borrador y devolver el id para que cuando guarde la denuncia solo actualice la info
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
	

} // Fin del controlador denuncias.