<?php

namespace Gophry\DTO;

abstract class RequestDTO extends DTO implements RequestDTOInterface {
    
	private $data;
	
    public function bind(array $data) {
		$this->data = $data;
        foreach($data as $key => $value) {
            if (property_exists($this, $key) && $this->{$key} instanceof RequestDTOInterface) {
                $this->{$key}->bind($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }
	
	public function toAssoc() {
		return $this->data;
	}
    
}