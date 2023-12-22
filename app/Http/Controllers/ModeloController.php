<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Repositories\ModeloRepository;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        }else{
            $modeloRepository = $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if($request->has('filtro')){
            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $modeloRepository->selectAtributos($request->atributos);
        }

        //$modelo = $this->modelo->all();

        return response()->json($modeloRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create(
            [
                'marca_id' => $request->marca_id,
                'nome' => $request->nome,
                'imagem' => $imagem_urn,
                'numero_portas' => $request->numero_portas,
                'lugares' => $request->lugares,
                'air_bag' => $request->air_bag,
                'abs' => $request->abs
            ]
        );

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {   
        
        $modelo = $this->modelo->with('marca')->find($id);

        if($modelo === null){
            return response()->json(['erro' => 'O registro não existe !'], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $modelo = $this->marca->find($id);

        if($modelo === null)
        {
            return response()->json(['erro' => 'O registro não existe !'], 404);
        }

        if($request->method() === 'PATCH')
        {

            $regrasDinamicas = array();

            foreach($modelo->rules() as $input => $regra){

                if(array_key_exists($input, $request->all())){

                    $regrasDinamidas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        }else{
            
            $request->validate($modelo->rules());

        }
        //Remove um arquivo antigo, caso algum arquivo tenha sido encaminhado no request
        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();

        
        return response()->json($modelo, 200);

        /*
        $modelo->update(            
            [
                'marca_id' => $request->marca_id,
                'nome' => $request->nome,
                'imagem' => $imagem_urn,
                'numero_portas' => $request->numero_portas,
                'lugares' => $request->lugares,
                'air_bag' => $request->air_bag,
                'abs' => $request->abs
            ]
        );
        */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $modelo= $this->modelo->find($id);

        if($modelo === null){

            return response()->json(['erro' => 'O registro não existe !'], 404);
        }

        //Remove um arquivo antigo, caso algum arquivo tenha sido encaminhado no request
        Storage::disk('public')->delete($modelo->imagem);
        $modelo->delete();

        return response()->json(['msg' => 'O modelo foi removida com sucesso !'],200);
    }
}
