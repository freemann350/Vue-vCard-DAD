<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\BlockVcardRequest;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateConfirmationCodeRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateVcardLimitRequest;
use App\Http\Requests\UpdateVcardRequest;
use App\Http\Requests\VerifyCredentialsRequest;
use App\Http\Resources\VcardResource;
use App\Models\Category;
use App\Models\DefaultCategory;
use App\Models\Transaction;
use App\Models\Vcard;
use App\Services\Base64Services;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class VcardController extends Controller
{
    private function storeBase64AsFile(Vcard $vcard, String $base64String)
    {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $vcard->id . "_" . rand(1000,9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function index()
    {
        return VcardResource::collection(Vcard::all());
    }

    public function show(Vcard $vcard)
    {
        return new VcardResource($vcard);
    }

    public function store(StoreVcardRequest $request)
    {
        $formData = $request->validated();

        $formData['blocked'] = "0";
        $formData['balance'] = "0";
        $formData['max_debit'] = "5000";
        $formData["password"] = bcrypt($formData["password"]);
        $formData["confirmation_code"] = bcrypt($formData["confirmation_code"]);
        $newVcard = Vcard::create($formData);
        $newCategories = DefaultCategory::all();
        foreach ($newCategories as $defaultCategory) {
            Category::create([
                'vcard' => $newVcard->phone_number,
                'name' => $defaultCategory->name,
                'type' => $defaultCategory->type
            ]);
        }
        
        return new VcardResource($newVcard);
    }

    public function updateLimit(UpdateVcardLimitRequest $request, Vcard $vcard) 
    {
        $vcard->max_debit = $request->validated()['max_debit'];
        $vcard->save();
        return new VcardResource($vcard);
    }

    public function update(UpdateVcardRequest $request, Vcard $vcard)
    {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) && $dataToSave["deletePhotoOnServer"];
        unset($dataToSave["base64ImagePhoto"]);
        unset($dataToSave["deletePhotoOnServer"]);

        $vcard->fill($dataToSave);

        if ($vcard->photo_url && ($deletePhotoOnServer || $base64ImagePhoto)) {
            if (Storage::exists('public/fotos/' . $vcard->photo_url)) {
                Storage::delete('public/fotos/' . $vcard->photo_url);
            }
            $vcard->photo_url = null;
        }

        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $vcard->photo_url = $this->storeBase64AsFile($vcard, $base64ImagePhoto);
        }
        $vcard->save();
        return new VcardResource($vcard);
    }

    public function block(BlockVcardRequest $request, Vcard $vcard)
    {
        $vcard->blocked = $request->validated()['blocked'];
        $vcard->save();
        return new VcardResource($vcard);
    }

    public function update_password(UpdatePasswordRequest $request, Vcard $vcard)
    {

        $formData = $request->validated();
        if (!Hash::check($formData['current_password'], $vcard->password))
            abort(422);
        $vcard->password = bcrypt($formData["password"]);
        $vcard->save();
        return new VcardResource($vcard);
    }

    public function update_confirmationcode(UpdateConfirmationCodeRequest $request, Vcard $vcard)
    {
        $formData = $request->validated();
        if (!Hash::check($formData['current_password'], $vcard->password))
            abort(422);
        $vcard->confirmation_code = bcrypt($formData["confirmation_code"]);
        $vcard->save();
        return new VcardResource($vcard);
    }

    public function verifyCredentials(VerifyCredentialsRequest $request, Vcard $vcard)
    {
        $formData = $request->validated();

        if (Hash::check($formData['password'], $vcard->password) && Hash::check($formData['confirmation_code'], $vcard->password))
            return true;
        
        abort(422);
    }
    public function destroy(Vcard $vcard)
    {
        if ($vcard->balance != 0)
            return abort(422);
    
        $counter = Transaction::where('vcard','=',$vcard->phone_number)->orWhere('pair_vcard','=',$vcard->phone_number)->count();

        if ($counter == 0) {
            DB::table('categories')->where('vcard', $vcard->phone_number)->delete();
            $vcard->forceDelete();
        }  else {
            $categories[] = Transaction::select()->where('vcard', $vcard->phone_number)->whereNotNull('category_id')->distinct()->pluck('category_id')->toArray();
            $categories = Arr::flatten($categories); // PARA EVITAR "Nested arrays may not be passed to whereIn method"
            DB::table('categories')->where('vcard','=',$vcard->phone_number)->WhereNotIn('id', $categories)->delete();
            $usedCategories = Category::select()->whereIn('id', $categories)->get();
            foreach ($usedCategories as $category) {
                $category->delete();
            }
            Transaction::where('vcard', $vcard->phone_number)->delete();
            $vcard->delete();
        }

        return new VcardResource($vcard);
    }

    public function share(Vcard $vcard)
    {
        VcardResource::$format = 'minimal';
        return new VcardResource($vcard);
    }

    
    public function totalNumOfVCards(Request $request)
    {
        $totalNumOfVCards = VCard::count();
        return $totalNumOfVCards;
    }

    public function totalNumOfActiveVCards(Request $request)
    {
        $totalNumOfActiveVCards = VCard::where('blocked', 0)->count();
        return $totalNumOfActiveVCards;
    }
    

    public function totalNumOfBlockedVCards(Request $request)
    {
        $totalNumOfBlockedVCards = VCard::where('blocked', 1)->count();
        return $totalNumOfBlockedVCards;
    }

}
