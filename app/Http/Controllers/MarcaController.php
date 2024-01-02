<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use Illuminate\Http\Request;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{   
    public function __construct(Marca $marca){

        $this->marca = $marca;

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        //Utilização do modelo MarcaRepository criado para encapsular os modelos de consulta
        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        }else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }


        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro);
        }


        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);
        }

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
        
    }

    public function getModelo(){
        $marcaRepository = new MarcaRepository($this->marca);
        return response()->json($marcaRepository->getResultado(), 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');
        $marca = $this->marca->create(
            [
                'nome' => $request->nome,
                'imagem' => $imagem_urn
            ]
        );

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     * @param Ingeter
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);

        if($marca === null){
            return response()->json(['erro' => 'O registro não existe !'], 404);
        }

        return response()->json($marca, 200);
        
    }

    /**
     * Update the specified resource in storage.
     * @param Integer
     */
    public function update(Request $request, $id){
       $request->validate($this->marca->rules(), $this->marca->feedback());
        $marca = $this->marca->find($id);

        if($marca === null)
        {
            return response()->json(['erro' => 'O registro não existe !'], 404);
        }


        if($request->method() === 'PATCH')
        {

            $regrasDinamicas = array();

            foreach($marca->rules() as $input => $regra){

                if(array_key_exists($input, $request->all())){

                    $regrasDinamidas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
        }else{
            
            $request->validate($marca->rules(), $marca->feedback());

        }

        $marca->fill($request->all());
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens', 'public');
            $marca->imagem = $imagem_urn;
        }

        $marca->save();
        return response()->json($marca, 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $marca= $this->marca->find($id);

        if($marca === null){
            return response()->json(['erro' => 'O registro não existe !'], 404);
        }

        //Remove um arquivo antigo, caso algum arquivo tenha sido encaminhado no request
        Storage::disk('public')->delete($marca->imagem);
        $marca->delete();

        return response()->json(['msg' => 'A marca foi removida com sucesso !'],200);
    }
}
