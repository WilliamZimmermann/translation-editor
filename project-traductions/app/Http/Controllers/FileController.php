<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class FileController extends Controller
{
    /**
     * This method will check the uploaded file and call another
     * method to extract all translation strings
     */
    public function checkFile(Request $request){
        $originalLanguage = $request->input('originalLanguage');

        if($request->hasFile('filesToUpload')){
            $extractionResult = [];
            if(!$request->input('unify')){
                foreach($request->file('filesToUpload') as $file)
                {
                    // We want that each node of translations has his own name/key. This key will be the original file name
                    $extractionResult[] = [explode('.', $file->getClientOriginalName())[0] => $this->extractTranslations($file, $request->input('fileType'))];
                }
            }else{
                $extractionResult = $this->unifyArraysKeys($request->file('filesToUpload'), $request->input('fileType'));
                dd($extractionResult);
            }
            
            return back()->with('message', "Some message")->with('content', json_encode($extractionResult));
        }else{
            return back()->with('message', "No files were uploaded!");
        }
    }

    /**
     * This function is used to extract the translation for different type of files
     */
    private function extractTranslations(UploadedFile $file, $fileType){
        switch($fileType){
            case "json":
                return "";
            case "laravel7":
                return $this->extractPHPLaravel7KeyAndTranslation($file);
            default:
                return "";
        }
    }

    /**
     * This function will extract all PHP keys and values (translations) for Laravel 7 Standard
     * and return a combined Array
     * @param UploadedFile $file
     * @return Array array with node of keys and translations
     */
    private function extractPHPLaravel7KeyAndTranslation(UploadedFile $file){
        $translationKeys = [];
        $translationContent = [];
        foreach(file($file) as $line) {
            $hasComma = strpos($line, "'");
            $hasPointer = strpos($line, "' => '");
            if($hasComma && $hasPointer){
                $translationKeys[] = $this->get_string_between($line, "'", "' => '");
                $translationContent[] = $this->get_string_between($line, "' => '", "'");
            }
        }
        // Combine keys and values
        return array_combine($translationKeys, $translationContent);
    }

    /**
     * I got this function from StackOverflow
     * Source: https://stackoverflow.com/questions/5696412/how-to-get-a-substring-between-two-strings-in-php
     */
    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * This function will unify arrays keys when it is possible
     * When keys are equal but values are different, it will create two different keys based on file name
     */
    private function unifyArraysKeys($files, $fileType){
        foreach($files as $file)
        {
            $extractionResult[] = $this->extractTranslations($file, $fileType);
        }

        $newArray = [];
        for($l=0; $l < count($extractionResult); $l++){
            $x = 0;
            foreach($extractionResult[$l] as $key => $value){
                if($extractionResult[$x] && $l != $x){
                    foreach($extractionResult[$x] as $key2 => $value2){
                        if($key == $key2){
                            dd($key, $key2, $value, $value2);
                        }
                    }
                }
                $x++;
            }
        }
            


        $unique_array = call_user_func_array('array_merge', $extractionResult);

        // That will flatten the array by exactly one level
        //$unique_array = call_user_func_array('array_merge', array_unique($extractionResult, SORT_REGULAR));
        
        // foreach($extractionResult as $translationKey){
        //      array_search(())
        // }
        
        
        ksort($unique_array);
        
        return $unique_array;
    }

}
