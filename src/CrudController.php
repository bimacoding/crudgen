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
            'modelName' => 'required|alpha',
            'addmore.*.fieldNameForm'  => 'required',
            'addmore.*.dbTypeForm'  => 'required'
        );
        $messages = [
            'modelName.required' => 'Nama Model Wajib Diisi',
            'modelName.alpha' => 'Nama Model Harus A-Z symbol tidak diizinkan',
            'addmore.*.fieldNameForm.required' => 'Nama Field (Field Name) Database Wajib Diisi',
            'addmore.*.dbTypeForm.required' => 'Type Database Wajib Diisi',
        ];
        $error = Validator::make($request->all(), $rules, $messages);
        if($error->fails()){
            return response()->json([
            'error'  => $error->errors()->all()
            ]);
        }
        // dd(isset($request->relations_column));

        $fillable = [];
        $dataDB = '';
        $serachAble = '';
        $validasi = '';
        $htmlCreate = '';
        $htmlEdit = '';
        $thInsert = '';
        $tdInsert = '';
        $hrc = '';
        $hre = '';
        $rlt = '';
        $brt = '';
        $brc = '';
        $brn = '';
        $controllerFileStore = '';
        $controllerFileUpdate = '';
        foreach($request->all()['addmore'] as $k){

            if (isset($k['fillableForm'])) {
                $fillable[] = "'".$k['fieldNameForm']."'";
            }

            if (!empty($k['validationsForm'])) {
                $validasi .= Crudgen::buildValidationData($k['fieldNameForm'], $k['validationsForm']);
            }

            if (isset($k['searchableForm']) && !isset($k['isForeignForm'])) {
                $serachAble .= Crudgen::buildSearchable($k['fieldNameForm']);
            }

            if($k['htmlTypeForm']=='file'){
                $controllerFileStore .= Crudgen::buildScriptUploadFileInController($k['fieldNameForm'],'store');
                $controllerFileUpdate .= Crudgen::buildScriptUploadFileInController($k['fieldNameForm'],'update');
            }

            if (isset($k['inFormForm']) && !isset($k['isForeignForm'])) {
                if($k['dbTypeForm'] == 'enum' && $k['enumFieldForm']!='' || $k['enumFieldForm']!=null){
                    $htmlCreate .= Crudgen::buildHtml($k['htmlTypeForm'],$k['fieldNameForm'],$k['enumFieldForm']);
                    $htmlEdit .= Crudgen::buildHtmlEdit($k['htmlTypeForm'],$k['fieldNameForm'],strtolower($request->modelName),$k['enumFieldForm']);
                }else{
                    $htmlCreate .= Crudgen::buildHtml($k['htmlTypeForm'],$k['fieldNameForm']);
                    $htmlEdit .= Crudgen::buildHtmlEdit($k['htmlTypeForm'],$k['fieldNameForm'],strtolower($request->modelName));
                }
            }

            if (isset($k['inIndexForm']) && !isset($k['isForeignForm'])) {
                $thInsert .= Crudgen::buildHtmlIndex('head',ucwords($k['fieldNameForm']),'');
                $tdInsert .= Crudgen::buildHtmlIndex('body',$k['fieldNameForm'],strtolower($request->modelName));
            }
            $dataDB .= (isset($k['isForeignForm']) ? Crudgen::buildTypeData($k['dbTypeForm'],$k['fieldNameForm'],$k['enumFieldForm'],true) : Crudgen::buildTypeData($k['dbTypeForm'],$k['fieldNameForm'],$k['enumFieldForm'],true));
            if (isset($request->relations_column)) {
                $hrc .= ((Crudgen::checkrel($request->relations_column,$k['fieldNameForm'])) ? Crudgen::renderRelationFormCreate($request->relations_column) : null);
                $hre .= ((Crudgen::checkrel($request->relations_column,$k['fieldNameForm'])) ? Crudgen::renderRelationFormEdit($request->relations_column,strtolower($request->modelName)) : null);
                $rlt .= ((Crudgen::checkrel($request->relations_column,$k['fieldNameForm'])) ? Crudgen::createRelationModels($request->relations_column) : null);
                $brt .= ((Crudgen::checkrel($request->relations_column,$k['fieldNameForm'])) ? Crudgen::bindRelationToController($request->relations_column) : null);
                $brc .= ((Crudgen::checkrel($request->relations_column,$k['fieldNameForm'])) ? Crudgen::bindRelationToCompact($request->relations_column) : null);
                $brn .= ((Crudgen::checkrel($request->relations_column,$k['fieldNameForm'])) ? Crudgen::bindRelationNamespace($request->relations_column) : null);
            }

        }

        // dd(array_merge($request->all()['addmore'],$request->relations_column));

        $model = [
            '{{modelName}}' => ucwords($request->modelName),
            '{{modelNameVariable}}' => strtolower($request->modelName),
            '{{modelTable}}' => strtolower($request->customTableName),
            '{{renameModelTable}}' => (isset($request->customTableName) ? Crudgen::renameModel($request->customTableName) : null),
            '{{paginationForm}}' => intVal($request->paginationRecord),
            '{{modelNameSpace}}' => config('erendicrudgenerator.namespace.model'),
            '{{modelFillable}}' => implode(',',$fillable),
            '{{fileStoreScript}}'=>$controllerFileStore,
            '{{fileUpdateScript}}'=>$controllerFileUpdate,
            '{{dataTable}}' => $dataDB,
            '{{validation}}' => !empty($validasi) ? "\t\t\$validator = Validator::make(\$request->all(), [".$validasi."]);\r\n\t\tif(\$validator->fails()){\r\n\t\t\treturn redirect()->back()->withErrors(\$validator)->withInput(\$request->all);\r\n\t\t}\r\n" : '',
            '{{searchAble}}' => $serachAble,
            '{{htmlCreate}}' => $htmlCreate,
            '{{htmlEdit}}' => $htmlEdit,
            '{{htmlTh}}' => $thInsert,
            '{{htmlTd}}' => $tdInsert,
            '{{htmlRelationCreate}}' => $hrc,
            '{{htmlRelationEdit}}' => $hre,
            '{{relations}}' => (!isset($rlt) ? $rlt : (!isset($request->relations_column)?'':Crudgen::createRelationModels($request->relations_column))),
            '{{modelRelation}}' => $brt,
            '{{modelRelationVariable}}' => $brc,
            '{{modelRelationNamespace}}' => $brn,
            '{{selectWithRelations}}' => (isset($k['isForeignForm']) ? 'ccc' : '')
        ];

        if (isset($request->customTableName) && !isset($request->relations_column)) {
            $stubmodelfile = 'model.table.stub';
        }elseif (!isset($request->customTableName) && isset($request->relations_column)) {
            $stubmodelfile = 'model.relations.stub';
        }elseif (isset($request->customTableName) && isset($request->relations_column)) {
            $stubmodelfile = 'model.table.relations.stub';
        }elseif(!isset($request->customTableName) && !isset($request->relations_column)){
            $stubmodelfile = 'model.stub';
        }
        $pathslist = [
            [
                'target'=>str_replace('\\','/',config('erendicrudgenerator.path.model')),
                'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                'new'   =>ucwords($request->modelName).'.php',
                'stub'  =>$stubmodelfile
            ],[
                'target'=>str_replace('\\','/',config('erendicrudgenerator.path.migration')),
                'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                'new'   => (isset($request->customTableName) ? date('Y_m_d_His').'_create_'.strtolower($request->customTableName).'_table.php' : date('Y_m_d_His').'_create_'.strtolower($request->modelName).'_table.php'),
                'stub'  => (isset($request->customTableName) ? 'migration.table.stub' : 'migration.stub')
            ],[
                'target'=>str_replace('\\','/',config('erendicrudgenerator.path.controller').'Be/'),
                'source'=>str_replace('\\','/',config('erendicrudgenerator.path.stub')),
                'new'   =>ucwords($request->modelName).'Controller.php',
                'stub'  =>(!empty($controllerFileStore) && !empty($controllerFileUpdate)) ? 'controller.fileupload.stub' : 'controller.stub'
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
            try {
                Crudgen::addRoute("Route::resource('/".strtolower($request->modelName)."s', Be\\".ucwords($request->modelName)."Controller::class);\n\t/*new route*/");
                Artisan::call('crud:init');
                return response()->json(['success'=>'Data Berhasil Diproses']);
            } catch (Exception $e) {
                throw response()->json(['error'=>'Gagal Memproses Data, silahkan cek folder migration, model, dan controller anda. mungkin file/data dengan nama model sudah dibuat sebelumnya']);
            }
        }else{
            return response()->json(['error'=>'Gagal Membuat Generator Silahkan Ulang Kembali']);
        }
    }
}
