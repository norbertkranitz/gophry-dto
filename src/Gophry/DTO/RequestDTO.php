<?php

namespace Gophry\DTO;

abstract class RequestDTO extends DTO implements RequestDTOInterface {

    protected $data;

    public function bind(array $data) {
        $this->data = $data;
        foreach ($data as $key => $value) {
            if (property_exists($this, $key) && $this->{$key} instanceof RequestDTOInterface) {
                if (is_string($value) && $newValue = json_decode($value, true)) {
                    $this->{$key}->bind($newValue);
                } else {
                    $this->{$key}->bind($value);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function toAssoc() {
        return $this->data;
    }

}