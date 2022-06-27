<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use ReflectionClass;

Trait RepositoryTrait{

    protected function updatePasswords($input, $type='create', $model=null){
         $newPassword = '';
         if($this->checkConstant('_PASSWORDS')){           
            if($type == 'create'){
                $newPassword = bcrypt($input['password']);
            }
            if($type == 'update'){
                if( isset($input['password']) && strlen($input['password']) ){
                    $newPassword = bcrypt($input['password']);
                }else{
                    $newPassword = $model->password;
                }
            }
                
        }
        $input['password'] = $newPassword;

        return $input;
    }

    protected function setFiles($input, $model=null){

        if($this->checkConstant('_FILES')){
           
            $files = request()->file();
            foreach($files as $fileKey => $fileData){
                $file = request()->file($fileKey);
                $filename = $file->hashName(); // Generate a unique, random name...
                if($model){
                    $oldFile = $model->avatar_path . $model->$fileKey;
                    File::delete($oldFile);
                }
                $file->move($this->model->avatar_path , $filename);
                $input[$fileKey] = $filename;                
            }
        }
        return $input;
    }

    protected function deleteFiles( $model){
        if($this->checkConstant('_FILES')){
           
            foreach($model::$modelFiles as $fileKey => $path){
                $oldFile = $path . $model->$fileKey;
                // dd( $oldFile);
                File::delete($oldFile);            
            }
        }
    }

    protected function checkConstant($constant){
        $class = new ReflectionClass($this->model);
        $allConstants = $class->getConstants();

        return array_key_exists($constant, $allConstants) ? $allConstants[$constant] : false;

    }
}

