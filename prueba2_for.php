¿Puedes encontrar cuál es el error en este código?
* La funcion slice elimina de la array la posición, pero en el bucle continua en la siguiente array,

¿Puedes añadir una única línea para hacerlo funcionar correctamente?
Restando $i - 1 para continuar correctamente con el for 
* $i--
* Línea 36

¿Qué problemas de rendimiento detectas?
El rendimiento se fe afectado directamente en el tamaño de la array que envías,

¿Cómo implementarías esta función?
How would you implement that function?
* usando la funcion array_unique nativa de php
* $resultado = array_unique($array);


returns a new array removing the duplicate numbers (keeping only the first one):
For example: removeDuplicatedNumbers(array(5,2,3,6,7,2,3,5,9,4,5)) returns

array(5, 2, 3, 6, 7, 9, 4);

<?php

function removeDuplicatedNumbers($array)
{
    $seenNumbers = array();
    for ($i = 0; $i < count($array); $i++) {
        $number = $array[$i];
        if (in_array($number, $seenNumbers)) {
            $array = array_merge(
                array_slice($array, 0, $i),
                array_slice($array, $i + 1, count($array) - $i - 1)
            );
            $i--; // <-- solucion del bug
        } else {
            $seenNumbers[] = $number;
        }
    }
    return $array;
}


$result = removeDuplicatedNumbers(array(5,2,3,6,7,2,3,5,9,4,5));

print_r($result);
