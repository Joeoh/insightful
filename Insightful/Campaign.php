<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/02/2017
 * Time: 12:16
 */

namespace Insightful;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Campaign extends Model
{

    public function user()
    {
        $this->belongsTo('App\User');
    }

    public function reviews()
    {
        return $this->hasMany('Insightful\Review');
    }


    public function getDateOfLastReviewStoredForSource(int $sourceId)
    {
        $last = $this->reviews()->orderBy('date', 'desc')->where('source_id', $sourceId)->first();
        if ($last == null) {
            //Set date as last 20 years
            return Carbon::now()->subYear(20);
        } else {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $last->date);

            return $date;
        }
    }

    public function getDateOfLastReviewStored()
    {
        $last = $this->reviews()->orderBy('date', 'desc')->first();
        if ($last == null) {
            //Set date as last 20 years
            return Carbon::now()->subYear(20);
        } else {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $last->date);

            return $date;
        }
    }

    //Returns the date of the first review for the campaign
    public function getDateOfFirstReview()
    {
        $last = $this->reviews()->orderBy('date', 'asc')->first();
        if ($last == null) {
            //Set date as last 20 years
            return null;
        } else {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $last->date);

            return $date;
        }
    }

    public function getNumberOfReviews()
    {
        return $this->reviews()->count();
    }


    /**
     *
     * @param int $numReviews
     *
     * Return collection of chronologically latest reviews
     */
    public function getLastReviews(int $numReviews)
    {
        $reviews = $this->reviews()->orderBy('date','desc')->limit($numReviews)->get();

        return $reviews;
    }


    /*
     * Return collection of reviews in a time period
     * */
    public function getReviewsForPeriod(Carbon $start, Carbon $end){
        return $this->reviews()->where('date','>=', $start)->where('date','<=',$end)->get();

    }

    /*
     * Returns keywords with their average sentiment from $start to $end
     *
     * */
    public function getKeywordsWithSentimentForPeriod(Carbon $start, Carbon $end)
    {
        /*
         * select avg(sentiment) as average_sentement, review_keywords.word, count(review_keywords.word) as count from `review_keywords`
         * JOIN sentences on (review_keywords.sentence_id = sentences.id)
         * JOIN reviews on (sentences.review_id = reviews.id) where `reviews`.`campaign_id` = 3
         *  GROUP BY review_keywords.word
         *  ORDER BY count desc;
         * */

        $keywordSentiment = DB::table('review_keywords')
            ->select(DB::raw('ROUND(AVG(`sentences`.`sentiment`) * 100,2) as average_sentiment,review_keywords.word, count(review_keywords.word) as count'))
            ->join('sentences', 'review_keywords.sentence_id', 'sentences.id')
            ->join('reviews', 'sentences.review_id', 'reviews.id')
            ->where('campaign_id', $this->id)
            ->where('reviews.date', '>=', $start)
            ->where('reviews.date', '<=', $end)
            ->groupBy('review_keywords.word')
            ->orderBy('count', 'desc')
            ->get();

        return $keywordSentiment;

    }


    public function getAverageSentimentForPeriod(Carbon $start, Carbon $end) : float
    {

        //Average sentiment for all reviews
        /*  select AVG(`sentences`.`sentiment`) as average_sentement from `reviews`
            JOIN `sentences` on (`reviews`.`id` = `sentences`.`review_id`)  where `reviews`.`campaign_id` = 1;
         * */
        $sentimentLastTwoWeeks = DB::table('reviews')
            ->select(DB::raw('ROUND(AVG(`sentences`.`sentiment`) * 100, 2) as average_sentiment'))
            ->join('sentences', 'reviews.id', 'sentences.review_id')
            ->where('campaign_id', $this->id)
            ->where('reviews.date', '>=', $start)
            ->where('reviews.date', '<=', $end)
            ->get()->first();

        if ($sentimentLastTwoWeeks->average_sentiment == null) {
            return -1.0;
        }

        return $sentimentLastTwoWeeks->average_sentiment;
    }

    public function getKeywords()
    {
        /*
         *
         * select `review_keywords`.`word`, count(`review_keywords`.`word`) as `count` from `reviews` JOIN `sentences` on (`reviews`.`id` = `sentences`.`review_id`)
         *  JOIN `review_keywords` on (`review_keywords`.`sentence_id` = `sentences`.`id`)
         * where `reviews`.`campaign_id` = 1 GROUP BY review_keywords.word ORDER BY `count` DESC;
         * */

        return DB::table('reviews')
            ->select(DB::raw('review_keywords.word as word, count(review_keywords.word) as count'))
            ->join('sentences', 'reviews.id', 'sentences.review_id')
            ->join('review_keywords', 'review_keywords.sentence_id', 'sentences.id')
            ->where('campaign_id', $this->id)
            ->having('count','>', 3)
            ->groupBy('review_keywords.word')
            ->orderBy('count', 'desc')
            ->get();
    }


    //Returns array of sentiment for period
    //Starts at beginning of the $start week and ends of $end week
    public function sentimentForPeriod(Carbon $start, Carbon $end)
    {

        $curWeekStart = $start->startOfWeek();
        $end = $end->endOfWeek();


        $weeks = [];

        while ($curWeekStart->lessThanOrEqualTo($end)) {
            $endOfCurWeek = $curWeekStart->copy()->endOfWeek();
            $weeks[] = [
                "startDate" => $curWeekStart->toDateString(),
                "endDate"   => $endOfCurWeek->toDateString(),
                "sentiment" => $this->getAverageSentimentForPeriod($curWeekStart, $endOfCurWeek)
            ];
            $curWeekStart = $curWeekStart->addWeek(1);
        }

        return $weeks;
    }


    public function getReviewsWithKeywordInPeriod($keyword, Carbon $start, Carbon $end){
        return $this->reviews()->where('text', 'LIKE', '%'.$keyword.'%')->where('date', '>=', $start)->where('date', '<=', $end)->get();
    }
}