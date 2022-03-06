<?php

namespace App\Classe;

use App\Entity\Category;

class Search
{
    //Propriété en public pour y accéder de partout
    
    //Barre de recherche
    /**
    * @var string 
    */
    public $string = '';

    //Filtres 
    /**
    * @var Category[]
    */
    public $categories = [];
}