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
        $uploadedFile = $request->file('fileToUpload');
        $extractionResult = $this->extractTranslations($uploadedFile);
        //dd($uploadedFile);

        // $fileName = time().$uploadedFile->getClientOriginalName();
        // $fileExtension = time().$uploadedFile->getClientOriginalExtension();
        // $fileContent = time().$uploadedFile->getContent();

        //die();
        return back()->with('message', $extractionResult["message"])->with('content', $extractionResult["content"]);
    }

    private function extractTranslations($file){
        switch(strtolower($file->getClientOriginalExtension())){
            case "json":
                return [
                    "content" => "nothing",
                    "message" => "It's a json file"
                ];
            case "php":
                return [
                    "content" => json_encode($this->extractPHPKeyAndTranslation($file)),
                    "message" => "It's a php file"
                ];
            default:
                return [
                    "content" => "",
                    "message" => "File not supported"
                ];
        }
    }

    /**
     * This function will extract all 
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
        return ["translationKeys" => $translationKeys, "translationContent"=> $translationContent];
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
