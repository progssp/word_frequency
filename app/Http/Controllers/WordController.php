<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WordController extends Controller
{
    public function word_freq(Request $request){
        $validation = Validator::make($request->all(),[
            'top' => 'required|int',
            'exclude' => 'required'
        ]);
        if($validation->fails()){
            return response()->json(['status'=>false,'msg'=>$validation->errors()]);    
        }

        ini_set('memory_limit','-1');
        ini_set('file_uploads','On');
        ini_set('upload_max_filesize','200M');
        ini_set('post_max_size','200M');
        

        if($request->hasFile('txt_file')){
            $top = $request->input('top');
            $exclude = $request->input('exclude');
            $exclude = json_decode($exclude,true);
            $path = $request->file('txt_file')->store('test_dir');
            
            $path = 'storage/'.$path;
            

            $file = file_get_contents($path);
            $file = strtolower($file);
            
            $words = str_word_count($file, 1);
            
            $diff = array_diff($words,$exclude);
            $wordCounts = array_count_values($diff);
            arsort($wordCounts);


            $values = array_values($wordCounts);
            
            $counter = -1;
            for($i=0;$i<count($values);$i++){
                if(intval($values[$i]) <= intval($top)){
                    break;
                }
                $counter++;
            }
            
            if($counter == -1){
                return [];
            }

            $i=0;
            

            $final_arr['data'] = [];
            $inner_obj = new \stdClass;
            foreach ($wordCounts as $word => $count) {
                $i++;
                if($i > $counter){
                    break;
                }
                $inner_obj->word = $word;
                $inner_obj->count = $count;

                $final_arr['data'][] = $inner_obj;
                $inner_obj = new \stdClass;
            }

            return $final_arr;
            
        }
        else if($request->input('text') != null){
            $text = $request->input('text');
            $text = strtolower($text);
            $top = $request->input('top');
            $exclude = $request->input('exclude');
            $exclude = json_decode($exclude,true);

            $words = str_word_count($text, 1);
            
            $diff = array_diff($words,$exclude);
            $wordCounts = array_count_values($diff);
            arsort($wordCounts);

            $values = array_values($wordCounts);
            
            $counter = -1;
            for($i=0;$i<count($values);$i++){
                if(intval($values[$i]) <= intval($top)){
                    break;
                }
                $counter++;
            }

            if($counter == -1){
                return [];
            }

            $i=0;
            

            $final_arr['data'] = [];
            $inner_obj = new \stdClass;
            foreach ($wordCounts as $word => $count) {
                $i++;
                if($i > $counter){
                    break;
                }
                $inner_obj->word = $word;
                $inner_obj->count = $count;

                $final_arr['data'][] = $inner_obj;
                $inner_obj = new \stdClass;
            }

            return $final_arr;
        }
        else{
            return response()->json(['status'=>false,'msg'=>'either upload text file or enter some text']);
        }
    }
}
