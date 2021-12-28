<?php
namespace Erendi\Crudgenerator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Exception;

class CrudController extends Controller
{
    public function insert(Request $request){
        $rules = array(
            'modelName' => 'required',
            'addmore.*.fieldNameForm'  => 'required',
            'addmore.*.dbTypeForm'  => 'required'
        );
        $messages = [
            'modelName.required' => 'Nama Model Wajib Diisi',
            'addmore.*.fieldNameForm.required' => 'Nama Field (Field Name) Database Wajib Diisi',
            'addmore.*.dbTypeForm.required' => 'Type Database Wajib Diisi',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if($error->fails()){
            return response()->json([
            'error'  => $error->errors()->all()
            ]);
        }
        $fillable = [];
        $dataDB = '';
        $serachAble = '';
        $validasi = '';
        $htmlCreate = '';
        $htmlEdit = '';
        $thInsert = '';
        $tdInsert = '';
        foreach($request->all()['addmore'] as $k){

            if (isset($k['fillableForm'])) {
                $fillable[] = "'".$k['fieldNameForm']."'";
            }

            if (!empty($k['validationsForm'])) {
                $validasi .= Crudgen::buildValidationData($k['fieldNameForm'], $k['validationsForm']);
            }

            if (isset($k['searchableForm'])) {
                $serachAble .= Crudgen::buildSearchable($k['fieldNameForm']);
            }

            if (isset($k['inFormForm'])) {
                $htmlCreate .= Crudgen::buildHtml($k['htmlTypeForm'],$k['fieldNameForm']);
                $htmlEdit .= Crudgen::buildHtmlEdit($k['htmlTypeForm'],$k['fieldNameForm'],strtolower($request->modelName));
            }

            if (isset($k['inIndexForm'])) {
                $thInsert .= Crudgen::buildHtmlIndex('head',ucwords($k['fieldNameForm']),'');
                $tdInsert .= Crudgen::buildHtmlIndex('body',$k['fieldNameForm'],strtolower($request->modelName));
            }

            $dataDB .= Crudgen::buildTypeData($k['dbTypeForm'],$k['fieldNameForm']);

            // $result[] = array(
            //     'fieldNameForm' => $k['fieldNameForm'],
            //     'dbTypeForm' => $k['dbTypeForm'],
            //     'validationsForm' => $k['validationsForm'],
            //     'htmlTypeForm' => $k['htmlTypeForm'],
            //     'fillableForm' => (isset($k['fillableForm'])? 'true' : 'flase'),
            //     'inFormForm' => (isset($k['inFormForm'])? 'true' : 'flase'),
            //     'inIndexForm' => (isset($k['inIndexForm'])? 'true' : 'flase'),
            //     'isForeignForm' => (isset($k['isForeignForm'])? 'true' : 'flase'),
            //     'primaryForm' => (isset($k['primaryForm'])? 'true' : 'flase'),
            //     'searchableForm' => (isset($k['searchableForm']) ? 'true' : 'flase')
            // );
        }
        $model = [
            '{{modelName}}' => ucwords($request->modelName),
            '{{modelNameVariable}}' => strtolower($request->modelName),
            '{{modelTable}}' => strtolower($request->customeTableName),
            '{{paginationForm}}' => intVal($request->paginationRecord),
            '{{modelNameSpace}}' => config('erendicrudgenerator.namespace.model'),
            '{{modelFillable}}' => implode(',',$fillable),
            '{{dataTable}}' => $dataDB,
            '{{validation}}' => !empty($validasi) ? "\$request->validate([".$validasi."]);" : '',
            '{{searchAble}}' => $serachAble,
            '{{htmlCreate}}' => $htmlCreate,
            '{{htmlEdit}}' => $htmlEdit,
            '{{htmlTh}}' => $thInsert,
            '{{htmlTd}}' => $tdInsert
        ];
        $pathslist = [
            [
                'target'=>str_replace('\\','/',config('erendicrudgenerator.path.model')),
                'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                'new'   =>ucwords($request->modelName).'.php',
                'stub'  => !empty($request->customeTableName) ? 'model.table.stub' : 'model.stub'
            ],[
                'target'=>str_replace('\\','/',config('erendicrudgenerator.path.migration')),
                'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                'new'   =>date('Y_m_d_His').'_create_'.strtolower($request->modelName).'_table.php',
                'stub'  =>!empty($request->customeTableName) ? 'migration.table.stub' : 'migration.stub'
            ],[
                'target'=>str_replace('\\','/',config('erendicrudgenerator.path.controller').'Be/'),
                'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                'new'   =>ucwords($request->modelName).'Controller.php',
                'stub'  =>'controller.stub'
            ],[
                'view' => [
                    [
                        'target'=>str_replace('\\','/',config('erendicrudgenerator.path.views').'be/').strtolower($request->modelName).'/',
                        'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                        'stub'=>'create.stub',
                        'new' =>'create.blade.php'
                    ],[
                        'target'=>str_replace('\\','/',config('erendicrudgenerator.path.views').'be/').strtolower($request->modelName).'/',
                        'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                        'stub'=>'edit.stub',
                        'new' =>'edit.blade.php'
                    ],[
                        'target'=>str_replace('\\','/',config('erendicrudgenerator.path.views').'be/').strtolower($request->modelName).'/',
                        'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                        'stub'=>'index.stub',
                        'new' =>'index.blade.php'
                    ]
                ]
            ]
        ];

        $exec = Crudgen::createCrud($pathslist,$model);
        if ($exec) {
            Artisan::call('migrate');
            return response()->json(['success'=>'Data Berhasil Diproses']);
        }
    }
}
