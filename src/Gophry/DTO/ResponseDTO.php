<?php

namespace Gophry\DTO;

abstract class ResponseDTO extends DTO implements ResponseDTOInterface {
    
    public function toArray() {
        $properties = get_object_vars($this);
        $result = array();
        foreach ($properties as $property => $value) {
            if ($value !== null) {
                if ($value instanceof IResponseDTO) {
                    $result[$property] = $value->toArray();  
                } else if (is_array($value)) {
                    if (array_keys($value) !== range(0, count($value) - 1)) {
                        $result[$property] = $value;
                    } else {
                        $array = [];
                        foreach ($value as $item) {
                            $array[] = $item instanceof IResponseDTO ? $item->toArray() : $item;
                        }
                        $result[$property] = $array;
                    }
                } else {
                    $result[$property] = $value;
                }
            }
        }
        return $result;
    }
    
}