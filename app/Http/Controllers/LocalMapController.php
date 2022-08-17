<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocalMapsPlacesIdResource;
use Illuminate\Http\Request;
use App\Models\LocalMap;
use App\Models\LocalMapsPlacesId;
use Illuminate\Support\Facades\Http;

class LocalMapController extends Controller
{

    //save map searches from user app
    public function save(Request $request)
    {
        try {
            LocalMap::create(
                [
                    'user_id' => $request->user_id,
                    'address' => $request->description,
                    'region' => $request->region,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'place_id' => isset($request->place_id) ? $request->place_id : NULL
                ]
            );
            return response()->json([
                'error' => false,
                'msg' => 'success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'msg' => 'Error',
                'data' => $th
            ]);
        }
    }
    public function savePlacesId(Request $request)
    {
        try {
            $places = [];
            foreach ($request->places as $key => $value) {
                $places[] = [
                    'user_id' => isset($request->user_id) ? $request->user_id : NULL,
                    'place_id' => $value['place_id'],
                    'formatted_address' => $value['description'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')

                ];
            }
            LocalMapsPlacesId::insert(
                $places
            );
            return response()->json([
                'error' => false,
                'msg' => 'success',
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'error' => true,
                'msg' => 'Error',
                'data' => $th
            ]);
        }
    }

    //search address from local maps
    public function search(Request $request)
    {
        try {
            //search for addresses with place id
            $searchResult = LocalMapsPlacesId::whereRaw("formatted_address LIKE '$request->search_query%'")->paginate(10);
            $searchResultMap = LocalMap::whereRaw("address LIKE '%$request->search_query%'")->paginate(10);
            //if addresses in local map inn empty
            if (count($searchResultMap) == 0) {
                //get from openstreetmap and save
                $data =  $this->openStreet($request->search_query);
                //return $data;
                if (count($data) > 0) {
                    foreach ($data as $key => $value) {
                        $places[] = [
                            'user_id' => isset($request->user_id) ? $request->user_id : NULL,
                            'address' => $value['display_name'],
                            'region' => isset($value['region']) ? $value['region'] : NULL,
                            'lat' =>  $value['lat'],
                            'lng' =>  $value['lon'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    LocalMap::insert($places);
                    //Re - search for addresses in local map
                    $searchResultMap = LocalMap::whereRaw("address LIKE '%$request->search_query%'")->paginate(10);
                }
            }
            $searchResultResource  = LocalMapsPlacesIdResource::collection($searchResult);
            $searchMapResultResource  = LocalMapsPlacesIdResource::collection($searchResultMap);

            //merge collections
            $mergedResults = $searchResultResource->merge($searchMapResultResource);
            return response()->json([
                'error' => false,
                'msg' => 'success',
                'data' => $mergedResults
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'msg' => 'Error',
                'data' => $th
            ]);
        }
    }

    //get user map search history
    public function history(Request $request)
    {
        try {
            $historyResult = LocalMapsPlacesId::whereRaw("formatted_address LIKE '%$request->search_query%'")->orderBy('id', 'DESC')->where('user_id', $request->user_id)->get();
            return response()->json([
                'error' => false,
                'msg' => 'success',
                'data' => LocalMapsPlacesIdResource::collection($historyResult)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'msg' => 'Error',
                'data' => $th
            ]);
        }
    }

    public function openStreet($search)

    {
        $url = 'https://nominatim.openstreetmap.org/search.php?q=' . $search . '&format=jsonv2&accept-language=en&countrycodes=gh&limit=10';
        $response = Http::get($url);
        return $response->json();
    }
}
