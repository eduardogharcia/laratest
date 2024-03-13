<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function list(Request $request) {
        $list = $this->loadListFromDisk();

        if (is_null($list)) {
            return response()->json([]);
        } else {
            return response()->json($list);
        }
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "quantity" => "required",
            "price" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $list = $this->loadListFromDisk();

        $newProduct = $validator->validated();
        $newProduct['id'] = uniqid();
        $newProduct['total'] = $newProduct['quantity'] * $newProduct['price'];
        $newProduct['datetime'] = time();

        if (is_null($list)) {
            $this->saveListAtDisk([$newProduct]);
        } else {
            array_push($list, $newProduct);
            $this->saveListAtDisk($list);
        }

        return response()->noContent(Response::HTTP_CREATED);

    }

    private function loadListFromDisk() {
        $list = json_decode(Storage::disk('local')->get('list.json'));
        return $list;
    }

    private function saveListAtDisk($list) {
        Storage::disk('local')->put('list.json', json_encode($list));
    }
}
