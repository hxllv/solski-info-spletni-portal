<?php

return [
    'required' => 'Obvezno polje.',
    'string' => 'Polje mora biti tekst.',
    'numeric' => 'Polje mora biti število.',
    'email' => 'Polje mora biti veljaven e-poštni naslov.',
    'unique' => 'Vrednost polja mora biti unikatna.',
    'array' => 'Polje mora biti tipa "Array".',
    'max' => [
        'numeric' => 'Polje ne sme biti večje kot :max.',
        'file'    => 'Polje ne sme biti večje kot :max kilobajtov.',
        'string'  => 'Polje ne sme biti večje kot :max znakov.',
        'array'   => 'Polje ne sme imeti več kot :max elementov.',
    ],
];