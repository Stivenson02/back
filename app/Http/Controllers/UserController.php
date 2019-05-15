<?php

namespace App\Http\Controllers;

use App\Administrator;
use App\Models\Administration\Parameters;
use App\Models\Administration\StakeholderDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class UserController extends Controller
{

    public function index()
    {
        $auth= Auth::User();
        $stakeolder=$auth->stakeholder;

        $data= array_search(3,json_decode($stakeolder->type_stakeholder_id)) ;
        if (!$data){
            return response()->json([
                "message"=> "El usuario no es de tipo proveedor"
            ], 500);
        }
        $validate_documents= $stakeolder->documents()->where('document_id', 1);
        if($validate_documents->exists()){
            $documents = $validate_documents->first();
            $auth['path']=$documents->path.'300X200_'.$documents->name;
        }else{
            $auth['path']=null;
        }
        $auth['business_name']=$stakeolder->business_name;
        $auth['business']=$stakeolder->business;
        $auth['phone_contact']=$stakeolder->phone_contact;
        $auth['send_city_id']=$stakeolder->send_city_id;
        $auth['send_city_name']=$stakeolder->sendCity->description;
        $auth['send_departament_id']=$stakeolder->sendCity->department_id;
        $auth['send_departament_name']=$stakeolder->sendCity->department->description;
        $auth['address_send']=$stakeolder->address_send;
        $auth['invoice_city_id']=$stakeolder->invoiceCity->id;
        $auth['address_invoice']=$stakeolder->address_invoice;
        $auth['invoise_city_name']=$stakeolder->invoiceCity->description;
        $auth['invoise_departament_id']=$stakeolder->invoiceCity->department_id;
        $auth['invoise_departament_name']=$stakeolder->invoiceCity->department->description;
        return $auth;
    }

    public function update(Request $request, Administrator $administrator)
    {
        $admin =$administrator->find($request->id);
        $admin->name = $request->name;
        $admin->last_name = $request->last_name;
        $admin->phone = $request->phone;
        $stakeholder= $admin->stakeholder()->first();
        $stakeholder->phone_contact = $request->phone_contact;
        $stakeholder->send_city_id = $request->send_city_id;
        $stakeholder->address_send = $request->address_send;
        $stakeholder->invoice_city_id = $request->invoice_city_id;
        $stakeholder->address_invoice = $request->address_invoice;
        $admin->save();
        $stakeholder->save();

        return [
            "message"=>"Informacion actualizada",
            "code"=>200
        ];
    }

    /**
     * @upload Files are send to s3
     * @update Data update in database
     */

    public function uploadAvatar(Request $request)
    {
        $data = explode(',', $request->base64);
        $content = base64_decode($data[1]);

        $location = 'avatar';
        $request->type_document_id=1;
        $type_image=true;

        return $this->loadFileData($request, $content, $location, $type_image);
    }

    public function uploadLogo(Request $request)
    {
        $data = explode(',', $request->base64);
        $content = base64_decode($data[1]);

        $location = 'logo';
        $request->type_document_id=2;
        $type_image=true;

        return $this->loadFileData($request, $content, $location, $type_image);
    }

    public function uploadIdentity(Request $request)
    {
        $data = explode(',', $request->base64);
        $content = base64_decode($data[1]);

        $location = 'identity';
        $request->type_document_id=4;
        $type_image=false;

        return $this->loadFileData($request, $content, $location, $type_image);
    }

    public function uploadNit(Request $request)
    {
        $data = explode(',', $request->base64);
        $content = base64_decode($data[1]);

        $location = 'nit';
        $request->type_document_id=5;
        $type_image=false;

        return $this->loadFileData($request, $content, $location, $type_image);
    }

    public function uploadRut(Request $request)
    {
        $data = explode(',', $request->base64);
        $content = base64_decode($data[1]);

        $location = 'rut';
        $request->type_document_id=6;
        $type_image=false;

        return $this->loadFileData($request, $content, $location, $type_image);
    }

    public function uploadChamberCommerce(Request $request)
    {
        $data = explode(',', $request->base64);
        $content = base64_decode($data[1]);

        $location = 'ccommerce';
        $request->type_document_id=7;
        $type_image=false;

        return $this->loadFileData($request, $content, $location, $type_image);
    }

    /**
     * @getFile this method gets files from s3
     */

    public function getAll()
    {
        $info=array();
        $auth= Auth::User();

        $stackeolder_document = Parameters::select(
            'parameters.code AS document_id','parameters.description',
            DB::raw('coalesce(stakeholder_document.name,\'NOIMAGEN.png\') AS name'),
            DB::raw('coalesce(stakeholder_document.path,\'https://superfuds-file.s3.us-west-2.amazonaws.com/supplier/profile/default/\') AS path'))
            ->leftJoin('stakeholder_document',function($q) use ($auth){
                $q->on('stakeholder_document.document_id','=', 'parameters.code')
                    ->where('stakeholder_document.stakeholder_id', $auth->stakeholder_id);
            })
            ->where('parameters.group','typedocument')
            ->orderBy('code')->get();

        foreach ($stackeolder_document as $document){
            $data = explode('.', $document->name);
            $show_document = $this->showDocument($data[0],$data[1]);
            array_push($info,array(
                'path'=>$document->path,
                'name'=>$document->name,
                'extension'=> $data[1],
                'document_id'=> $document->document_id,
                'show_document'=>$show_document,
            ));
        }
        return response()->json($info, 200);
    }

    public function getAvatar()
    {
        $auth= Auth::User();
        $stackeolder_document_validate= StakeholderDocument::where('stakeholder_id',$auth->stakeholder_id)
            ->where('document_id',1);
        if ($stackeolder_document_validate->exists()){
            $stackeolder_document= $stackeolder_document_validate->first();
            $data = explode('.', $stackeolder_document->name);
            $show_document = $this->showDocument($data[0],$data[1]);
            return response()->json([
                'path'=>$stackeolder_document->path,
                'name'=>$stackeolder_document->name,
                'extension'=> $data[1],
                'document_id'=> $stackeolder_document->document_id,
                'show_document'=>$show_document,
            ], 200);
        }else{
            return response()->json([
                'mesagge' => 'No existe registro del archivo'
            ], 500);
        }
    }

    public function deleteDocument($document_id)
    {
        $auth= Auth::User();
        $stackeolder_document_validate= StakeholderDocument::where('stakeholder_id',$auth->stakeholder_id)
            ->where('document_id',(int)$document_id);
        if ($stackeolder_document_validate->exists()){
            $stackeolder_document= $stackeolder_document_validate->first();
            Storage::disk('s3')->delete($stackeolder_document->path.$stackeolder_document->name);
            Storage::disk('s3')->delete($stackeolder_document->path.'300X200'.$stackeolder_document->name);
            $stackeolder_document_validate->delete();
            return response()->json([
                'mesagge' => 'Archivo eliminado'
            ], 200);
        }else{
            return response()->json([
                'mesagge' => 'Porfavor Cargue un archivo'
            ], 500);
        }

    }

    private function showDocument($name,$extention){
        if ($name == 'NOIMAGEN'){
            $respons = 'https://superfuds-file.s3.us-west-2.amazonaws.com/supplier/profile/default/NOIMAGEN.png';
        }else{
            if ($extention == 'png' || $extention == 'PNG' || $extention == 'jpg' || $extention == 'JPG' || $extention == 'jpeg' || $extention == 'JPEG') {
                $respons = 'https://superfuds-file.s3.us-west-2.amazonaws.com/supplier/profile/default/300X200_IMAGENUPLOAD.png';
            } elseif ($extention == 'pdf' || $extention == 'PNG'){
                $respons = 'https://superfuds-file.s3.us-west-2.amazonaws.com/supplier/profile/default/300X200_PDFUPLOAD.png';
            } elseif ($extention == 'doc' || $extention == 'DOC' || $extention == 'docx' || $extention == 'DOCX'){
                $respons = 'https://superfuds-file.s3.us-west-2.amazonaws.com/supplier/profile/default/300X200_DOCUPLOAD.png';
            }
        }
        return $respons;
    }

    private function saveImages($file, $location, $filename)
    {
        $manager = new ImageManager(array('driver' => 'imagick'));
        $image = $manager->make($file);
        Storage::disk("s3")->put('supplier/profile/'.$location.'/300X200_'.$filename,$image->resize(500, 500)->encode('png')->getEncoded(),'public');
    }

    private function saveFiles($file, $location, $filename, $request)
    {

        Storage::disk('s3')->put('supplier/profile/'.$location.'/'.$filename, $file);

        $auth= Auth::User();
        $stackeolder_document_validate=StakeholderDocument::where('stakeholder_id',$auth->stakeholder_id)
            ->where('document_id',(int)$request->type_document_id);
        $path= 'https://superfuds-file.s3.us-west-2.amazonaws.com/supplier/profile/'.$location.'/';
        if ($stackeolder_document_validate->exists()){
            $stackeolder_document= $stackeolder_document_validate->first();
            Storage::disk('s3')->delete($stackeolder_document->path.$stackeolder_document->name);
            Storage::disk('s3')->delete($stackeolder_document->path.'300X200'.$stackeolder_document->name);
        }else{
            $stackeolder_document= new StakeholderDocument();
        }
        $stackeolder_document->stakeholder_id=$auth->stakeholder_id;
        $stackeolder_document->document_id=(int)$request->type_document_id;
        $stackeolder_document->path= $path;
        $stackeolder_document->name=$filename;
        $stackeolder_document->status_document=1;
        $stackeolder_document->save();
    }

    private function validateSaveDocument(Request $request, $extention)
    {
        if ((int)$request->type_document_id == 1 || (int)$request->type_document_id == 2) {
            if ($extention == 'png' || $extention == 'PNG' || $extention == 'jpg' || $extention == 'JPG' || $extention == 'jpeg' || $extention == 'JPEG') {
                $respons = 1;
            } else {
                $respons = 0;
            }
        } else {
            if ($extention == 'doc' || $extention == 'DOC' || $extention == 'docx' || $extention == 'DOCX' || $extention == 'pdf' || $extention == 'PDF' || $extention == 'png' || $extention == 'PNG' || $extention == 'jpg' || $extention == 'JPG' || $extention == 'jpeg' || $extention == 'JPEG') {
                $respons = 1;
            } else {
                $respons = 0;
            }
        }
        return $respons;
    }

    /**
     * @param Request $request
     * @param $content
     * @param $location
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadFileData(Request $request, $content, $location, $type_image)
    {
        $filenameWithExtension = $request->name;
        $extention = pathinfo($filenameWithExtension, PATHINFO_EXTENSION);

        $respons = $this->validateSaveDocument($request, $extention);
        if ($respons == 1) {
            $filename = sha1(time()) . ".{$extention}";
            if ($type_image){
                $this->saveImages($content, $location, $filename);
            }
            $this->saveFiles($content, $location, $filename, $request);
            return response()->json([
                'mesagge' => $location.' actualizado',
                'code' => 200
            ], 200);
        } else {
            return response()->json([
                'mesagge' => 'El Archivo no tiene un formato valido',
                'code' => 500
            ],500);
        }
    }
}
