<?php

	class FormatFecha {
		private $createdAt;
		private $meses;
		private $dia;
		private $mes;
		private $anio;
		private $fecha;

		public function __construct( &$createAt ) {
			$this->createAt = $createAt;
			$this->meses = array(
				"Jan" => "01",
				"Feb" => "02",
				"Mar" => "03",
				"Apr" => "04",
				"May" => "05",
				"Jun" => "06",
				"Jul" => "07",
				"Aug" => "08",
				"Sep" => "09",
				"Oct" => "10",
				"Nov" => "11",
				"Dec" => "12"
			);

		}//end __construct

		public function getFechaFormated() {

			$this->dia = substr( $this->createAt, 8, 2 );

			$mes = substr( $this->createAt, 4, 3 );

			$this->mes = str_replace( $mes, $this->meses[ $mes ], $mes );

			$this->anio = substr( $this->createAt, 26, 4 );

			$this->fecha = trim( $this->dia )."-".$this->mes."-".$this->anio;

			return $this->fecha;
			
		}//end getFechaFormated

	}//end FormatFecha

?>