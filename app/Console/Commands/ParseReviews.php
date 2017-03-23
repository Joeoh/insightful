<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Insightful\Campaign;
use Insightful\Keyword;
use Insightful\Parser\ReviewParser;
use Insightful\Sentence;

class ParseReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse stored reviews, send to Cognitive Services API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        ReviewParser::parseReviewsToSentences();


        $sentences = Sentence::all()->where('parsed',0)->take(1000);

        $res = ReviewParser::parseSentences($sentences);

        $sentiments = $res['sentiment'];
        $keyPhrases = $res['phrases'];

        if(sizeof($sentiments) == 0 ) die("Nothing to do\n");


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
                    echo "thanks microsoft\n";
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
