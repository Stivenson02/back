<?php

namespace App\Http\Controllers\Administration;

use App\Http\Requests\Administration\ProductsCreateRequest;
use App\Models\Administration\NewProducts;
use App\Models\Administration\NewProductsImage;
use App\Models\Administration\Products;
use App\Models\Administration\ProductsImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    public function getProductAll()
    {
        $auth= Auth::User();
        $product=$auth->stakeholder->VProduct()
            ->orderBy('vproducts.quantity')
            ->get();
        return $product;
    }

    public function getProductDetail($id)
    {
        $product_detail=Products::
        select('categories.description AS name_categorie','products.id','products.title','products.description','products.short_description','products.units_supplier','products.reference',
            'products.bar_code',DB::raw("products.characteristic::text"),'products.ingredients','products.why','products.about','products.slug','products.category_id'
            ,'products.cost_sf' ,'products.image' ,DB::raw('SUM(inventory.quantity) as quantity'),DB::raw(' false as new'))
            ->leftJoin('inventory','inventory.product_id','products.id')
            ->join('categories','categories.id','products.category_id')
            ->groupBy('name_categorie','products.id','products.title','products.description','products.short_description','products.units_supplier','products.reference',
                'products.bar_code',DB::raw("products.characteristic::text"),'products.ingredients','products.why','products.about','products.slug','products.category_id',
                'products.cost_sf','products.image')
            ->find($id);

        $product_image=Products::find($id)->images;
        $product_detail['images_products'] =$product_image;
        return $product_detail;
    }

    public function  changeImageDefault(Request $request)
    {
        $produc=Products::find($request->id_product)->images()->where('main',true)->first();
        $produc->main = false;
        $produc->save();

        $image_product = ProductsImage::find($request->id_image);
        $image_product->main=true;
        $image_product->save();

        return response()->json([
            'mesagge' => 'Tarjeta actualizada'
        ], 200);
    }

    public function uploadProductDetail(Request $request)
    {
        $produc=Products::find($request->id_product);
        $validate=Products::where('id',$request->id_product)->count();
        if ($validate <= 5){
            $filename = $this->uploadImageProducts($request);
            $pr_image= new ProductsImage();
            $pr_image->product_id = $produc->id;
            $pr_image->path = "https://superfuds-file.s3.us-west-2.amazonaws.com/product/".$filename;
            $pr_image->main = false;
            $pr_image->active = true;
            $pr_image->thumbnail = "https://superfuds-file.s3.us-west-2.amazonaws.com/product/thumbnail/".$filename;
            $pr_image->save();
            return response()->json([
                'mesagge' => 'Imagen agregada'
            ], 200);
        }else{
            return response()->json([
                'mesagge' => 'No fue posible cargar la imagen'
            ], 500);
        }
    }

    public function uploadNewProductDetail(Request $request)
    {
        if ($request->isnew){
            $produc=NewProducts::find($request->id_product);
            $validate=NewProducts::where('id',$request->id_product)->count();
            if ($validate <= 5){
                $filename = $this->uploadImageNewProducts($request);
                $pr_image= new NewProductsImage();
                $pr_image->product_id = $produc->id;
                $pr_image->path = "https://superfuds-file.s3.us-west-2.amazonaws.com/product/new/".$filename;
                $pr_image->main = false;
                $pr_image->active = true;
                $pr_image->thumbnail = "https://superfuds-file.s3.us-west-2.amazonaws.com/product/new/thumbnail/".$filename;
                $pr_image->save();
                return response()->json([
                    'mesagge' => 'Imagen agregada'
                ], 200);
            }
        }
        return response()->json([
            'mesagge' => 'No fue posible cargar la imagen'
        ], 500);

    }

    /**
     * @param Request $request
     * @return string
     */
    private function uploadImageProducts(Request $request)
    {
        $data = explode(',', $request->base64);
        $file = base64_decode($data[1]);

        $request->type_document_id = 1;

        $filenameWithExtension = $request->name;
        $extention = pathinfo($filenameWithExtension, PATHINFO_EXTENSION);

        $filename = sha1(time()) . ".{$extention}";

        $manager = new ImageManager(array('driver' => 'imagick'));
        $image = $manager->make($file);
        Storage::disk("s3")->put('product/thumbnail/' . $filename, $image->resize(219,219)->encode('png')->getEncoded(), 'public');
        Storage::disk("s3")->put('product/' . $filename, $file, 'public');
        return $filename;
    }

    /**
     * @param Request $request
     * @return string
     */
    private function uploadImageNewProducts(Request $request)
    {
        $data = explode(',', $request->base64);
        $file = base64_decode($data[1]);

        $request->type_document_id = 1;

        $filenameWithExtension = $request->name;
        $extention = pathinfo($filenameWithExtension, PATHINFO_EXTENSION);

        $filename = sha1(time()) . ".{$extention}";

        $manager = new ImageManager(array('driver' => 'imagick'));
        $image = $manager->make($file);
        Storage::disk("s3")->put('product/new/thumbnail/' . $filename, $image->resize(219,219)->encode('png')->getEncoded(), 'public');
        Storage::disk("s3")->put('product/new/' . $filename, $file, 'public');
        return $filename;
    }

    public function deleteImageProducts($id_image)
    {
        $pr_image= ProductsImage::find($id_image);

        Storage::disk("s3")->delete($pr_image->thumbnail);
        Storage::disk("s3")->delete($pr_image->path);
        $pr_image->delete();

        return response()->json([
            'mesagge' => 'Imagen eliminada'
        ], 200);

    }

    public function deleteImageNewProducts($id_image)
    {
        $pr_image= NewProductsImage::find($id_image);

        Storage::disk("s3")->delete($pr_image->thumbnail);
        Storage::disk("s3")->delete($pr_image->path);
        $pr_image->delete();

        return response()->json([
            'mesagge' => 'Imagen eliminada'
        ], 200);

    }

    public function storeNewProducts(ProductsCreateRequest $request)
    {
        $auth= Auth::User();
        $request['supplier_id']=$auth->stakeholder->id;
        $result = NewProducts::create($request->all());
        return response()->json([
            'mesagge' => 'Producto creado'
        ], 200);
    }

    public function showAllNewProduct()
    {
        $auth= Auth::User();
        $product= NewProducts::where('supplier_id', $auth->stakeholder->id)->get();
        foreach ($product as $detal){
            $detal['thumbnail']='https://superfuds-file.s3.us-west-2.amazonaws.com/product/thumbnail/7ef3590e1eef9e7d8455934268b2c801522af1d3.png';
            $detal['title_ec']=$detal->title;
            $detal['reference']="Por Aprobar";
            $detal['quantity']=$detal->units_sf;

        }
        return $product;
    }

    public function getDetailNewProduct($id)
    {
        $product_detail=NewProducts::
        select('categories.description AS name_categorie','new_products.id','new_products.title','new_products.description','new_products.short_description','new_products.units_supplier','new_products.reference',
            'new_products.bar_code',DB::raw("new_products.characteristic::text"),'new_products.ingredients','new_products.why','new_products.about','new_products.slug','new_products.category_id'
            ,'new_products.cost_sf' ,'new_products.image' ,DB::raw('SUM(inventory.quantity) as quantity'),DB::raw(' true as new'))
            ->leftJoin('inventory','inventory.product_id','new_products.id')
            ->join('categories','categories.id','new_products.category_id')
            ->groupBy('name_categorie','new_products.id','new_products.title','new_products.description','new_products.short_description','new_products.units_supplier','new_products.reference',
                'new_products.bar_code',DB::raw("new_products.characteristic::text"),'new_products.ingredients','new_products.why','new_products.about','new_products.slug','new_products.category_id',
                'new_products.cost_sf','new_products.image')
            ->find($id);

        $product_image=NewProducts::find($id)->images;
        $product_detail['images_products'] =$product_image;
        return $product_detail;
    }

    public function deleteNewProducts($id_pr)
    {
        $product=NewProducts::find($id_pr);
        $pr_images=$product->images;
        foreach ($pr_images as $image){
            $this->deleteImageNewProducts($image->id);
        }
        $product->delete();

        return response()->json([
            'mesagge' => 'Producto eliminado'
        ], 200);
    }

    public function updateProduct(Request $request, $id_product)
    {
        $product=Products::find($id_product);
        $product->edit_product = json_encode( $request->all());
        $product->save();

        return response()->json([
            'mesagge' => 'Edicion enviada'
        ], 200);
    }

}
