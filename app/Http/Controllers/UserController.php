<?php

namespace App\Http\Controllers;

use App\Administrator;
use App\Models\Administration\StakeholderDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth= Auth::User();
        $stakeolder=$auth->stakeholder()->first();
        $auth['business_name']=$stakeolder->business_name;
        $auth['business']=$stakeolder->business;
        $auth['phone_contact']=$stakeolder->phone_contact;
        $auth['send_city_id']=$stakeolder->send_city_id;
        $auth['send_city_name']=$stakeolder->sendCity()->first()->description;
        $auth['send_departament_id']=$stakeolder->sendCity()->first()->department_id;
        $auth['send_departament_name']=$stakeolder->sendCity()->first()->department()->first()->description;
        $auth['address_send']=$stakeolder->address_send;
        $auth['invoice_city_id']=$stakeolder->invoiceCity()->first()->id;
        $auth['address_invoice']=$stakeolder->address_invoice;
        $auth['invoise_city_name']=$stakeolder->invoiceCity()->first()->description;
        $auth['invoise_departament_id']=$stakeolder->invoiceCity()->first()->department_id;
        $auth['invoise_departament_name']=$stakeolder->invoiceCity()->first()->department()->first()->description;
        return $auth;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAvatar()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function documentImages(Request $request)
    {
        $filenameWithExtension = $request->file("file")->getClientOriginalName();
        $extention = pathinfo($filenameWithExtension, PATHINFO_EXTENSION);
        if ($extention == 'png' ||$extention == 'PNG' ||$extention == 'jpg' || $extention == 'JPG' ||$extention == 'jpeg' ||$extention == 'JPEG' ){
            $file = $request->file('file');
            $filename = sha1(time()).".{$extention}";

            $manager = new ImageManager(array('driver' => 'imagick'));
            $image = $manager->make($file)->resize(300, 200);

            Storage::disk('s3')->put('supplier/profile/'.$request->location.'/'.$filename, $file);
            Storage::disk('s3')->put('supplier/profile/'.$request->location.'/300X200_'.$filename, $image);

            $auth= Auth::User();
            $stackeolder_document_validate=StakeholderDocument::where('stakeholder_id',$auth->stakeholder_id)
                ->where('document_id',(int)$request->type_document_id);

            if ($stackeolder_document_validate->exists()){
                $stackeolder_document= $stackeolder_document_validate->first();
                Storage::disk('s3')->delete($stackeolder_document->path);
                Storage::disk('s3')->delete('supplier/profile/'.$request->location.'/'.$stackeolder_document->name);
            }else{
                $stackeolder_document= new StakeholderDocument();
            }
            $stackeolder_document->stakeholder_id=$auth->stakeholder_id;
            $stackeolder_document->document_id=(int)$request->type_document_id;
            $stackeolder_document->path='supplier/profile/'.$request->location.'/300X200_'.$filename;
            $stackeolder_document->name=$filename;
            $stackeolder_document->status_document=1;
            $stackeolder_document->save();

            return response()->json([
                'mesagge'=>'Imagen Almacenada',
                'code'=>200
            ]);
        }else{
            return response()->json([
                'mesagge'=>'La imagen no tiene un formato valido',
                'code'=>500
            ]);
        }

    }

    public function documentFiles(Request $request)
    {
        $filenameWithExtension = $request->file("file")->getClientOriginalName();
        $extention = pathinfo($filenameWithExtension, PATHINFO_EXTENSION);
        if ($extention == 'doc' || $extention == 'DOC' || $extention == 'docx' || $extention == 'DOCX' || $extention == 'pdf' || $extention == 'PDF' || $extention == 'png' || $extention == 'PNG' || $extention == 'jpg' || $extention == 'JPG' || $extention == 'jpeg' || $extention == 'JPEG' ){
            $file = $request->file('file');
            $filename = sha1(time()).".{$extention}";

            Storage::disk('s3')->put('supplier/documents/'.$request->location.'/'.$filename, $file);

            $auth= Auth::User();
            $stackeolder_document_validate=StakeholderDocument::where('stakeholder_id',$auth->stakeholder_id)
                ->where('document_id',(int)$request->type_document_id);

            if ($stackeolder_document_validate->exists()){
                $stackeolder_document= $stackeolder_document_validate->first();
                Storage::disk('s3')->delete($stackeolder_document->path);
            }else{
                $stackeolder_document= new StakeholderDocument();
            }
            $stackeolder_document->stakeholder_id=$auth->stakeholder_id;
            $stackeolder_document->document_id=(int)$request->type_document_id;
            $stackeolder_document->path='supplier/documents/'.$request->location.'/'.$filename;
            $stackeolder_document->name=$filename;
            $stackeolder_document->status_document=0;
            $stackeolder_document->save();

            return response()->json([
                'mesagge'=>'Documento Almacenado',
                'code'=>200
            ]);
        }else{
            return response()->json([
                'mesagge'=>'El documento no tiene un formato valido',
                'code'=>500
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function show(Administrator $administrator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function edit(Administrator $administrator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Administrator $administrator)
    {
        //
    }
}
