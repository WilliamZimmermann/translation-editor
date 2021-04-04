<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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
            foreach($request->file('filesToUpload') as $file)
            {
                $extractionResult[] = $this->extractTranslations($file);
            }
            return back()->with('message', "Some message")->with('content', json_encode($extractionResult));
        }else{
            return back()->with('message', "No files were uploaded!");
        }
    }

    private function extractTranslations($file){
        switch(strtolower($file->getClientOriginalExtension())){
            case "json":
                return "";
            case "php":
                return $this->extractPHPKeyAndTranslation($file);
            default:
                return "";
        }
    }

    /**
     * This function will extract all PHP keys and values (translations)
     * and return a combined Array
     */
    private function extractPHPKeyAndTranslation($file){
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

}
