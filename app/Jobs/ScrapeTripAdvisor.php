<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Insightful\Campaign;
use Insightful\Keyword;
use Insightful\Parser\ReviewParser;
use Insightful\Review;
use Insightful\Scraper\TripAdvisorReviewScraper;

use Insightful\Sentence;

class ScrapeTripAdvisor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $start;
    protected $end;

    /**
     * Create a new job instance.
     *
     * @param Campaign  $campaign
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(Campaign $campaign, \DateTime $start, \DateTime $end)
    {
        $this->campaign = $campaign;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @internal param Campaign $campaign
     */
    public function handle()
    {
        $campaign = $this->campaign;


        if ($campaign->tripadvisor_slug != "") {
            $tripadvisorScraper = new TripAdvisorReviewScraper($campaign->tripadvisor_slug);
            $reviews = $tripadvisorScraper->getReviewsBetween($this->start, $this->end);

            foreach ($reviews as $review) {
                $storeReview = new Review;
                $storeReview->author = $review->author;
                $storeReview->date = $review->date;
                $storeReview->text = $review->text;
                $storeReview->rating = $review->rating;
                $storeReview->num_words = $review->num_words;
                $storeReview->campaign_id = $campaign->id;
                $storeReview->source_id = $review->source_id;
                $storeReview->save();
            }
        }
        ReviewParser::parseReviewsToSentences();


        $sentences = DB::table('sentences')->select('sentences.id','sentences.text')->join("reviews","sentences.review_id","=","reviews.id")->where("reviews.campaign_id", $campaign->id)->where("sentences.parsed",0)->get();

        if(sizeof($sentences) == 0) return;

        $res = ReviewParser::parseSentences($sentences);

        $sentiments = $res['sentiment'];
        $keyPhrases = $res['phrases'];

        if(sizeof($sentiments) == 0 ) return;//("Nothing to do\n");


        foreach ($sentiments as $sentiment) {
            $id = $sentiment['id'];
            $score = $sentiment['score'];

            $sentence = Sentence::findOrFail($id);
            $sentence->sentiment = $score;
            $sentence->parsed = true;
            $sentence->save();

        }


        foreach ($keyPhrases as $keyPhrase) {
            $sentenceId = $keyPhrase['id'];

            foreach ($keyPhrase['keyPhrases'] as $word){
                if ($word == ""){
                    break;
                }
                $keyword = new Keyword;
                $keyword->word = $word;
                $keyword->sentence_id = $sentenceId;
                $keyword->save();
            }
        }


    }
}
