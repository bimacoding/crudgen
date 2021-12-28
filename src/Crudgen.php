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

    public static function buildValidationData($name, $valid){
        return "'".$name."' => '".$valid."',";
    }

    public static function buildSearchable($field){
        return "->where('".$field."', 'like', \"%{\$request->keyword}%\")";
    }

    public static function buildHtml($type,$name){
        $out = '';
        switch ($type) {
            case 'text':
            case 'file':
            case 'number':
            case 'password':
            case 'email':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<input name=\"".$name."\" class=\"form-control\" type=\"".$type."\">";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'textarea':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<textarea name=\"".$name."\" class=\"form-control\" rows=\"10\"></textarea>";
                $out .= "</div>";
                $out .= "</div>";
                break;
            default:
                $out .= '';
                break;
        }
        return $out;
    }

    public static function buildHtmlEdit($type,$name,$param){
        $out = '';
        switch ($type) {
            case 'text':
            case 'number':
            case 'password':
            case 'email':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<input name=\"".$name."\" class=\"form-control\" type=\"".$type."\" value=\"{{".$param."->".$name."}}\">";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'textarea':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<textarea name=\"".$name."\" class=\"form-control\" rows=\"10\">{{".$param."->".$name."}}</textarea>";
                $out .= "</div>";
                $out .= "</div>";
                break;
            case 'file':
                $out .= "<div class=\"col-12\">";
                $out .= "<div class=\"form-group\">";
                $out .= "<label>".$name."</label>";
                $out .= "<input name=\"".$name."\" class=\"form-control\" type=\"".$type."\">";
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

    public static function buildTypeData($type,$name){
        $out = '';
        switch ($type) {
            case 'string':
                $out .= "\$table->".$type."('".$name."',255)->nullable();\r\n";
                break;
            case 'text':
                $out .= "\$table->".$type."('".$name."')->nullable();\r\n";
                break;
            case 'increments':
                $out .= "\$table->".$type."('".$name."');\r\n";
                break;
            case 'integer':
                $out .= "\$table->".$type."('".$name."')->default(0);\r\n";
                break;
            case 'biginteger':
                $out .= "\$table->bigInteger('".$name."')->default(0);\r\n";
                break;
            case 'timestamps':
                $out .= "\$table->".$type."('".$name."')->nullable();\r\n";
                break;
            case 'longtext':
                $out .= "\$table->longText('".$name."')->nullable();\r\n";
                break;
            case 'mediumtext':
                $out .= "\$table->mediumText('".$name."')->nullable();\r\n";
                break;
            case 'boolean':
                $out .= "\$table->".$type."('".$name."')->nullable();\r\n";
                break;
            case 'float':
                $out .= "\$table->".$type."('".$name."',10,0)->nullable();\r\n";
                break;
            case 'double':
                $out .= "\$table->".$type."('".$name."',10,0)->nullable();\r\n";
                break;
            default:
                $out .= "";
                break;
        }
        return $out;
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
}
