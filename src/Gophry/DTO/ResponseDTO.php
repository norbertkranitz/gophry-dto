<?php

namespace Gophry\DTO;

abstract class ResponseDTO extends DTO implements ResponseDTOInterface {
    
    public function toArray() {
        $properties = get_object_vars($this);
        $result = array();
        foreach ($properties as $property => $value) {
            $result[$property] = $value instanceof ResponseDTOInterface ? $value->toArray() : $value;
        }
        return $result;
    }
    
}