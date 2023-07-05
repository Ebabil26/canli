<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Jobs\SendPushNotification;
use App\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use App\Http\Requests;

class AnnouncementsController extends Controller
{
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Displays datatables front end view
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        return view('announcements.index');
    }

    public function index()
    {
        $announcements = Announcement::where('status', Announcement::ANNOUNCEMENT_STATUS_APPROVED)->orderBy('created_at', 'desc')->get();
        return response()->json($announcements);
    }

    public function savePhotos()
    {
        $files = array('photos' => Input::file('photos'));

        $filesData = [];
        foreach ($files['photos'] as $index => $file) {

            $uploadDir = '/uploads/announcement_photos/';

            $fileName = str_random(). '.' . $file->getClientOriginalExtension();
            $fileOriginalName = $file->getClientOriginalName();
            $fileMimeType = $file->getClientMimeType();
            $fileSize = $file->getClientSize();
            $file->move(public_path() . $uploadDir, $fileName);

            $fileInfo = [
                'file_name' => $fileName,
                'file_path' => $uploadDir,
                'original_name' => $fileOriginalName,
                'mime_type' => $fileMimeType,
                'file_size' => $fileSize
            ];

            $filesData[] = $fileInfo;
        }

        return response()->json([
            'error' => 0,
            'result' => ['photos' => $filesData]
        ]);

    }

    public function show($id)
    {
        $announcement = Announcement::find($id);

        return response()->json($announcement);
    }

    public function add(Request $request)
    {

        $announcement = (int)$request->id > 0 ? Announcement::find((int)$request->id) : new Announcement();

        $announcement->body = $request->body;
        $announcement->header = $request->header;
        $announcement->image = $request->image;
        $announcement->status = $request->status;

        $announcement->save();


        // sending push notification

        if($announcement->status == Announcement::ANNOUNCEMENT_STATUS_APPROVED)
        {
            foreach (Token::all() as $device) {

                $device->message = $announcement->header; // плохое решение сохранять сообщение в таблицу токенов, перепилить!
                $device->save();

                dispatch(new SendPushNotification($device));

            }
        }


        return response()->json($announcement);
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData()
    {
        return Datatables::of(Announcement::query())
            ->editColumn('status', '{{App\Announcement::$statuses[$status]}}')
            ->addColumn('action', function ($announcement) {
                return '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#announcementModal" data-announcement-id="' . $announcement->id . '"><i class="glyphicon glyphicon-edit"></i></button>';
            })
            ->make(true);
    }
}
