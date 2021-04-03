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
        $extractionResult = $this->extractTranslations($uploadedFile->getContent(), $uploadedFile->getClientOriginalExtension());
        //dd($uploadedFile);

        // $fileName = time().$uploadedFile->getClientOriginalName();
        // $fileExtension = time().$uploadedFile->getClientOriginalExtension();
        // $fileContent = time().$uploadedFile->getContent();

        //die();
        return back()->with('message', $extractionResult["message"])->with('content', $extractionResult["content"]);
    }

    private function extractTranslations($content, $fileExtension){
        switch(strtolower($fileExtension)){
            case "php":
                return [
                    "content" => $content,
                    "message" => "It's a php file"
                ];
            default:
                return [
                    "content" => "",
                    "message" => "File not supported"
                ];
        }
    }
}
