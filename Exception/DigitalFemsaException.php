<?php

namespace DigitalFemsa\Payments\Exception;

class DigitalFemsaException extends \Exception
{
    public const INVALID_PHONE_MESSAGE = 'Télefono no válido. 
        El télefono debe tener al menos 10 carácteres.
        Los caracteres especiales se desestimaran, solo se puede ingresar como 
        primer carácter especial: +';
}
