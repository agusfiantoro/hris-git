<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\API\Employee;
use App\Models\API\Attendance;
use App\Models\Curl;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class AppController extends BaseController
{
    public function getVersion(Request $request)
    {
        /*
        * Trello Issue 149.004 API Get Version
        */
        try {
            $this->authMobile($request);
        } catch (Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }
        $request->validate([
            // 'id_company' => 'required'
        ]);
        $latestVersion = DB::table('app_version_management as avm')
                            ->select(
                                'avm.version_number',
                                'avm.id_user',
                                'avm.status',
                                'avm.id_company',
                                'av.description as change_log'
                            )
                            ->leftJoin('app_versions as av', 'av.version_number', '=', 'avm.version_number')
                            // ->where('id_company', $request->id_company)
                            ->where('avm.status', 'A')
                            ->latest('avm.version_number')
                            ->first();
                            // dd($latestVersion);
        return $this->sendSuccess("Data was successfully sent", $latestVersion, false);
    }
}