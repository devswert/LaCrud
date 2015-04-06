<?php namespace DevSwert\LaCrud\Theme;

use DevSwert\LaCrud\Utils;

final class FormBuilder{
	use Utils;

	private $base_theme;

	public function __construct($base_theme,$theme){
		$base_theme = $base_theme.'/forms';

		if(!is_dir($base_theme)){
			$this->throwException("Don't exist a 'forms' folder in your Theme for LaCrud");
		}
		$this->base_theme = 'lacrud::'.$theme.'.forms.';
	}

	//Metodo publico para su funcion
	public function generateFormAddOrEdit($fields){
		if(is_array($fields) && count($fields) > 0){
			$html = '';
			foreach ($fields as $key => $field){
				if( $key === 'hasManyRelation' && is_array($fields['hasManyRelation'])){
					$html .= $this->addMultiSelectManyRelation($fields['hasManyRelation']);
				}
				else if(!$field['isAutoincrement']){
					if(count($field['hasForeignKeys']) > 0){
						$html .= $this->addSelectForeignKey($field);
					}
					else if( is_array($field) ){
						switch ($field['type']){
							case 'string':
								$html .= ( $field['isPassword'] ) ? $this->addPassword($field) : $this->addInput($field);
								break;
							case 'date':
								$html .= $this->addSimpleDate($field);
								break;
							case 'datetime':
								$html .= $this->addDateTime($field);
								break;
							case 'boolean':
								$html .= $this->addCheckbox($field);
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
							case 'simpletext':
								$html .= $this->addTextarea($field);
								break;
							case 'image':
								$html .= $this->addImage($field);
								break;
							case 'upload':
								$html .= $this->addUpload($field);
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
		$this->throwException('The fields given is not valid');
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

	private function addSimpleDate($field){
		return \View::make($this->base_theme.'.date', compact('field'))->render();
	}

	private function addSelect($field){
		return \View::make($this->base_theme.'.select', compact('field'))->render();
	}

	private function addCheckbox($field){
		return \View::make($this->base_theme.'.checkbox', compact('field'))->render();
	}

	private function addSelectForeignKey($field){
		return \View::make($this->base_theme.'.select-foreign', compact('field'))->render();
	}

	private function addMultiSelectManyRelation($relations){
		return \View::make($this->base_theme.'.multiple-select', compact('relations'))->render();
	}

	private function addTextEditor($field){
		return \View::make($this->base_theme.'.textedit', compact('field'))->render();
	}

	private function addTextarea($field){
		return \View::make($this->base_theme.'.textarea', compact('field'))->render();
	}

	private function addImage($field){
		return \View::make($this->base_theme.'.image', compact('field'))->render();	
	}

	private function addUpload($field){
		return \View::make($this->base_theme.'.upload', compact('field'))->render();	
	}

}
