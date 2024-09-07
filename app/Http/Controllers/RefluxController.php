<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Chart;
use App\Models\ChartStat;
use App\Models\Song;
use App\Models\User;

class RefluxController extends Controller {
    public function grade($score, $max) {
        $percent = $score / $max;
        if ($percent < 2 / 9) return "F";
        if ($percent < 3 / 9) return "E";
        if ($percent < 4 / 9) return "D";
        if ($percent < 5 / 9) return "C";
        if ($percent < 6 / 9) return "B";
        if ($percent < 7 / 9) return "A";
        if ($percent < 8 / 9) return "AA";
        if ($percent < 9 / 9) return "AAA";
    }

    public function gradediff($score, $max) {
        $percent = $score / $max;
        if ($percent < 2 / 9) {
            if ($percent < 2 / 18) {
                return "F+" . $score;
            } else {
                return "E-" . ceil($max * 2 / 9 - $score);
            }
        }
        if ($percent < 3 / 9) {
            if ($percent < 5 / 18) {
                return "E+" . ceil($score - $max * 2 / 9);
            } else {
                return "D-" . ceil($max * 3 / 9 - $score);
            }
        }
        if ($percent < 4 / 9) {
            if ($percent < 7 / 18) {
                return "D+" . ceil($score - $max * 3 / 9);
            } else {
                return "C-" . ceil($max * 4 / 9 - $score);
            }
        }
        if ($percent < 5 / 9) {
            if ($percent < 9 / 18) {
                return "C+" . ceil($score - $max * 4 / 9);
            } else {
                return "B-" . ceil($max * 5 / 9 - $score);
            }
        }
        if ($percent < 6 / 9) {
            if ($percent < 11 / 18) {
                return "B+" . ceil($score - $max * 5 / 9);
            } else {
                return "A-" . ceil($max * 6 / 9 - $score);
            }
        }
        if ($percent < 7 / 9) {
            if ($percent < 13 / 18) {
                return "A+" . ceil($score - $max * 6 / 9);
            } else {
                return "AA-" . ceil($max * 7 / 9 - $score);
            }
        }
        if ($percent < 8 / 9) {
            if ($percent < 15 / 18) {
                return "AA+" . ceil($score - $max * 7 / 9);
            } else {
                return "AAA-" . ceil($max * 8 / 9 - $score);
            }
        } else {
            if ($percent < 17 / 18) {
                return "AAA+" . ceil($score - $max * 8 / 9);
            } else if ($score < $max) {
                return "MAX-" . ceil($max - $score);
            } else if ($score === $max) {
                return "MAX+0";
            }
        }
    }

    public function songplayed(Request $request) {
        return response('This API is not supported yet.', 400);
    }

    public function addsong(Request $request) {
        if (Song::where('iidx_id', $request->songid)->exists()) {
            // TODO: update unlock type
            return response('The song has already been added.');
        } else {
            $song = new Song;

            $song->iidx_id = $request->songid;
            $song->title = $request->title;
            $song->title2 = $request->title2;
            $song->genre = $request->genre;
            $song->artist = $request->artist;
            $song->bpm = $request->bpm;
            $song->unlocktype = $request->unlockType;
            $song->save();

            return response('The song has been added.');
        }
    }

    public function addchart(Request $request) {
        if (Chart::where('song_id', $request->song_id, 'difficulty', $request->difficulty)->exists()) {
            // TODO: update level
            return response('The chart has already been added.');
        } else {
            $chart = new Chart;
            $chart->song_id = $request->songid;
            $chart->difficulty = $request->diff;
            $chart->level = $request->level;
            $chart->notecount = $request->notecount;
            $chart->save();

            return response('The chart has been added.');
        }
    }

    public function postscore(Request $request) {
        $user = User::where('apikey', $request->apikey)->first();
        if (!$user) {
            return response('The user was not found.', 400);
        }

        $chart = Chart::where('song_id', $request->songid, 'difficulty', $request->diff)->first();
        if (!$chart) {
            return response('The chart was not found.', 400);
        }

        $chartstat = ChartStat::where('chart_id', $chart->id, 'user_id', $request->user_id)->first();
        if (!$chartstat) {
            $chartstat = new ChartStat;
            $chartstat->chart_id = $chart->chart_id;
            $chartstat->user_id = $user->id;
        }
        $chartstat->ex_score = (int) $request->exscore;
        $chartstat->miss = (int) $request->misscount;
        $chartstat->percent_max = $chartstat->ex_score / ($chart->notecount * 2.0);
        $chartstat->grade = RefluxController::grade($chartstat->ex_score, $chart->notecount * 2.0);
        $chartstat->gradediff = RefluxController::gradediff($chartstat->ex_score, $chart->notecount * 2.0);
        $chartstat->lamp = $request->lamp;
        $chartstat->save();

        return response('The score has been posted.');
    }

    public function updateEntries(Request $request) {
        return response('This API is not supported yet.', 400);
    }

    public function genStats(Request $request) {
        return response('This API is not supported yet.', 400);
    }

    public function updatesong(Request $request) {
        return response('This API is not supported yet.', 400);
    }

    public function unlocksong(Request $request) {
        return response('This API is not supported yet.', 400);
    }
}
