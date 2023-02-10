<?php

namespace App\Http\Controllers\API\KYC;

use App\Http\Controllers\Controller;
use App\Http\Requests\KYCRequest;
use App\Models\Address;
use App\Models\KYC;
use App\Shared\UploadBase64Image;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class KYCController extends Controller
{
    use ApiResponder;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(KYCRequest $request)
    {
        //
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                $validated['id_or_passport_front_file_path']=UploadBase64Image::upload_image($validated['id_or_passport_front_file_path'],'kyc');
                $validated['id_or_passport_back_file_path']=UploadBase64Image::upload_image($validated['id_or_passport_back_file_path'],'kyc');
                $validated['selfie_file_path']=UploadBase64Image::upload_image($validated['selfie_file_path'],'kyc');
                //
                $user=KYC::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully uploaded your KYC details '.config('app.name.'),
                    $user,
                    Response::HTTP_CREATED);
            });

        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
