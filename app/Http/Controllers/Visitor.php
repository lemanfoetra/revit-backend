<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seeker\VisitorRequest;
use App\Models\Visitors;
use DateTime;
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

        $result = DB::table('visitors')
            ->select(DB::raw("
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day0 00:00:00' AND '$day0 23:59:59' ) AS '$day0',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day1 00:00:00' AND '$day1 23:59:59' ) AS '$day1',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day2 00:00:00' AND '$day2 23:59:59' ) AS '$day2',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day3 00:00:00' AND '$day3 23:59:59' ) AS '$day3',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day4 00:00:00' AND '$day4 23:59:59' ) AS '$day4',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day5 00:00:00' AND '$day5 23:59:59' ) AS '$day5',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '$day6 00:00:00' AND '$day6 23:59:59' ) AS '$day6' 
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

        $result = DB::table('visitors')
            ->select(DB::raw("
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[0]['firstday'] . " 00:00:00' AND '" . $listDay[0]['lastday'] . " 23:59:59' ) AS 'Minggu Sekarang',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[1]['firstday'] . " 00:00:00' AND '" . $listDay[1]['lastday'] . " 23:59:59' ) AS 'Minggu Sebelumnya',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[2]['firstday'] . " 00:00:00' AND '" . $listDay[2]['lastday'] . " 23:59:59' ) AS 'Minggu -2',
                (SELECT COUNT(id) FROM visitors WHERE user_id = '$user_id' AND created_at BETWEEN '" . $listDay[3]['firstday'] . " 00:00:00' AND '" . $listDay[3]['lastday'] . " 23:59:59' ) AS 'Minggu -3'
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
