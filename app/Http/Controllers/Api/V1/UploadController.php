<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\Country;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\FileUploadRequest;

class UploadController extends Controller
{
    /**
     * Upload and store one or more files to server storage
     *
     * @param  FileUploadRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileUploadRequest $request)
    {
      $timestamp = time();
      $filenames = [];
      foreach($request->all() as $key => $value){
        if($request->hasfile($key)){
          $filename = ($timestamp++).'-'.$request[$key]->getClientOriginalName();
          $request[$key]->move(public_path().'/storage/uploads/', $filename);
          $filenames[$key] = $filename;
        }
      }
      return response()->json(['filenames'=> $filenames],200);
    }

    /**
     * Remove the specified file from server storage.
     *
     * @param  \Illuminate\Http\Resource  $request
     * @return response message
     */
    public function destroy(Request $request)
    {
      // server side deletes, todo
      return response(['message'=>'File(s) deleted - todo']);
    }
}
