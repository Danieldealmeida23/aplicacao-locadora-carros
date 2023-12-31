<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome','imagem'];
    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'file|mimes:png,jpg,jpeg'
        ];
    }

    public function feedback()
    {
        return [
            'required'=> 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O campo nome precisa ter no mínimo 3 letras',
            'imagem' => 'O arquivo deve conter uma imagem'
        ];
    }
    public function modelos(){
        return $this->hasMany('App\Models\Modelo');
    }
}
