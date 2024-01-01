<?php

namespace App\Repositories;

class MarcaRepository extends AbstractRepository{
    public function getResultado(){
        return $this->model->get();
    }
}


?>