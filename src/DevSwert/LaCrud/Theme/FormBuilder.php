<?php
namespace DevSwert\LaCrud\Theme;

final class FormBuilder{

	private $base_theme;

	public function __construct($base_theme,$theme){
		$base_theme = $base_theme.'/forms';
		$this->base_theme = (is_dir($base_theme)) ? 'packages.DevSwert.LaCrud.'.$theme.'.forms' : 'la-crud::Default.forms';
	}

	//Metodo publico para su funcion
	public function generateFormAddOrEdit($fields){
		if(is_array($fields) && count($fields) > 0){
			$html = '';
			foreach ($fields as $field){
				if(!$field['isAutoincrement']){
					if(count($field['hasForeignKeys']) > 0){
						$html .= $this->addSelectForeignKey($field);
					}
					else{
						switch ($field['type']){
							case 'string':
								$html .= ( $field['isPassword'] ) ? $this->addPassword($field) : $this->addInput($field);
								break;
							case 'date':
								$html .= $this->addDateTime($field);
								break;
							case 'datetime':
								$html .= $this->addDateTime($field);
								break;
							case 'boolean':
								$html .= $this->addRadio($field);
								break;
							case 'enum':
								$html .= $this->addSelect($field);
								break;
							case 'integer':
								$html .= $this->addInteger($field);
								break;
							case 'text':
								$html .= $this->addTextEditor($field);
								break;
							default:
								$html .= $this->addInput($field);
								break;
						}
					}
				}
			}
			return $html;
		}
		$trace = debug_backtrace();
		trigger_error(
            'The fields given is not valid'.
            ' on ' . $trace[0]['file'] .
            ' in line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
	}

	//Metodos privados para la generacion de los campos
	private function addInput($field){
		return \View::make($this->base_theme.'.input', compact('field'))->render();
	}

	private function addPassword($field){
		return \View::make($this->base_theme.'.password', compact('field'))->render();
	}

	private function addInteger($field){
		return \View::make($this->base_theme.'.integer', compact('field'))->render();
	}

	private function addDateTime($field){
		return \View::make($this->base_theme.'.datetime', compact('field'))->render();
	}

	private function addSelect($field){
		return \View::make($this->base_theme.'.select', compact('field'))->render();
	}

	private function addSelectForeignKey($field){
		return \View::make($this->base_theme.'.select-foreign', compact('field'))->render();
	}

	private function addTextEditor($field){
		return \View::make($this->base_theme.'.textarea', compact('field'))->render();
	}

}
