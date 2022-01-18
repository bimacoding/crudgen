<?php

namespace Erendi\Crudgenerator;

use Exception;

class Crudgen {
    public static function render(){
        return view('crud::crud');
    }

    public static function renderjs(){
        return view('crud::scripts');
    }

    public static function buildScriptUploadFileInController($name,string $type)
    {
        $out = '';
        if($type == 'store'){
            $out .= "\t\t\$file = \$request->file('".$name."');\r\n";
            $out .= "\t\t\$input = \$request->all();\r\n";
            $out .= "\t\tif (file_exists(\$file)) {\r\n";
            $out .= "\t\t\t\$nama_file = \$file->getClientOriginalName();\r\n";
            $out .= "\t\t\t\$pathfile = Storage::putFileAs(\r\n";
            $out .= "\t\t\t\t'public/upload',\r\n";
            $out .= "\t\t\t\t\$file,\r\n";
            $out .= "\t\t\t\ttime().\"_\".\$nama_file,\r\n";
            $out .= "\t\t\t);\r\n";
            $out .= "\t\t\t}\r\n";
            $out .= "\t\t\$input['".$name."'] = basename(\$pathfile);\r\n";
        }else{
            $out .= "\t\t\$file = \$request->file('".$name."');\r\n";
            $out .= "\t\t\$input = \$request->except('imagenow');\r\n";
            $out .= "\t\tif (file_exists(\$file)) {\r\n";
            $out .= "\t\t\tStorage::disk('public')->delete('upload/'.\$request->imagenow);\r\n";
            $out .= "\t\t\t\$nama_file = \$file->getClientOriginalName();\r\n";
            $out .= "\t\t\t\$pathfile = Storage::putFileAs(\r\n";
            $out .= "\t\t\t\t'public/upload',\r\n";
            $out .= "\t\t\t\t\$file,\r\n";
            $out .= "\t\t\t\ttime().\"_\".\$nama_file,\r\n";
            $out .= "\t\t\t);\r\n";
            $out .= "\t\t\t\$input['".$name."'] = basename(\$pathfile);\r\n";
            $out .= "\t\t}else{\r\n";
            $out .= "\t\t\t\$input['".$name."'] = \$request->imagenow;\r\n";
            $out .= "\t\t}\r\n";

        }
        return $out;
    }

    public static function buildValidationData($name, $valid){
        return "'".$name."' => '".$valid."',";
    }

    public static function buildSearchable($field){
        return "->where('".$field."', 'like', \"%{\$request->keyword}%\")";
    }

    public static function createRelationModels(array $arr){
        $out = '';
        foreach($arr as $k => $r){
            $out .= (new Crudgen)->buildRelation($r['relType'],$r['relModel'],$r['relForeign'],$r['relLocal']);
        }
        return $out;
    }

    public static function buildRelationHtmlCreate($modelname,$foreign,$local,string $fileshow = null)
    {
        $out = '';
        $out .= "<div class=\"col-12\">";
        $out .= "<div class=\"form-group\">";
        $out .= "<label>".$modelname."</label>";
        $out .= "<select class=\"form-control\" name=\"".$foreign."\">\n";
        $out .= "@foreach(\$".strtolower($modelname)." as \$result)\n";
        $out .= "\t<option value=\"{{\$result->".$local."}}\">{{\$result->".$fileshow."}}</option>\n";
        $out .= "@endforeach\n";
        $out .= "</select>\n";
        $out .= "</div>";
        $out .= "</div>";
        return $out;
    }

    public static function buildRelationHtmlEdit($modelname,$foreign,$local,string $fileshow = null,$m)
    {
        $out = '';
        $out .= "<div class=\"col-12\">";
        $out .= "<div class=\"form-group\">";
        $out .= "<label>".$modelname."</label>";
        $out .= "<select class=\"form-control\" name=\"".$foreign."\">\n";
        $out .= "@foreach(\$".strtolower($modelname)." as \$result)\n";
        $out .= "\t<option value=\"{{\$result->".$local."}}\" {!! \$".$m."->".$foreign." == \$result->".$local." ? 'selected' : '' !!}>{{\$result->".$fileshow."}}</option>\n";
        $out .= "@endforeach\n";
        $out .= "</select>\n";
        $out .= "</div>";
        $out .= "</div>";
        return $out;
    }

    public static function renderRelationFormCreate(array $arr)
    {
        $out = '';
        foreach($arr as $r){
            $out .= (new Crudgen)->buildRelationHtmlCreate($r['relModel'],$r['relForeign'],$r['relLocal'],$r['relFieldShow']);
        }
        return $out;
    }

    public static function renderRelationFormEdit(array $arr, string $m)
    {
        $out = '';
        foreach($arr as $r){
            $out .= (new Crudgen)->buildRelationHtmlEdit($r['relModel'],$r['relForeign'],$r['relLocal'],$r['relFieldShow'],$m);
        }
        return $out;
    }

    public static function buildRelation($type,$modelname,$foreign,$local){
        $out = '';

        switch ($type) {
            case 'hasOne':
                $out .= "\tpublic function ".strtolower($modelname)."(){\r\n";
                $out .= "\t\treturn \$this->".$type."(".ucwords($modelname)."::class,'".$foreign."','".$local."');\r\n";
                $out .= "\t}\t\n";
                break;
            case 'hasMany':
                $out .= "\tpublic function ".strtolower($modelname)."(){\r\n";
                $out .= "\t\treturn \$this->".$type."(".ucwords($modelname)."::class);\r\n";
                $out .= "\t}\t\n";
                break;
            case 'belongsTo':
                $out .= "\tpublic function ".strtolower($modelname)."(){\r\n";
                $out .= "\t\treturn \$this->".$type."(".ucwords($modelname)."::class,'".$foreign."');\r\n";
                $out .= "\t}\t\n";
                break;
            case 'belongsToMany':
                $out .= "\tpublic function ".strtolower($modelname)."(){\r\n";
                $out .= "\t\treturn \$this->".$type."(".ucwords($modelname)."::class);\r\n";
                $out .= "\t}\t\n";
                break;
            default:
                $out .= '';
                break;
        }
        return $out;
    }

    public static function buildHtml($type,$name, $enumfield = null){
        $out = '';
        if($enumfield!=null){
            $opt = '';
            foreach(explode(',',$enumfield) as $ef){ $opt .= "\t<option value=\"".str_replace(['\'',','],'', $ef)."\">".str_replace(['\'',','],'', $ef)."</option>\n"; }
        }
        switch ($type) {
            case 'text':
            case 'file':
            case 'number':
            case 'password':
            case 'email':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<input name=\"".$name."\" class=\"form-control\" type=\"".$type."\" value=\"{{ old('".$name."') }}\">";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'textarea':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<textarea name=\"".$name."\" class=\"form-control\" rows=\"10\">{{ old('".$name."') }}</textarea>";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'select':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<select name=\"".$name."\" class=\"form-control\">\n<option value=\"-\">-- select --</option>\n".$opt."</select>";
                $out .= "</div>";
                $out .= "</div>";
                break;
            default:
                $out .= '';
                break;
        }
        return $out;
    }

    public static function buildHtmlEdit($type,$name,$param, $enumfield = null){
        $out = '';
        if($enumfield!=null){
            $opt = '';
            foreach(explode(',',$enumfield) as $ef){
                $opt .= "\t<option value=\"".str_replace(['\'',','],'', $ef)."\" {!! (\$".$param."->".$name." == '".str_replace(['\'',','],'', $ef).") ? 'selected' : '') !!}>".str_replace(['\'',','],'', $ef)."</option>\n";
            }
        }
        switch ($type) {
            case 'text':
            case 'number':
            case 'password':
            case 'email':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<input name=\"".$name."\" class=\"form-control\" type=\"".$type."\" value=\"{{\$".$param."->".$name."}}\">";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'textarea':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<textarea name=\"".$name."\" class=\"form-control\" rows=\"10\">{{\$".$param."->".$name."}}</textarea>";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'file':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<input name=\"".$name."\" class=\"form-control\" type=\"".$type."\">";
                $out .= "<input type=\"hidden\" name=\"imagenow\" class=\"form-control\" value=\"{{\$".$param."->".$name."}}\">";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'select':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<select name=\"".$name."\" class=\"form-control\">\n".$opt."</select>";
                $out .= "</div>";
                $out .= "</div>";
                break;
            default:
                $out .= '';
                break;
        }
        return $out;
    }

    public static function buildHtmlIndex($type,$data,$var){
        $out = '';
        if ($type == 'body' && $var != '') {
            $out .= "<td class=\"p-1\">{{\$".$var."->".$data."}}</td>\n";
        }else if($type == 'head' && $var == ''){
            $out .= "<th class=\"p-1\">".$data."</th>\n";
        }
        return $out;
    }

    public static function buildTypeData($type,$name,$enumfield = null,$foreign){
        $out = '';
        switch ($type) {
            case 'string':
                $out .= "\t\t\t\$table->".$type."('".$name."',255)->nullable();\r\n";
                break;
            case 'text':
                $out .= "\t\t\t\$table->".$type."('".$name."')->nullable();\r\n";
                break;
            case 'increments':
                $out .= "\t\t\t\$table->bigIncrements('".$name."');\r\n";
                break;
            case 'integer':
                $out .= "\t\t\t\$table->".$type."('".$name."')->nullable()->default(0);\r\n";
                break;
            case 'unsignedInteger':
                $out .= "\t\t\t\$table->unsignedInteger('".$name."');\n";
                $out .= "\t\t\t\$table->foreign('".$name."')->references('id')->on('".explode('_',$name)[0]."s');\r\n";
                break;
            case 'unsignedBigInteger':
                $out .= "\t\t\t\$table->unsignedBigInteger('".$name."');\n";
                $out .= "\t\t\t\$table->foreign('".$name."')->references('id')->on('".explode('_',$name)[0]."s');\r\n";
                break;
            case 'biginteger':
                $out .= "\t\t\t\$table->bigInteger('".$name."')->default(0);\r\n";
                break;
            case 'timestamps':
                $out .= "\t\t\t\$table->".$type."('".$name."')->nullable();\r\n";
                break;
            case 'date':
                $out .= "\t\t\t\$table->".$type."('".$name."');\r\n";
                break;
            case 'longtext':
                $out .= "\t\t\t\$table->longText('".$name."')->nullable();\r\n";
                break;
            case 'mediumtext':
                $out .= "\t\t\t\$table->mediumText('".$name."')->nullable();\r\n";
                break;
            case 'boolean':
                $out .= "\t\t\t\$table->".$type."('".$name."')->nullable();\r\n";
                break;
            case 'float':
                $out .= "\t\t\t\$table->".$type."('".$name."',10,0)->nullable();\r\n";
                break;
            case 'double':
                $out .= "\t\t\t\$table->".$type."('".$name."',10,0)->nullable();\r\n";
                break;
            case 'enum':
                $out .= "\t\t\t\$table->".$type."('".$name."',[$enumfield])->nullable()->default(".explode(',',$enumfield)[0].");\r\n";
                break;
            default:
                $out .= "";
                break;
        }
        return $out;
    }

    public static function bindRelationToController(array $arr)
    {
        $out = '';
        foreach($arr as $k){
            $out .= "\t\t\$".strtolower($k['relModel'])." = ".$k['relModel']."::all();\n";
        }
        return $out;
    }

    public static function bindRelationToCompact(array $arr)
    {
        $out = '';
        foreach($arr as $k){
            $out .= ",'".strtolower($k['relModel'])."'";
        }
        return $out;
    }

    public static function bindRelationNamespace(array $arr)
    {
        $out = '';
        foreach($arr as $k){
            $out .= "use ".config('erendicrudgenerator.namespace.model')."\\".$k['relModel'].";\n";
        }
        return $out;
    }

    public static function renameModel(string $str){
        return str_replace(' ','',ucwords(str_replace(['_','-'],' ',$str)));
    }

    public static function createCrud(array $arr,array $rep){
        foreach($arr as $a){
            if(isset($a['view'])){
                (new Crudgen)->createCrud($a['view'],$rep);
            }else{
                if (!file_exists($a['target'])) {
                    mkdir($a['target'], 0775, true);
                }
                if(!is_dir($a['source'])) {
                    throw new Exception("source directory not found");
                }
                if(!file_exists($a['source'].$a['stub'])){
                    throw new Exception("file not found");
                }
                $target = $a['target'].$a['new'];
                $source = $a['source'].$a['stub'];
                $data = file_get_contents($source);
                $newdata = strtr($data, $rep);
                file_put_contents($target, $newdata);
            }
        }
        return true;
    }

    public static function addRoute($string){
        $data = file_get_contents(base_path('routes/web.php'));
        $newdata =  preg_replace('!/\*.*?\*/!s', $string, $data);
        file_put_contents(base_path('routes/web.php'), $newdata);
        return true;
    }
}
