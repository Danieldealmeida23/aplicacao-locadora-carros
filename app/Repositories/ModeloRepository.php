<?php

namespace App\Repositories;


class ModeloRepository extends AbstractRepository{
    public function getResultado(){
        return $this->model->get();
    }
}


?>