<?php namespace DevSwert\LaCrud\Theme;

use DevSwert\LaCrud\Utils;

final class FormBuilder{
	use Utils;

	/**
	 * Base path for load templates.
	 *
	 * @var string
	 */
	private $base_theme;

	/**
	 * Init the instance creting or defining the base_theme 
	 * for the class.
	 *
	 * @param $base_theme  string    
	 * @param $theme       string
	 * @return void
	 */
	public function __construct($base_theme,$theme){
		$base_theme = $base_theme.'/forms';

		if(!is_dir($base_theme)){
			$this->throwException("Don't exist a 'forms' folder in your Theme for LaCrud");
		}
		$this->base_theme = 'lacrud::'.$theme.'.forms.';
	}

	/**
	 * Generate the HTML form according to $fields
	 *
	 * @param $fields    array() 
	 * @return $html string / void on Exception
	 */
	public function generateFormAddOrEdit($fields){
		if(is_array($fields) && count($fields) > 0){
			$html = '';
			foreach ($fields as $key => $field){
				if( $key === 'hasManyRelation' && is_array($fields['hasManyRelation'])){
					foreach ($fields['hasManyRelation'] as $relation) {
						$html .= $this->addMultiSelectManyRelation($relation);
					}
				}
				else if(!$field['isAutoincrement']){
					if(count($field['hasForeignKeys']) > 0){
						$html .= $this->addSelectForeignKey($field);
					}
					else if( is_array($field) ){
						switch ($field['type']){
							case 'string':
								$html .= ( $field['isEncrypted'] ) ? $this->addPassword($field) : $this->addInput($field);
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
							case 'time':
								$html .= $this->addTime($field);
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
	}

	/**
	 * Generate the HTML for Basic ipnut
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addInput($field){
		return \View::make($this->base_theme.'.input', compact('field'))->render();
	}

	/**
	 * Generate the HTML for password/encrypted ipnut
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addPassword($field){
		return \View::make($this->base_theme.'.password', compact('field'))->render();
	}

	/**
	 * Generate the HTML for numeric ipnut
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addInteger($field){
		return \View::make($this->base_theme.'.integer', compact('field'))->render();
	}

	/**
	 * Generate the HTML for DateTime ipnut
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addDateTime($field){
		return \View::make($this->base_theme.'.datetime', compact('field'))->render();
	}

	/**
	 * Generate the HTML for time ipnut
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addTime($field){
		return \View::make($this->base_theme.'.time', compact('field'))->render();
	}

	/**
	 * Generate the HTML for Only date ipnut
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addSimpleDate($field){
		return \View::make($this->base_theme.'.date', compact('field'))->render();
	}

	/**
	 * Generate the HTML for select/combobox
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addSelect($field){
		return \View::make($this->base_theme.'.select', compact('field'))->render();
	}

	/**
	 * Generate the HTML for checkbox input / Boolean type
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addCheckbox($field){
		return \View::make($this->base_theme.'.checkbox', compact('field'))->render();
	}

	/**
	 * Generate the HTML for select with foereign data loaded
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addSelectForeignKey($field){
		return \View::make($this->base_theme.'.select-foreign', compact('field'))->render();
	}

	/**
	 * Generate the HTML for MultiSelect
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addMultiSelectManyRelation($relations){
		return \View::make($this->base_theme.'.multiple-select', compact('relations'))->render();
	}

	/**
	 * Generate the HTML for Texteditor
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addTextEditor($field){
		return \View::make($this->base_theme.'.textedit', compact('field'))->render();
	}

	/**
	 * Generate the HTML for basic textarea
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addTextarea($field){
		return \View::make($this->base_theme.'.textarea', compact('field'))->render();
	}

	/**
	 * Generate the HTML for input file or image view
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addImage($field){
		return \View::make($this->base_theme.'.image', compact('field'))->render();	
	}

	/**
	 * Generate the HTML for input file or link ti resource
	 *
	 * @param $field
	 * @return $html string
	 */
	private function addUpload($field){
		return \View::make($this->base_theme.'.upload', compact('field'))->render();	
	}

}
