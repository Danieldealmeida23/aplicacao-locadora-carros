<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class AbstractRepository {
    public function __construct(Model $model){
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos){
        $this->model = $this->model->with($atributos);
        //Sempre atribuir o ultimo estado ao objeto
    }

    public function filtro($filtros){
        $filtros = explode(';', $filtros);

        foreach($filtros as $key => $condicao){
                
            $c = explode(':', $condicao);

            $this->model = $this->model->where($c[0], $c[1], $c[2]);
            //A query está sendo montada
        }
    }

    public function selectAtributos($atributos){
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultadoPaginado($numeroRegistroPorPagina){
        return $this->model->paginate($numeroRegistroPorPagina);
    }

    public function getResultado(){
        return $this->model->get();
    }

    //Criar uma função para retornar a query no estado final do objeto (filtros)
}

?>