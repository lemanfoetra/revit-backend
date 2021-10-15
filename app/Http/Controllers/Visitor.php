<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seeker\VisitorRequest;
use App\Models\Visitors;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class Visitor extends Controller
{

    public function days()
    {
        $user_id = JWTAuth::user()->id;
        $day0   = $this->reduceDay(0);
        $day1   = $this->reduceDay(1);
        $day2   = $this->reduceDay(2);
        $day3   = $this->reduceDay(3);
        $day4   = $this->reduceDay(4);
        $day5   = $this->reduceDay(5);
        $day6   = $this->reduceDay(6);

        $name0 = date('d/m/Y', strtotime($day0));
        $name1 = date('d/m/Y', strtotime($day1));
        $name2 = date('d/m/Y', strtotime($day2));
        $name3 = date('d/m/Y', strtotime($day3));
        $name4 = date('d/m/Y', strtotime($day4));
        $name5 = date('d/m/Y', strtotime($day5));
        $name6 = date('d/m/Y', strtotime($day6));

        $result = DB::table('visitors')
            ->select(DB::raw("
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day0 00:00:00' AND '$day0 23:59:59' ) AS '$name0',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day1 00:00:00' AND '$day1 23:59:59' ) AS '$name1',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day2 00:00:00' AND '$day2 23:59:59' ) AS '$name2',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day3 00:00:00' AND '$day3 23:59:59' ) AS '$name3',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day4 00:00:00' AND '$day4 23:59:59' ) AS '$name4',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day5 00:00:00' AND '$day5 23:59:59' ) AS '$name5',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day6 00:00:00' AND '$day6 23:59:59' ) AS '$name6' 
            "))
            ->where('user_id', $user_id)
            ->first();

        return response()->json(
            [
                'success' => count((array)$result) > 0 ? true : false,
                'message' => count((array)$result) > 0 ? 'Berhasil' : 'Gagal',
                'data'    => (array)$result,
            ],
            count((array)$result) > 0 ? 200 : 401
        );
    }


    public function weeks()
    {
        $user_id = JWTAuth::user()->id;
        $listDay = (array)$this->listDayOfWeek();

        $name0 = date('d/M', strtotime($listDay[0]['firstday'])) . " - " . date('d/M', strtotime($listDay[0]['lastday']));
        $name1 = date('d/M', strtotime($listDay[1]['firstday'])) . " - " . date('d/M', strtotime($listDay[1]['lastday']));
        $name2 = date('d/M', strtotime($listDay[2]['firstday'])) . " - " . date('d/M', strtotime($listDay[2]['lastday']));
        $name3 = date('d/M', strtotime($listDay[3]['firstday'])) . " - " . date('d/M', strtotime($listDay[3]['lastday']));

        $result = DB::table('visitors')
            ->select(DB::raw("
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[0]['firstday'] . " 00:00:00' AND '" . $listDay[0]['lastday'] . " 23:59:59' ) AS '" . $name0 . "',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[1]['firstday'] . " 00:00:00' AND '" . $listDay[1]['lastday'] . " 23:59:59' ) AS '" . $name1 . "',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[2]['firstday'] . " 00:00:00' AND '" . $listDay[2]['lastday'] . " 23:59:59' ) AS '" . $name2 . "',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[3]['firstday'] . " 00:00:00' AND '" . $listDay[3]['lastday'] . " 23:59:59' ) AS '" . $name3 . "'
            "))
            ->where('user_id', $user_id)
            ->first();

        return response()->json(
            [
                'success' => count((array)$result) > 0 ? true : false,
                'message' => count((array)$result) > 0 ? 'Berhasil' : 'Gagal',
                'data'    => (array)$result,
            ],
            count((array)$result) > 0 ? 200 : 401
        );
    }


    public function store(VisitorRequest $request)
    {
        $visitor = Visitors::create(
            [
                'user_id' => $request->bengkel_id,
                'user_id_visitor' => JWTAuth::user()->id,
            ]
        );
        return response()->json(
            [
                'success' => $visitor != null ? true : false,
                'message' => $visitor != null ? 'Berhasil disimpan' : 'Gagal disimpan',
                'data'    => $visitor,
            ],
            $visitor != null ? 200 : 401
        );
    }


    protected function addDay($date, $day)
    {
        return date('Y-m-d', strtotime($date . " + $day days"));
    }


    protected function reduceDay($day)
    {
        $date = date('Y-m-d');
        return date('Y-m-d', strtotime($date . " - $day days"));
    }


    protected function listDayOfWeek()
    {
        $weekday = [];
        for ($i = 1; $i < 5; $i++) {
            $firstday   = date('Y-m-d', strtotime("sunday -$i week"));
            $lastday    = $this->addDay($firstday, 6);

            $weekday = array_merge($weekday, [[
                'firstday'  => $firstday,
                'lastday'   => $lastday,
            ]]);
        }
        return $weekday;
    }
}
