<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\EventsDate;

class EventsController extends Controller
{
    public function events (Request $request)
    {
        $dtFr = date('Y-m-01', strtotime($request->input('date_from', date('Y-m-d'))));
        $dtTo = date('Y-m-t', strtotime($request->input('date_to', date('Y-m-d'))));

        $query = EventsDate::with('events')->whereRaw("date >= ? AND date <= ?", [$dtFr, $dtTo])->get();
        $query = collect($query);

        $period = static::dates($dtFr, $dtTo);
        $dates = [];
        foreach ($period as $p) {
            $d = $p->format('Y-m-d');
            $e = $query->firstWhere('date', $d);
            $dates[] = [
                'date' => $d,
                'monthyear' => $p->format('Ym01'),
                'events' => (!empty($e)) ? $e['events']['event'] : null
            ];
        }

        $dates = collect($dates)->groupBy('monthyear')->all();
        return $dates;
    }

    public function save (Request $request)
    {
        $dtFr = date('Y-m-d', strtotime($request->input('date_from')));
        $dtTo = date('Y-m-d', strtotime($request->input('date_to')));
        $days = $request->input('days');
        $event = $request->input('event');
        
        $query = Events::updateOrCreate(['event' => $event]);
        $id = $query->id;

        $dates = [];
        $period = static::dates($dtFr, $dtTo);
        foreach ($period as $date) {
            $n = $date->format('N');
            if (in_array($n, $days)) $dates[] = [
                'events_id' => $id,
                'date' => $date->format('Y-m-d')
            ];
        }

        $query2 = EventsDate::whereRaw("date >= ? AND date <= ?", [$dtFr, $dtTo])->delete();
        $query3 = EventsDate::insert($dates);

        return $dates;
    }

    private static function dates ($from, $to)
    {
        return new \DatePeriod(
            date_create($from),
            date_interval_create_from_date_string('1 day'),
            date_create(date('Y-m-d', strtotime($to.' +1 day')))
        );
    }
}
