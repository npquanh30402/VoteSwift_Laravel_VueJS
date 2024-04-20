<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class Vote extends Model
{
    use HasFactory;

    protected $table = 'votes';

    public static function getQuestionResults($questions)
    {
        $nestedResults = [];

        foreach ($questions as $question) {
            $results = DB::table('votes')
                ->join('candidates', 'votes.candidate_id', '=', 'candidates.id')
                ->where('candidates.question_id', $question->id)
                ->select('candidates.candidate_title', DB::raw('COUNT(*) as vote_count'))
                ->groupBy('candidates.id', 'candidates.candidate_title')
                ->get();

            $candidates = $results->map(function ($result) {
                $result->candidate_title = Crypt::decryptString($result->candidate_title);
                return $result;
            });

            $questionArray = [
                'question_title' => Crypt::decryptString($question->question_title),
//                'question_description' => Crypt::decryptString($question->question_description),
                'candidates' => $candidates->pluck('candidate_title')->toArray(),
                'vote_counts' => $candidates->pluck('vote_count')->toArray(),
            ];

            $nestedResults[] = $questionArray;
        }

//        dd($nestedResults);

        return $nestedResults;
    }

    public static function collectUserVoteIds($questions, $room)
    {
        $user_has_voted_ids = collect();

        if (!$room->settings->allow_anonymous_voting) {
            $user_ids = [];

            foreach ($questions as $question) {
                $result_users = DB::table('votes')
                    ->join('candidates', 'votes.candidate_id', '=', 'candidates.id')
                    ->where('candidates.question_id', $question->id)
                    ->select('votes.user_id')
                    ->groupBy('votes.user_id')
                    ->get();

                foreach ($result_users as $result_user) {
                    $user_ids[] = User::find($result_user->user_id);
                }
            }

            $user_has_voted_ids = collect($user_ids)->unique();
        }

        return $user_has_voted_ids;
    }

    public static function getUserVotes($user, $question)
    {
        return DB::table('votes')
            ->join('candidates', 'votes.candidate_id', 'candidates.id')
            ->where('votes.user_id', $user->id)
            ->where('candidates.question_id', $question->id)
            ->select('candidates.id as candidate_id', 'candidates.candidate_title')
            ->get();
    }

    public static function calculateVoteCountsInTimeInterval(VotingRoom $room, $timeInterval = '1', $timeUnit = 'day')
    {
        $startTime = Carbon::parse($room->start_time);
        $endTime = Carbon::parse($room->end_time);

        $timeRange = $startTime->copy()->range($endTime, $timeInterval, $timeUnit);

        $times = [];
        $voteCounts = [];

        foreach ($timeRange as $dateTime) {
            $startOfInterval = $dateTime;
            $endOfInterval = $dateTime->copy()->add($timeUnit, $timeInterval);

            $voteCount = Vote::whereBetween('created_at', [$startOfInterval, $endOfInterval])->count();

            if ($voteCount === 0) {
                continue;
            }

            $times[] = $startOfInterval->format('M d');
            $voteCounts[] = $voteCount;
        }

        return [$times, $voteCounts];
    }

    public function room()
    {
        return $this->belongsTo(VotingRoom::class, 'voting_room_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'id');
    }
}
