<?php

namespace App\Http\Controllers\EventManagement\Room;

use App\Http\Controllers\Controller;
use App\Models\EventManagement\MasterEventRoom;
use App\Models\Organization\MasterOrganization\MasterLocation;
use App\Traits\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterRoomController extends Controller {
    use StandardResponse;

    public function index(Request $request) 
    {
        if($request->ajax()) {
            $rooms = MasterEventRoom::currentCompany()->withLocation()->get();
            return DataTables::of($rooms)
                ->addIndexColumn()
                ->make();
        }
        return view('event_management.master_room.index');
    }

    public function getData()
    {
        $locations = MasterLocation::where('id_company', session('id_company'))
                                    ->where('status', 'A')
                                    ->get(['id_location as id', 'description as text']);
        return $this->success([
            'locations' => $locations,
        ]);
    }

    public function getEdit(Request $request)
    {
        $request->validate([
            'id_event_room' => 'required',
        ]);
        $room = MasterEventRoom::findOrFail($request->id_event_room);
        return $this->success($room);
    }

    public function save(Request $request)
    {
        $acceptedRequests = [
            'id_event_room' => 'nullable',
            'id_location' => 'required',
            'description' => 'required|string',
            'status' => 'required|in:A,I',
        ];
        $request->validate($acceptedRequests);
        $data = $request->only(array_keys($acceptedRequests));
        try {
            self::checkRoomNameSimilarity($request->id_event_room, $request->description, $request->id_location);
            if($request->id_event_room) {
                $room = MasterEventRoom::findOrFail($request->id_event_room);
                $room->update(array_merge($data, [
                    'updated_by' => session('id_user'),
                ]));
            } else {
                MasterEventRoom::create(array_merge($data, [
                    'created_by' => session('id_user'),
                    'id_company' => session('id_company'),
                ]));
            }
        } catch(Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode());
        }
    }

    protected static function checkRoomNameSimilarity($eventRoomId, $roomName, $locationId): void
    {
        $room = MasterEventRoom::currentCompany()
                            ->where('id_location', $locationId)
                            ->where('description', $roomName);
        if($eventRoomId) {
            $room->where('id_event_room', '!=', $eventRoomId);
        }
        if($room->count() > 0) {
            throw new Exception("Room $roomName already exists");
        }
    }
}