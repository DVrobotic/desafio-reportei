<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    public $table = "contents";
    protected $primaryKey = "id";

    protected $fillable = ['file_path', 'box_id'];

    public function box(){
        return $this->belongsTo(Box::class, 'box_id');
    }

    public static function saveFile($data, $key, $name, $diretorio, $oldFile = '') {
        
        if(isset($data[$name][$key]) && is_file($data[$name][$key])){
            $fileName = $data[$name][$key]->getClientOriginalName();
            $fileName = hash('sha256', $fileName . strval(time())) . '.' . $data[$name][$key]->getClientOriginalExtension();
            static::deleteFile($oldFile, $diretorio);
            $data[$name][$key]->storeAs($diretorio, $fileName);
            $data[$name][$key] = "storage/boxes/files/" . $fileName;
        }else{
            unset($data[$name][$key]);
        }

        return $data;
    }
    
    public static function deleteFile($fileName, $diretorio) {
        if($fileName != '' && file_exists(storage_path(str_replace('storage', 'app/public', $fileName))) ){
            unlink(storage_path(str_replace('storage', 'app/public', $fileName)));
        }
    }

    public function getTypeAttribute(){
        try{
            if(file_exists($this->file_path)){
                return mime_content_type($this->file_path);
            }
        } catch(Exception $ex){
            return "";
        }
    }
}
